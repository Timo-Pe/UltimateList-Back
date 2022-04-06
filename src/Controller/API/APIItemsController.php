<?php

namespace App\Controller\API;

use App\Entity\Item;
use App\Repository\ItemRepository;
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

class APIItemsController extends AbstractController
{
    /**
     * @Route("/api/items", name="app_api_items")
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
     * @Route("/api/items/{id<\d+>}", name="api_items_get_item", methods={"GET"})
     */
    public function getItem(Item $item = null) 
    {
        if ($item === null){
            return $this->json(['error' => 'Item non trouvé', Response::HTTP_NOT_FOUND]);
        }
        return $this->json($item, Response::HTTP_OK, [], ['groups' => 'get_items_collection']);
    }

    /**
     * @Route("/api/items/create", name="app_api_create_items", methods={"POST"})
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
                ->setImage($item->getImage())
                ->setMode($item->getMode());

        $productors = $item->getProductor();
        foreach ($productors as $productor) {
            $newItem->setProductor($productor);
        }

        $editors = $item->getEditor();
        foreach ($editors as $editor) {
            $newItem->setEditor($editor);
        }

        $autors = $item->getAutor();
        foreach ($autors as $autor) {
            $newItem->setAutor($autor);
        }

        $hosts = $item->getHost();
        foreach ($hosts as $host) {
            $newItem->setHost($host);
        }
        $developers = $item->getDeveloper();
        foreach ($developers as $developer) {
            $newItem->setDeveloper($developer);
        }

        $list_items = $item->getListItems();
        foreach ($list_items as $list_item) {
            $newItem->addListItem($list_item);
        }

        $platforms = $item->getPlatforms();
        foreach ($platforms as $platform) {
            $newItem->addPlatform($platform);
        }

        $tags = $item->getTags();
        foreach ($tags as $tag) {
            $newItem->addTag($tag);
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
     * @Route("api/items/{id<\d+>}", name="api_items_delete", methods={"DELETE"})
     */

    public function deleteItem(Item $item = null, ManagerRegistry $doctrine) 
    {
        // ajouter un token pour autoriser le delete
        if ($item === null) {
            return $this->json(['errors' => 'L\'item ne peut être supprimé car il n\' existe pas'], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager = $doctrine->getManager();
        
        $entityManager->remove($item);
        $entityManager->flush();
        return $this->json(Response::HTTP_OK );
    }

    /**
     * @Route("api/items/{id<\d+>}", name="api_items_edit", methods={"PATCH"})
     */
    public function editItem(Item $item, ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request)
    {
        $jsonContent = $request->getContent();
        $itemEdit = $serializer->deserialize($jsonContent, Item::class, 'json');

        $item->setName($itemEdit->getName())
        ->setDescription($itemEdit->getDescription())
        ->setImage($itemEdit->getImage())
        ->setMode($itemEdit->getMode());

        $productors = $itemEdit->getProductor();
        foreach ($productors as $productor) {
            $item->setProductor($productor);
        }

        $editors = $itemEdit->getEditor();
        foreach ($editors as $editor) {
            $item->setEditor($editor);
        }

        $autors = $itemEdit->getAutor();
        foreach ($autors as $autor) {
            $item->setAutor($autor);
        }

        $hosts = $itemEdit->getHost();
        foreach ($hosts as $host) {
            $item->setHost($host);
        }
        $developers = $itemEdit->getDeveloper();
        foreach ($developers as $developer) {
            $item->setDeveloper($developer);
        }

        $list_items = $itemEdit->getListItems();
        foreach ($list_items as $list_item) {
            $item->addListItem($list_item);
        }

        $platforms = $itemEdit->getPlatforms();
        foreach ($platforms as $platform) {
            $item->addPlatform($platform);
        }

        $tags = $itemEdit->getTags();
        foreach ($tags as $tag) {
            $item->addTag($tag);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($item);
        $entityManager->flush();
        return $this->json($item, Response::HTTP_OK, [], ['groups' => 'get_items_collection']);
   
    }
}

