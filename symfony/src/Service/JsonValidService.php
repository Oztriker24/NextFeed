<?php

namespace App\Service;

use Exception;

class JsonValidService
{
    public function __construct(
    ) {}

    public function validJson(array $data, string $dataField) {
        if(!isset($data[$dataField])) {
           throw new Exception("Le flux JSON envoyé est incorrect");
        };
        return true;
    }

}