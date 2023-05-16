<?php

namespace App\Service;

use Exception;

class ValidatorService
{
  public function returnViolations($data)
  {
    foreach ($data as $message) {
      $messages['errors'][] = [
        'property' => $message->getPropertyPath(),
        'value' => $message->getInvalidValue(),
        'message' => $message->getMessage(),
      ];
    }

    if (count($messages['errors']) > 0) {
      return throw new Exception($message->getPropertyPath(). " => ".$message->getMessage() );
    }
    return false;
  }
}
