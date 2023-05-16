<?php

namespace App\Service;

use App\Validator\CategoryValidator;
use App\Repository\CategoryRepository;
use App\Repository\FluxRssRepository;
use App\Repository\UserRepository;
use App\Validator\FluxRssValidator;
use App\Validator\UserValidator;
use Exception;

class ExistDataService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryValidator $categoryValidator,
        private readonly FluxRssRepository $fluxRssRepository,
        private readonly FluxRssValidator $fluxRssValidator,
        private readonly UserRepository $userRepository,
        private readonly UserValidator $userValidator,
        
    ) {}

    public function ExistData($data, string $findIndex, object $repository, string $errorMessage, string $path) {

        $exist = $repository->findOneBy([$findIndex => $data]);

        if($exist === null) {
            throw new Exception ("[" . $path . "] => " . $errorMessage);
        }
        return $exist;
    }

}