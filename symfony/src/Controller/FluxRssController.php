<?php

namespace App\Controller;

use App\Service\AccessService;
use App\Service\FluxRssService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/fluxrss', name: 'fluxRss_')]
class FluxRssController extends AbstractController
{
  public function __construct(
    private readonly FluxRssService $fluxRssService,
    private readonly AccessService $accessService
  ) {
  }

  #[Route('/', name: 'get_all', methods: 'GET')]
  public function getAllFluxRss(Request $request): JsonResponse
  {
    $jwtToken = substr($request->headers->get('Authorization'), 7);

    if (!$this->accessService->isAdmin($jwtToken)) {
      return new JsonResponse([
        'code' => 401,
        'message' => 'Accès refusé !'
      ], Response::HTTP_BAD_REQUEST);
    }

    return $this->fluxRssService->getAll();
  }

  #[Route('/{id}', name: 'get_one', methods: 'GET')]
  public function getOneFluxRss(int $id): JsonResponse
  {
    return $this->fluxRssService->getOne($id);
  }
  /**
   * @throws JsonException
   */
  #[Route('/add', name: 'add', methods: 'POST')]
  public function addFluxRss(Request $request)
  {
    $jwtToken = substr($request->headers->get('Authorization'), 7);

    if (!$this->accessService->isAdmin($jwtToken)) {
      return new JsonResponse([
        'code' => 401,
        'message' => 'Accès refusé !'
      ], Response::HTTP_UNAUTHORIZED);
    }

    $req = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
    return $this->fluxRssService->add($req);
  }
  /**
   * @throws JsonException
   */
  #[Route('/update/{id}', name: 'update', methods: 'PATCH')]
  public function updateFluxRss(Request $request, int $id): JsonResponse
  {
    $jwtToken = substr($request->headers->get('Authorization'), 7);

    if (!$this->accessService->isAdmin($jwtToken)) {
      return new JsonResponse([
        'code' => 401,
        'message' => 'Accès refusé !'
      ], Response::HTTP_UNAUTHORIZED);
    }

    $req = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
    return $this->fluxRssService->update($req, $id);
  }
  #[Route('/delete/{id}', name: 'delete', methods: 'DELETE')]
  public function deleteFluxRss(Request $request, int $id): JsonResponse
  {
    $jwtToken = substr($request->headers->get('Authorization'), 7);

    if (!$this->accessService->isAdmin($jwtToken)) {
      return new JsonResponse([
        'code' => 401,
        'message' => 'Accès refusé !'
      ], Response::HTTP_UNAUTHORIZED);
    }

    return $this->fluxRssService->delete($id);
  }
  #[Route('/paginate/{id}', name: 'paginate', methods: 'GET')]
  public function fluxRssPaginate(Request $request, int $id)
  {
    $jwtToken = substr($request->headers->get('Authorization'), 7);

    if (!$this->accessService->isAdmin($jwtToken)) {
      return new JsonResponse([
        'code' => 401,
        'message' => 'Accès refusé !'
      ], Response::HTTP_BAD_REQUEST);
    }

    return $this->fluxRssService->getFluxRssPage($id);
  }
}
