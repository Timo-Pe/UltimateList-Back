<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class APIUsersController extends AbstractController
{
    /**
     * @Route("/api/users", name="app_api_users")
     */
    public function userList(UserRepository $usersList): Response
    {
        $usersCollection = $usersList->findAll();
        //$user = $serializer->serialize($usersCollection, 'json');
       
        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $usersCollection,
            // Le status code
            200,
            // Les en-têtes de réponse à ajouter (aucune)
            [],
            ['groups' => 'get_users_collection']
        );
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="api_users_get_user", methods={"GET"})
     */
    public function getUser(User $user = null)
    {
        if ($user === null){
            return $this->json(['error' => 'User non trouvé', Response::HTTP_NOT_FOUND]);
        }
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'get_users_collection']);
    }

    /**
     * @Route("/api/users/create", name="app_api_create_users", methods={"POST"})
     */
    public function createUser(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $jsonContent = $request->getContent();

        try {
            $user = $serializer->deserialize($jsonContent, User::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsClean = [];
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };
            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $newUser = new User();

        $newUser->setUsername($user->getUsername())
                ->setPassword($user->getPassword())
                ->setEmail($user->getEmail());
                
        $roles = $user->getRoles();
      

        foreach ($roles as $role) {
            
            $newUser->setRoles($role);
            
        }
        $listItems = $user->getListItems();

        foreach ($listItems as $listItem) {
            $newUser->addListItem($listItem);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($newUser);
        $entityManager->flush();

        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $user,
            // Le status code
            Response::HTTP_CREATED,
            // Les en-têtes de réponse à ajouter (aucune)
            [
                //'Location' => $this->generateUrl('api_users_get_user', ['id' => $user->getId()])
            ],
            ['groups' => 'get_users_collection']
        );
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="api_users_delete", methods={"DELETE"})
     */

    public function deleteUser(User $user = null, ManagerRegistry $doctrine) 
    {
        // ajouter un token pour autoriser le delete
        if ($user === null) {
            return $this->json(['errors' => 'Le user ne peut être supprimé car il n\' existe pas'], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager = $doctrine->getManager();
        
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->json(Response::HTTP_OK );
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="api_users_edit", methods={"PUT"})
     */
    public function editUser(User $user,ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request)
    {
        $jsonContent = $request->getContent();
        $userEdit = $serializer->deserialize($jsonContent, User::class, 'json');

        $user->setUsername($userEdit->getUsername())
             ->setPassword($userEdit->getPassword())
             ->setEmail($userEdit->getEmail());

        $roles = $userEdit->getRoles();
        foreach ($roles as $role) {
            $user->setRoles($role);
        }

        $listItemsRemove = $user->getListItems();
        foreach ($listItemsRemove as $listItemRemove) {
            $user->removeListItem($listItemRemove);
        }

        $listItems = $userEdit->getListItems();
        foreach ($listItems as $listItem) {
            $user->addListItem($listItem);
        }
    
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'get_users_collection']);
    }
}

