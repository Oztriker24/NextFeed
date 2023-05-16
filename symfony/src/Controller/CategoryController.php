<?php

namespace App\Controller;

use JsonException;
use App\Service\AccessService;
use App\Service\CategoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly AccessService $accessService,
        private readonly CategoryService $categoryService,
    ) {
    }

    #[Route('/', name: 'get_all', methods: 'GET')]
    public function getAllCategories(Request $request): JsonResponse
    {
        $jwtToken = substr($request->headers->get('Authorization'), 7);

        return $this->categoryService->getAll();
    }

    #[Route('/{id}', name: 'get_one', methods: 'GET')]
    public function getOneCategory(int $id): JsonResponse
    {
        return $this->categoryService->getOne($id);
    }

    /**
     * @throws JsonException
     */
    #[Route('/add', name: 'add', methods: 'POST')]
    public function addCategory(Request $request): JsonResponse
    {
        $jwtToken = substr($request->headers->get('Authorization'), 7);

        if (!$this->accessService->isAdmin($jwtToken)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Accès refusé !'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $req = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        return $this->categoryService->add($req);
    }

    /**
     * @throws JsonException
     */
    #[Route('/update/{id}', name: 'update', methods: 'PATCH')]
    public function updateCategory(Request $request, int $id): JsonResponse
    {
        $jwtToken = substr($request->headers->get('Authorization'), 7);

        if (!$this->accessService->isAdmin($jwtToken)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Accès refusé !'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $req = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        return $this->categoryService->update($req, $id);
    }

    #[Route('/delete/{id}', name: 'delete', methods: 'DELETE')]
    public function deleteCategory(Request $request, int $id): JsonResponse
    {
        $jwtToken = substr($request->headers->get('Authorization'), 7);

        if (!$this->accessService->isAdmin($jwtToken)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Accès refusé !'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->categoryService->delete($id);
    }
    #[Route('/paginate/{id}', name: 'paginate', methods: 'GET')]
    public function categoryPaginate(Request $request, int $id)
    {
        $jwtToken = substr($request->headers->get('Authorization'), 7);

        if (!$this->accessService->isAdmin($jwtToken)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Accès refusé !'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->categoryService->getCategoryPage($id);
    }
    #[Route('/randoms/category', name: 'get_randoms', methods: 'GET')]
    public function getTwoRandoms(Request $request): JsonResponse
    {
        $jwtToken = substr($request->headers->get('Authorization'), 7);

        return $this->categoryService->getTwoRandomsPerCategory();
    }
}
