<?php

namespace App\Controller\API;

use App\Entity\Item;
use App\Entity\ListItem;
use App\Repository\ListItemRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface as SerializerSerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class APIListItemsController extends AbstractController
{
    /**
     * @Route("/api/list_items", name="app_api_listItems")
     * Affiche la liste des items de l'utilisateurs
     * Besoin Front : pour les listes d'items par utilisateurs (filtre à faire par le front)
     */
    public function listItemList(ListItemRepository $listItemsList): Response
    {
        $listItemsCollection = $listItemsList->findAllOrderedByAdded();
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
     * Affiche les infos d'un seul item d'un utilisateurs
     * Besoin Front : pour les détails de cards d'un item utilisateur
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
     * Créer les infos de liste sur l'item de l'utilisateur
     * Besoin Front : quand clique sur ajouter un item, ajoute des infos/préférences pour l'utilisateur
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

        if ($listItem->getItem() ) {
            $newListItem = new ListItem();

            $newListItem->setItemAddedAt(new DateTimeImmutable("NOW"))
                        ->setItemComment(null)
                        ->setItemRating(null)
                        ->setItemStatus(0)
                        ->setMode($listItem->getItem()->getMode())
                        ->setUser($listItem->getUser());
    
            $newListItem->setItem($listItem->getItem());
    
    
            $entityManager = $doctrine->getManager();
            $entityManager->persist($newListItem);
            $entityManager->flush();
    
            return $this->json(
                // Les données à sérialiser (à convertir en JSON)
                $newListItem,
                // Le status code
                Response::HTTP_CREATED,
                // Les en-têtes de réponse à ajouter (aucune)
                [
                    //'Location' => $this->generateUrl('api_listitems_get_listitem', ['id' => $listitem->getId()])
                ],
                ['groups' => 'get_list_items_collection']
            );
        }

    }

    /**
     * @Route("api/list_items/{id<\d+>}", name="api_listItems_delete", methods="DELETE")
     * Enlève les infos/préférences d'un item utilisateur
     * Besoin Front : quand l'utilisateur veut supprimer un item de sa liste
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
     * @Route("api/list_items/{id<\d+>}", name="api_listitems_edit", methods={"PATCH"})*
     * Modifie les infos/préférences d'un item utilisateur
     * Besoin Front : quanbd l'utilisateur veut ajouter/modifier une info/pref (ex : commentaire ou vu/en cours)
     */
    public function editListItem(ListItem $listItem, ManagerRegistry $doctrine, SerializerSerializerInterface $serializer, Request $request)
    {
        $jsonContent = $request->getContent();
        $listItemEdit = $serializer->deserialize($jsonContent, ListItem::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $listItem]);

        $listItemEdit->setMode($listItem->getItem()->getMode());
        
        $entityManager = $doctrine->getManager();
        $entityManager->persist($listItemEdit);
        $entityManager->flush();
        return $this->json($listItem, Response::HTTP_OK, [], ['groups' => 'get_list_items_collection']);
    }
}

