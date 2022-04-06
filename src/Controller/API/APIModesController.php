<?php

namespace App\Controller\API;

use App\Entity\Mode;
use App\Repository\ModeRepository;
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

class APIModesController extends AbstractController
{
    /**
     * @Route("/api/modes", name="app_api_modes")
     */
    public function modeList(ModeRepository $modesList): Response
    {
        $modesCollection = $modesList->findAll();
        //$mode = $serializer->serialize($modesCollection, 'json');
       
        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $modesCollection,
            // Le status code
            200,
            // Les en-têtes de réponse à ajouter (aucune)
            [],
            ['groups' => 'get_modes_collection']
        );
    }

    /**
     * @Route("/api/modes/{id<\d+>}", name="api_modes_get_mode", methods="GET")
     */
    public function getMode(Mode $mode = null) 
    {
        if ($mode === null){
            return $this->json(['error' => 'Mode non trouvé', Response::HTTP_NOT_FOUND]);
        }
        return $this->json($mode, Response::HTTP_OK, [], ['groups' => 'get_modes_collection']);
    }

    /**
     * @Route("/api/modes/create", name="app_api_create_modes", methods="POST")
     */
    public function createMode(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $jsonContent = $request->getContent();

        try {
            $mode = $serializer->deserialize($jsonContent, Mode::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($mode);

        if (count($errors) > 0) {
            $errorsClean = [];
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };
            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $newMode = new Mode();

        $newMode->setName($mode->getName());

        if ($mode->getItems() != null) {
            $items = $mode->getItems();
            foreach ($items as $item) {
                $newMode->addItem($item);
            }
        }

        if ($mode->getListItems() != null) {
            $listItems = $mode->getListItems();
            foreach ($listItems as $listItem) {
                $newMode->addListItem($listItem);
            }
        }

        if ($mode->getPlatforms() != null) {
            $platforms = $mode->getPlatforms();
            foreach ($platforms as $platform) {
                $newMode->addPlatform($platform);
            }
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($mode);
        $entityManager->flush();

        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $mode,
            // Le status code
            Response::HTTP_CREATED,
            // Les en-têtes de réponse à ajouter (aucune)
            [
                //'Location' => $this->generateUrl('api_modes_get_mode', ['id' => $mode->getId()])
            ],
            ['groups' => 'get_modes_collection']
        );
    }

    /**
     * @Route("api/modes/{id<\d+>}", name="api_modes_delete", methods="DELETE")
     */

    public function deleteMode(Mode $mode = null, ManagerRegistry $doctrine) 
    {
        // ajouter un token pour autoriser le delete
        if ($mode === null) {
            return $this->json(['errors' => 'Le mode ne peut être supprimé car il n\' existe pas'], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager = $doctrine->getManager();
        
        $entityManager->remove($mode);
        $entityManager->flush();
        return $this->json(Response::HTTP_OK );
    }

    /**
     * @Route("api/modes/{id<\d+>}", name="api_modes_edit", methods="PUT")
     */
    public function editMode(Mode $mode,ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request)
    {
        $jsonContent = $request->getContent();
        $modeEdit = $serializer->deserialize($jsonContent, Mode::class, 'json');

        $mode->setName($modeEdit->getName());
    
        if ($modeEdit->getItems() != null) {
            $itemsRemove = $mode->getItems();
            foreach ($itemsRemove as $itemRemove) {
                $mode->removeItem($itemRemove);
            }
        
            $items = $modeEdit->getItems();
            
            foreach ($items as $item) {
                $mode->addItem($item);
            }
        }
        
        if ($modeEdit->getListItems() != null) {
            $listItemsRemove = $mode->getListItems();
            foreach ($listItemsRemove as $listItemRemove) {
                $mode->removeListItem($listItemRemove);
            }

            $listItems = $modeEdit->getListItems();
            foreach ($listItems as $listItem) {
            $mode->addListItem($listItem);
            }
        }

        if ($modeEdit->getPlatforms() != null) {
            $platformsRemove = $mode->getPlatforms();
            foreach ($platformsRemove as $platformRemove) {
                $mode->removePlatform($platformRemove);
            }
        
            $platforms = $modeEdit->getPlatforms();
            foreach ($platforms as $platform) {
                $mode->addPlatform($platform);
            }
        }
    
        $entityManager = $doctrine->getManager();
        $entityManager->persist($mode);
        $entityManager->flush();
        return $this->json($mode, Response::HTTP_OK, [], ['groups' => 'get_modes_collection']);
   
    }
}

