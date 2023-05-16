<?php

namespace App\Validator;

use App\Service\ValidatorService;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;


class FluxRssValidator
{
  public function  __construct(
    private readonly ValidatorService $validatorService
  ) {
  }
  public function validFluxRss(array $data)
  {
    $validator = Validation::createValidator();

    $constraint = new Assert\Collection([
      'fields' => [
        'url' => [
          new Assert\Regex([
            'pattern' => "/(?:http[s]?:\/\/.)?(?:www\.)?[-a-zA-Z0-9@%._\+~#=]{2,256}\.[a-z]{2,6}\b(?:[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)/",
            'message' => "Votre Url n'est pas valide !"
          ]),
          new Assert\NotBlank([
            'message' => 'Vous devez renseigner un flux RSS !'
          ]),
          new Assert\Type([
            'type' => 'string',
            'message' => "L'Url doit être une chaîne de caractère "
          ]),
        ],
        'categoryId' => [
          new Assert\NotBlank([
            'message' => 'Vous devez renseigner un Id de catégorie !'
          ]),
          new Assert\Type([
            'type' => 'int',
            'message' => "L'ID de la catégorie doit être une chaîne de caractère "
          ]),
        ],
        'description' => [
          new Assert\NotBlank([
            'message' => 'Vous devez renseigner une description !'
          ]),
          new Assert\Type([
            'type' => 'string',
            'message' => "La description de la catégorie doit être une chaîne de caractère "
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
