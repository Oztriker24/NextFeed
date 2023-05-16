<?php

namespace App\Service;

use Exception;
use DateTimeImmutable;
use App\Entity\Category;
use App\Validator\CategoryValidator;
use App\Repository\CategoryRepository;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly SerializerInterface  $serializer,
        private readonly CategoryValidator $categoryValidator,
        private readonly FindDuplicate $findDuplicate,
        private readonly JsonValidService $jsonValidService,
        private readonly PaginatorInterface $paginatorInterface
    ) {
    }

    public function getAll(): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();

        if (count($categories) == 0) {
            return new JsonResponse('Aucune catégories enregistrées', Response::HTTP_OK);
        }

        return new JsonResponse(json_decode($this->serializer->serialize($categories, 'json')),  Response::HTTP_OK);
    }

    public function getOne($id): JsonResponse
    {
        $category = $this->categoryRepository->findOneBy(array('id' => $id));

        if ($category == null) {
            return new JsonResponse('Aucune catégorie trouvée', Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(json_decode($this->serializer->serialize($category, 'json')), Response::HTTP_OK);
    }

    public function add($data): JsonResponse
    {
        try {
            $this->jsonValidService->validJson($data, "name" );
            $this->findDuplicate->findDuplicateData($data, "name", $this->categoryRepository, "Cette catégorie existe déjà !", "");
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        $this->categoryValidator->validCategory($data);

        $category = new Category();
        $category = $this->serializer->fromArray(array_merge($this->serializer->toArray($category), $data), Category::class);

        $this->categoryRepository->save($category, true);

        return new JsonResponse(json_decode($this->serializer->serialize($category, 'json'), Response::HTTP_CREATED));
    }

    public function update($data, $id): JsonResponse
    {
       
        $category = $this->categoryRepository->findOneBy(['id' => $id]);
        if ($category == null) {
            return new JsonResponse('Aucune catégorie trouvée', Response::HTTP_NOT_FOUND);
        }
        $categoryName = $category->getName();    
        try {
            $this->jsonValidService->validJson($data, "name");
            $this->findDuplicate->findDuplicateData($data, "name", $this->categoryRepository, "Cette catégorie existe déjà !", $categoryName);
        } catch(Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        $this->categoryValidator->validCategory($data);

        $category->setName($data['name']);
        $category->setUpdatedAt(new DateTimeImmutable('now'));

        $this->categoryRepository->save($category, true);

        return new JsonResponse('Modification réussie !', Response::HTTP_ACCEPTED);
    }

    public function delete($id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);
        if ($category == null) {
            return new JsonResponse('Aucune catégorie trouvée', Response::HTTP_NOT_FOUND);
        }
        if (count($category->getFluxRsses()->toArray()) > 0) {
            return new JsonResponse('Vous ne pouvez pas supprimer une catégorie associée à un ou plusieurs flux RSS !', Response::HTTP_NOT_FOUND);
        }

        $this->categoryRepository->remove($category, true);

        return new JsonResponse('Supression réussie !', Response::HTTP_ACCEPTED);
    }
    public function getCategoryPage($id):JsonResponse 
    {
      $categoryQuery = $this->categoryRepository->PaginatedCategories();
      $categoryPaginated = $this->paginatorInterface->paginate($categoryQuery, $id , 10);

      return new JsonResponse([
        "items" =>json_decode($this->serializer->serialize($categoryPaginated->getItems(), 'json')),
        "itemsNumberPerPage" => $categoryPaginated->getItemNumberPerPage(),
        "currentPageNumber" => $categoryPaginated->getCurrentPageNumber(),
        "totalCount" => $categoryPaginated->getTotalItemCount(),
      ], Response::HTTP_OK);
    }
    public function getTwoRandomsPerCategory()
    {
      $categories = $this->categoryRepository->findAll();

      $randomsFluxRss = [];
      foreach($categories as $category) {
        $randomFluxRss = array_rand($category->getFluxRsses()->toArray(), 2);
        array_push($randomsFluxRss, $category->getFluxRsses()[$randomFluxRss[0]]);
        array_push($randomsFluxRss, $category->getFluxRsses()[$randomFluxRss[1]]);
      }
      return new JsonResponse(json_decode($this->serializer->serialize($randomsFluxRss, 'json')),  Response::HTTP_OK);
    }
}
