<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class APIUsersController extends AbstractController
{
    /**
     * @Route("/api/users", name="app_api_users")
     * Affiche la liste des users
     * Besoin Front : (future version) recherche des users
     */
    public function userList(UserRepository $usersList, SerializerInterface $serializer): Response
    {
        $usersCollection = $usersList->findAll();

        //$user = $serializer->serialize($usersCollection,'json', ['groups' => 'get_users_collection']);

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
     * @Route("/api/users/{id<\d+>}", name="api_users_get_user_id", methods={"GET"})
     * Afficher les infos d'un utilisateur
     * Besoin Front : afficher le profil utilisateur
     */
    public function getUserById(User $user = null)
    {
        if ($user === null){
            return $this->json(['error' => 'User non trouvé', Response::HTTP_NOT_FOUND]);
        }

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'get_users_collection']);
    }

    /**
     * @Route("/api/users/{username}", name="api_users_get_user_username", methods={"GET"})
     * Afficher les infos d'un utilisateur
     * Besoin Front : afficher le profil utilisateur
     */
    public function getUserByUsername(User $user = null)
    {
        if ($user === null){
            return $this->json(['error' => 'User non trouvé', Response::HTTP_NOT_FOUND]);
        }

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'get_users_collection']);
    }

    /**
     * @Route("/api/users/create", name="app_api_create_users", methods={"POST"})
     * Créer un user
     * Besoin Front : inscription au site
     */
    public function createUser(UserPasswordHasherInterface $passwordHasher, Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
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
                $errorsClean[$error->getPropertyPath()][] = "Attention ce champ est invalide !";
            };
            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $newUser = new User();

        $newUser->setUsername($user->getUsername())
                ->setEmail($user->getEmail());

        $plaintextPassword = $user->getPassword();
        $hashedPassword = $passwordHasher->hashPassword(
            $newUser,
            $plaintextPassword
        );
        $newUser->setPassword($hashedPassword);
                
        $roles = $user->getRoles();
        $newUser->setRoles($roles);   

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
     * Supprimer user
     * Besoin Front : supprimer son compte
     */

    public function deleteUser(User $user = null, ManagerRegistry $doctrine) 
    {
        if ($user === null) {
            return $this->json(['errors' => 'Le user ne peut être supprimé car il n\' existe pas'], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager = $doctrine->getManager();
        
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->json(Response::HTTP_OK );
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="api_users_edit", methods={"PATCH"})
     * Modifier user
     * Besoin Front : modifier le profil
     */
    
    public function editUser(UserPasswordHasherInterface $passwordHasher,User $user,ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request)
    {
        $this->denyAccessUnlessGranted('USER_EDIT', $user);
        $jsonContent = $request->getContent();
        $userEdit = $serializer->deserialize($jsonContent, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        $user->setUsername($userEdit->getUsername())
             ->setEmail($userEdit->getEmail());

        $plaintextPassword = $userEdit->getPassword();
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        
        $user->setRoles($userEdit->getRoles());


        // $listItemsRemove = $user->getListItems();
        // foreach ($listItemsRemove as $listItemRemove) {
        //     $user->removeListItem($listItemRemove);
        // }

        // $listItems = $userEdit->getListItems();
        // foreach ($listItems as $listItem) {
        //     $user->addListItem($listItem);
        // }
    
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'get_users_collection']);
    }
}

