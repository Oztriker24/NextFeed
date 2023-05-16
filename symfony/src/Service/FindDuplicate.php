<?php

namespace App\Service;

use JsonException;
use App\Validator\CategoryValidator;
use App\Repository\CategoryRepository;
use App\Repository\FluxRssRepository;
use App\Repository\UserRepository;
use App\Validator\FluxRssValidator;
use App\Validator\UserValidator;
use Exception;

class FindDuplicate
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryValidator $categoryValidator,
        private readonly FluxRssRepository $fluxRssRepository,
        private readonly FluxRssValidator $fluxRssValidator,
        private readonly UserRepository $userRepository,
        private readonly UserValidator $userValidator,
        
    ) {}
    public function findDuplicateData( array $data, string $findIndex, object $repository, string $errorMessage, string $entity) {
        
        
        $exist = $repository->findOneBy([$findIndex => $data[$findIndex]]);

        if($exist != null  && $data[$findIndex] != $entity ) {
            throw new Exception($errorMessage);
        } 
        return true;
    }

}