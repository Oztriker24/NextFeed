<?php

namespace App\Validator;

use App\Service\ValidatorService;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryValidator
{
    public function  __construct(
        private readonly ValidatorService $validatorService
    )
    {}
    public function validCategory(array $data)
    {
        $validator = Validation::createValidator();

        $constraint = new Assert\Collection([
            'fields' => [
                'name' => [
                    new Assert\Type([
                        'type' => 'string',
                        'message' => "Votre catégorie doit être une chaîne de caractère "
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Cette valeur doit faire plus de 2 caractères !',
                        'maxMessage' => 'Cette valeur doit faire moins de 25 caractères !'
                    ]),
                    new Assert\NotBlank([
                        'message' => 'Vous devez rentrer une catégorie'
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
