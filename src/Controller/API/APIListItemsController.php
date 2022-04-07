<?php

namespace App\Controller\API;

use App\Entity\Item;
use App\Entity\ListItem;
use App\Entity\Mode;
use App\Entity\User;
use App\Form\ListItemType;
use App\Repository\ListItemRepository;
use DateTimeImmutable;
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

class APIListItemsController extends AbstractController
{
    /**
     * @Route("/api/list_items", name="app_api_listItems")
     */
    public function listItemList(ListItemRepository $listItemsList): Response
    {
        $listItemsCollection = $listItemsList->findAll();
        //$listitem = $serializer->serialize($listitemsCollection, 'json');
       
        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $listItemsCollection,
            // Le status code
            200,
            // Les en-têtes de réponse à ajouter (aucune)
            [],
            ['groups' => 'get_list_items_collection']
        );
    }

    /**
     * @Route("/api/list_items/{id<\d+>}", name="api_listitems_get_listItem", methods="GET")
     */
    public function getListItem(ListItem $listItem = null) 
    {
        if ($listItem === null){
            return $this->json(['error' => 'ListItem non trouvé', Response::HTTP_NOT_FOUND]);
        }
        return $this->json($listItem, Response::HTTP_OK, [], ['groups' => 'get_list_items_collection']);
    }

    /**
     * @Route("/api/list_items/create", name="app_api_create_listItems", methods="POST")
     */
    public function createListItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $jsonContent = $request->getContent();

        try {
            $listItem = $serializer->deserialize($jsonContent, ListItem::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($listItem);

        if (count($errors) > 0) {
            $errorsClean = [];
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };
            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $newListItem = new ListItem();

        $newListItem->setItemAddedAt(new DateTimeImmutable("NOW"))
                    ->setItemComment($listItem->getItemComment())
                    ->setItemRating($listItem->getItemRating())
                    ->setItemStatus($listItem->getItemStatus())
                    ->setMode($listItem->getMode())
                    ->setUser($listItem->getUser());

        $items = $listItem->getItems();
        foreach ($items as $item) {
            $newListItem->addItem($item);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($newListItem);
        $entityManager->flush();

        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $listItem,
            // Le status code
            Response::HTTP_CREATED,
            // Les en-têtes de réponse à ajouter (aucune)
            [
                //'Location' => $this->generateUrl('api_listitems_get_listitem', ['id' => $listitem->getId()])
            ],
            ['groups' => 'get_list_items_collection']
        );
    }

    /**
     * @Route("api/list_items/{id<\d+>}", name="api_listItems_delete", methods="DELETE")
     */

    public function deleteListItem(ListItem $listItem = null, ManagerRegistry $doctrine) 
    {
        // ajouter un token pour autoriser le delete
        if ($listItem === null) {
            return $this->json(['errors' => 'Le listitem ne peut être supprimé car il n\' existe pas'], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager = $doctrine->getManager();
        
        $entityManager->remove($listItem);
        $entityManager->flush();
        return $this->json(Response::HTTP_OK );
    }

    /**
     * @Route("api/list_items/{id<\d+>}", name="api_listitems_edit", methods={"PATCH"})
     */
    public function editListItem(ListItem $listItem, ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request)
    {
        $jsonContent = $request->getContent();
        $listItemEdit = $serializer->deserialize($jsonContent, ListItem::class, 'json');

        if ($listItemEdit->getItemComment() != null) {
            $listItem->setItemComment($listItemEdit->getItemComment());
        }
        else {
            $listItem->setItemComment($listItem->getItemComment());
        }

        if ($listItemEdit->getItemRating() != null) {
            $listItem->setItemRating($listItemEdit->getItemRating());
        }
        else {
            $listItem->setItemRating($listItem->getItemRating());
        }

        if ($listItemEdit->getMode() != null) {
            $listItem->setMode($listItemEdit->getMode());
        }
        else {
            $listItem->setMode($listItem->getMode());
        }

        if ($listItemEdit->getUser() != null) {
            $listItem->setUser($listItemEdit->getUser());
        }
        else {
            $listItem->setUser($listItem->getUser());
        }

        if ($listItemEdit->getItemStatus() === null) {
            $listItem->setItemStatus(0);
        }
        else {
            $listItem->setItemStatus($listItemEdit->getItemStatus());
        }

        $items = $listItemEdit->getItems();
        foreach ($items as $item) {
            $listItem->addItem($item);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($listItem);
        $entityManager->flush();
        return $this->json($listItem, Response::HTTP_OK, [], ['groups' => 'get_list_items_collection']);
    }

    /**
     * @Route("/api/users/{userid<\d+>}/modes/{id<\d+>}/list_items", name="api_listitems_get_items_by_listItem", methods="GET")
     */
    public function getListItemByUser($userid, Mode $mode, ListItemRepository $listItemRepo) 
    {
        $userItems = $listItemRepo->findUserItemsByMode($userid, $mode->getId());
        if ($userItems === null){
            return $this->json(['error' => 'ListItem non trouvé', Response::HTTP_NOT_FOUND]);
        }
        return $this->json($userItems, Response::HTTP_OK, [], ['groups' => 'get_list_items_collection']);
    }
}

