<?php

namespace App\Validator;

use App\Service\ValidatorService;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserValidator
{
    const ROLES = [
        ['ROLE_ADMIN'],
        [ "ROLE_USER"]
    ];
    public function  __construct(
        private readonly ValidatorService $validatorService
    )
    {}
    public function validUser(array $data)
    {
        $validator = Validation::createValidator();

        $constraint = new Assert\Collection([
            'fields' => [
                'name' => [
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom d\'utilisateur doit faire plus de 2 caractères !',
                        'maxMessage' => 'Le nom d\'utilisateur  doit faire moins de 25 caractères !'
                    ]),
                    new Assert\NotBlank([
                        'message' => 'Vous devez rentrez un nom d\'utilisateur'
                    ]),
                    new Assert\Type([
                        'type' => 'string',
                        'message' => "Le nom d'utilisateur doit être une chaîne de caractère "
                    ]),
                ],
                'email' => [
                    new Assert\Callback([
                        'callback' => static function ($data, ExecutionContextInterface $context) {
                            if (isset($context->getRoot()['exist']) && $context->getRoot()['exist']) {
                                $context
                                    ->buildViolation("L'utilisateur existe déjà")
                                    ->addViolation();
                            }
                        }
                    ]),
                    new Assert\NotBlank([
                        'message' => 'Vous devez rentrer une adresse e-mail !'
                    ]),
                    new Assert\Email([
                        'message' => 'Adresse email invalide !'
                    ]),
                    new Assert\Type([
                        'type' => 'string',
                        'message' => "L'email doit être une chaîne de caractère "
                    ]),
                ],
                'password' => [
                    new Assert\NotBlank([
                        'message' => 'Vous devez rentrer un mot de passe !'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/',
                        'message' => 'Votre mot de passe n\'est pas valide !'
                    ]),
                    new Assert\Type([
                        'type' => 'string',
                        'message' => "Le mot de passe doit être une chaîne de caractère "
                    ]),
                ],
                'roles' => [
                    new Assert\NotBlank([
                        'message' => 'Vous devez sélectionner au moins un rôle !'
                    ]),
                    new Assert\Type([
                        'type' => 'array',
                        'message' => "Le Rôle doit être un tableau composer d'un rôle en chaine de caractère "
                    ]), 
                    new Assert\Callback([
                        'callback' => static function ($data, ExecutionContextInterface $context) {                           
                            if ($context->getRoot()['roles'] == $_ENV['USER']) {
                                $context
                                    ->buildViolation("Rôle invalide !")
                                    ->addViolation();
                            }
                        }
                    ]),
                  ],
                  'isActive' => [
                    new Assert\NotNull([
                        'message' => 'Vous devez définir l\'état de cet utilisateur !'
                    ]),
                    new Assert\Type([
                        'type' => 'boolean',
                        'message' => "La statut doit être un de type boolean "
                    ]),
                ],
            ],
            'allowExtraFields' => true,
        ]);

        $violations = $validator->validate($data, $constraint);
        if (count($violations) != 0) {
            return $this->validatorService->returnViolations($violations);
        }
        return true;
    }
   
}
