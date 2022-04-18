<?php

namespace App\Controller\API;

use App\Entity\Item;
use App\Entity\Mode;
use App\Entity\User;
use App\Repository\ItemRepository;
use App\Repository\ModeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class APIItemsController extends AbstractController
{
    /**
     * @Route("/api/items", name="app_api_items")
     * Affiche la liste des items
     * Besoin Front : pour les recommandations et les recherches
     */
    public function itemList(ItemRepository $itemsList): Response
    {
        $itemsCollection = $itemsList->findAll();
        //$item = $serializer->serialize($itemsCollection, 'json');
       
        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $itemsCollection,
            // Le status code
            200,
            // Les en-têtes de réponse à ajouter (aucune)
            [],
            ['groups' => 'get_items_collection']
        );
    }

    /**
     * @Route("/api/items/recco/{userId<\d+>}", name="app_api_items_recco")
     * Affiche la liste des items
     * Besoin Front : pour les recommandations et les recherches
     */
    public function itemListRecco(ItemRepository $itemsList, $userId): Response
    {
        $itemsCollection = $itemsList->findAllExceptInListItem($userId);
        dd($itemsCollection);
        //$item = $serializer->serialize($itemsCollection, 'json');
       
        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $itemsCollection,
            // Le status code
            200,
            // Les en-têtes de réponse à ajouter (aucune)
            [],
            ['groups' => 'get_items_collection']
        );
    }

    /**
     * @Route("/api/items/{id<\d+>}", name="api_items_get_item", methods="GET")
     * Affiche les infos d'un item
     * Besoin Front : pour les détails des cards items
     */
    public function getItem(Item $item = null) 
    {
        if ($item === null){
            return $this->json(['error' => 'Item non trouvé', Response::HTTP_NOT_FOUND]);
        }
        return $this->json($item, Response::HTTP_OK, [], ['groups' => 'get_items_collection']);
    }

    /**
     * @Route("/api/items/create", name="app_api_create_items", methods="POST")
     * Créer un item
     * Besoin Front : (futures versions) proposition d'un item par un utilisateur 
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $jsonContent = $request->getContent();

        try {
            $item = $serializer->deserialize($jsonContent, Item::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($item);

        if (count($errors) > 0) {
            $errorsClean = [];
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };
            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $newItem = new Item();

        $newItem->setName($item->getName())
                ->setDescription($item->getDescription())
                ->setReleaseDate($item->getReleaseDate())
                ->setImage($item->getImage())
                ->setMode($item->getMode())
                ->setProductor($item->getProductor())
                ->setEditor($item->getEditor())
                ->setAutor($item->getAutor())
                ->setHost($item->getHost())
                ->setDeveloper($item->getDeveloper());

        
        if ($item->getListItems() != null) {
            $list_items = $item->getListItems();
            foreach ($list_items as $list_item) {
                $newItem->addListItem($list_item);
            }
        }

        if ($item->getPlatforms() != null) {
            $platforms = $item->getPlatforms();
            foreach ($platforms as $platform) {
                $newItem->addPlatform($platform);
            }
        }

        if ($item->getTags() != null) {
            $tags = $item->getTags();
            foreach ($tags as $tag) {
                $newItem->addTag($tag);
            }
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($newItem);
        $entityManager->flush();

        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $item,
            // Le status code
            Response::HTTP_CREATED,
            // Les en-têtes de réponse à ajouter (aucune)
            [
                //'Location' => $this->generateUrl('api_items_get_item', ['id' => $item->getId()])
            ],
            ['groups' => 'get_items_collection']
        );
    }

    /**
     * @Route("/api/modes/{id<\d+>}/items", name="api_items_get_items_by_mode", methods="GET")
     * Affiche la liste des items selon le mode
     * Besoin Front : pour les recommandations et les recherches (facilitation par rapport à l'affiche de toute la liste)
     */
    public function getItemsByMode(Mode $mode, ModeRepository $modeRepository) 
    {
        $listItems = $modeRepository->findItemsByMode($mode->getId());

        if ($listItems === null){
            return $this->json(['error' => 'Item non trouvé', Response::HTTP_NOT_FOUND]);
        }
        return $this->json($listItems, Response::HTTP_OK, [], ['groups' => 'get_items_collection']);
    }

}

