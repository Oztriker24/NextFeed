<?php

namespace App\Service;

use Exception;
use App\Entity\FluxRss;
use App\Validator\FluxRssValidator;
use App\Repository\FluxRssRepository;
use App\Repository\CategoryRepository;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class FluxRssService
{
  public function __construct(
    private readonly FluxRssRepository $fluxRssRepository,
    private readonly FluxRssValidator $fluxRssValidator,
    private readonly SerializerInterface $serializer,
    private readonly CategoryRepository $categoryRepository,
    private readonly FindDuplicate $findDuplicate,
    private readonly JsonValidService $jsonValidService,
    private readonly ExistDataService $existDataService,
    private readonly PaginatorInterface $paginatorInterface
  ) {
  }

  public function getAll(): JsonResponse
  {
    $fluxRss = $this->fluxRssRepository->findAll();
    if (count($fluxRss) == 0) {
      return new JsonResponse('Aucun flux RSS enregistrés', Response::HTTP_OK);
    }
    return new JsonResponse(json_decode($this->serializer->serialize($fluxRss, 'json')), Response::HTTP_OK);
  }

  public function getOne(int $id): JsonResponse
  {
    $fluxRss = $this->fluxRssRepository->findOneBy(array('id' => $id));

    if ($fluxRss == null) {
      return new JsonResponse('Aucun FluxRss trouvé !', Response::HTTP_NOT_FOUND);
    }

    return new JsonResponse(json_decode($this->serializer->serialize($fluxRss, 'json')), Response::HTTP_ACCEPTED);
  }

  public function add($data): JsonResponse
  {
    try {
      $this->jsonValidService->validJson($data, "url");
      $this->fluxRssValidator->validFluxRss($data);
      $category = $this->existDataService->ExistData($data["categoryId"], "id", $this->categoryRepository, "Cette catégorie n'existe pas !", "categoryId");
      $this->findDuplicate->findDuplicateData($data, "url", $this->fluxRssRepository, "Ce flux Rss existe déjà !", "");
    } catch (Exception $e) {
      return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
    }

    $flux = new FluxRss();
    $flux->setCategory($category);
    $flux->setDescription($data["description"]);
    $flux = $this->serializer->fromArray(array_merge($this->serializer->toArray($flux), $data), FluxRss::class);

    $this->fluxRssRepository->save($flux, true);


    return new JsonResponse('Flux Rss enregistré !', Response::HTTP_CREATED);
  }

  public function update($data, $id): JsonResponse
  {
    $flux = $this->fluxRssRepository->findOneBy(['id' => $id]);
    if ($flux == null) {
      return new JsonResponse('Aucun Flux RSS trouvé ', Response::HTTP_NOT_FOUND);
    }
    $fluxRssName = $flux->getUrl();
    
    try {
      $this->jsonValidService->validJson($data, "url");
      $this->fluxRssValidator->validFluxRss($data);
      $category = $this->existDataService->ExistData($data["categoryId"], "id", $this->categoryRepository, "Cette catégorie n'existe pas !", "fluxRss");
      $this->findDuplicate->findDuplicateData($data, "url", $this->fluxRssRepository, "Ce flux Rss existe déjà !", $fluxRssName);
    } catch (Exception $e) {
      return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
    }

    $flux->setCategory($category);
    $fluxUpdate = $this->serializer->fromArray(array_merge($this->serializer->toArray($flux), $data), FluxRss::class);

    $this->fluxRssRepository->save($fluxUpdate, true);

    return new JsonResponse(json_decode($this->serializer->serialize($fluxUpdate, 'json')), Response::HTTP_ACCEPTED);
  }

  public function delete($id): JsonResponse
  {

    $flux = $this->fluxRssRepository->find($id);

    if ($flux == null) {
      return new JsonResponse('[Aucun flux RSS trouvé', Response::HTTP_BAD_REQUEST);
    }
    $this->fluxRssRepository->remove($flux, true);

    return new JsonResponse('Supression réussie !', Response::HTTP_ACCEPTED);
  }
  public function getFluxRssPage($id): JsonResponse
  {
    $fluxRssQuery = $this->fluxRssRepository->PaginatedFluxRsses();
    $fluxRssPaginated = $this->paginatorInterface->paginate($fluxRssQuery, $id, 10);

    return new JsonResponse([
      "items" => json_decode($this->serializer->serialize($fluxRssPaginated->getItems(), 'json')),
      "itemsNumberPerPage" => $fluxRssPaginated->getItemNumberPerPage(),
      "currentPageNumber" => $fluxRssPaginated->getCurrentPageNumber(),
      "totalCount" => $fluxRssPaginated->getTotalItemCount(),
    ], Response::HTTP_OK);
  }
}
