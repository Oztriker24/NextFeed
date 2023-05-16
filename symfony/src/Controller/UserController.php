<?php

namespace App\Controller;

use App\Service\UserService;
use App\Service\AccessService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly AccessService $accessService,
    ) {
    }

    #[Route('/', name: 'get_all', methods: 'GET')]
    public function getAllUsers(Request $request): JsonResponse
    {
        $jwtToken = substr($request->headers->get('Authorization'), 7);
        if (!$this->accessService->isUser($jwtToken)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Accès refusé !'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->userService->getAll();
    }
    /**
     * @throws JsonException
     */
    #[Route('/profile', name: 'profiles', methods: 'GET')]
    public function getInfo(Request $request): JsonResponse
    {
        
        $jwtToken = substr($request->headers->get('Authorization'), 7);

        $decodeTokenEmail = $this->accessService->decodeToken($jwtToken);

        return $this->userService->profile($decodeTokenEmail);
    }

    #[Route('/{id}', name: 'get_one', methods: 'GET')]
    public function getOneUser(int $id): JsonResponse
    {
        return $this->userService->getOne($id);
    }
    /**
     * @throws JsonException
     */
    #[Route('/add', name: 'add', methods: 'POST')]
    public function addUser(Request $request)
    {
        // $jwtToken = substr($request->headers->get('Authorization'), 7);

        // if(!$this->accessService->isAdmin($jwtToken)){
        //     return new JsonResponse([
        //         'code' => 401,
        //         'message' =>'Accès refusé !'], Response::HTTP_UNAUTHORIZED);
        // }

        $req = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);


        return $this->userService->add($req);
    }
    /**
     * @throws JsonException
     */
    #[Route('/update/{id}', name: 'update', methods: 'PATCH')]
    public function updateUser(Request $request, int $id): JsonResponse
    {
        $jwtToken = substr($request->headers->get('Authorization'), 7);

        if (!$this->accessService->isAdmin($jwtToken)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Accès refusé !'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $req = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        return $this->userService->update($req, $id);
    }
    
    #[Route('/updateStatus/{id}', name: 'update', methods: 'PATCH')]
    public function updateUserStatus(Request $request, int $id): JsonResponse
    {
        $jwtToken = substr($request->headers->get('Authorization'), 7);

        if (!$this->accessService->isAdmin($jwtToken)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Accès refusé !'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $req = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        return $this->userService->updateStatus($req, $id);
    }


    #[Route('/delete/{id}', name: 'delete', methods: 'DELETE')]
    public function deleteUser(Request $request, int $id): JsonResponse
    {
        $jwtToken = substr($request->headers->get('Authorization'), 7);

        if (!$this->accessService->isAdmin($jwtToken)) {
            return new JsonResponse([
                'code' => 401,
                'message' => 'Accès refusé !'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->userService->delete($id);
    }

    #[Route('/login', name: 'login', methods: 'POST')]
    public function loginUser(Request $request)
    {
        $req = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $this->userService->login($req);
    }
    #[Route('/paginate/{id}', name: 'paginate' , methods : 'GET')]
    public function userPaginate(Request $request, int $id) 
    {
      $jwtToken = substr($request->headers->get('Authorization'), 7);

      if(!$this->accessService->isAdmin($jwtToken)){
          return new JsonResponse([
              'code' => 401,
              'message' =>'Accès refusé !'], Response::HTTP_BAD_REQUEST);
      }
      
      return $this->userService->getUserPage($id); 
    }
}
