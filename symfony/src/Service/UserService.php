<?php

namespace App\Service;

use Exception;
use App\Entity\User;
use App\Validator\UserValidator;
use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserValidator $userValidator,
        private readonly SerializerInterface  $serializer,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly FindDuplicate $findDuplicate,
        private readonly JsonValidService $jsonValidService,
        private readonly ExistDataService $existDataService,
        private readonly PaginatorInterface $paginatorInterface
    ) {
    }

    public function getAll(): JsonResponse
    {

        $users = $this->userRepository->findAll();

        if (count($users) == 0) {
            return new JsonResponse('Aucun utilisateurs enregistrés',  Response::HTTP_OK);
        }
        return new JsonResponse(json_decode($this->serializer->serialize($users, 'json')),  Response::HTTP_OK);
    }

    public function getOne(int $id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(array('id' => $id));

        if ($user == null) {
            return new JsonResponse('Aucun utilisateur trouvé !', Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(json_decode($this->serializer->serialize($user, 'json')),  Response::HTTP_OK);
    }

    public function add($data): JsonResponse
    {
        try {
            $this->jsonValidService->validJson($data, "email");
            $this->userValidator->validUser($data);
            $this->findDuplicate->findDuplicateData($data, "email", $this->userRepository, "Cet E-mail est déjà utilisé !", "");
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setRoles($data['roles']);
        $user->setName($data["name"]);
        $user->setEmail($data["email"]);
        $user->setPassword($hashedPassword);
        $user->setIsActive($data["isActive"]);

        $this->userRepository->save($user, true);

        return new JsonResponse('Utilisateur enregistré !', Response::HTTP_CREATED);
    }

    public function update($data, $id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if ($user == null) {
            return new JsonResponse('Aucun utilisateur trouvée', Response::HTTP_BAD_REQUEST);
        }
        $userEmail = $user->getEmail();
        $data["roles"] = $user->getRoles();
        $data["password"] = "Temporary1!";

        try {
            $this->jsonValidService->validJson($data, "email");
            $this->userValidator->validUser($data);
            $this->findDuplicate->findDuplicateData($data, "email", $this->userRepository, "Cet E-mail est déjà utilisé !", $userEmail);
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        $user->setName($data['name']);
        $user->setEmail($data['email']);

        $this->userRepository->save($user, true);

        return new JsonResponse('Modification réussie !', Response::HTTP_ACCEPTED);
    }
    public function updateStatus($data, $id): JsonResponse {
      $user = $this->userRepository->findOneBy(['id' => $id]);
      if ($user == null) {
          return new JsonResponse('Aucun utilisateur trouvée', Response::HTTP_BAD_REQUEST);
      }
      
      $newStatus = $data["isActive"];

      $user->setIsActive($newStatus);

      $this->userRepository->save($user, true);

      return new JsonResponse(json_decode($this->serializer->serialize($user, 'json')),  Response::HTTP_OK);

    }
    public function delete($id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if ($user == null) {
            return new JsonResponse('Aucun utilisateur trouvé', Response::HTTP_BAD_REQUEST);
        }

        $this->userRepository->remove($user, true);

        return new JsonResponse('Supression réussie !', Response::HTTP_ACCEPTED);
    }

    public function login($data)
    {
        $user = $this->userRepository->findBy(["email" => $data['email']]);

        if ($user == null || !$this->passwordHasher->isPasswordValid($user[0], $data['password'])) {
            return new JsonResponse('Adresse E-mail ou mot de passe invalide', Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse('Ok', Response::HTTP_BAD_REQUEST);
    }
    public function profile($email)
    {

        $user = $this->userRepository->findBy(["email" => $email])[0];
        $userInfo = [
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "name" => $user->getName(),
            "roles" => $user->getRoles(),
            "created_at" => $user->getCreatedAt(),
        ];

         return new JsonResponse(json_decode($this->serializer->serialize($userInfo, 'json')), Response::HTTP_ACCEPTED);
      
    }
    public function getUserPage($id):JsonResponse 
    {
      $userQuery = $this->userRepository->paginatedUsers();
      $userPaginated = $this->paginatorInterface->paginate($userQuery, $id , 10);

      return new JsonResponse([
        "items" =>json_decode($this->serializer->serialize($userPaginated->getItems(), 'json')),
        "itemsNumberPerPage" => $userPaginated->getItemNumberPerPage(),
        "currentPageNumber" => $userPaginated->getCurrentPageNumber(),
        "totalCount" => $userPaginated->getTotalItemCount(),
      ], Response::HTTP_OK);
    }
}
