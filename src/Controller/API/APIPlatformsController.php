<?php

namespace App\Controller\API;

use App\Entity\Platform;
use App\Repository\PlatformRepository;
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

class APIPlatformsController extends AbstractController
{
    /**
     * @Route("/api/platforms", name="app_api_platforms")
     */
    public function platformList(PlatformRepository $platformsList): Response
    {
        $platformsCollection = $platformsList->findAll();
        //$platform = $serializer->serialize($platformsCollection, 'json');
       
        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $platformsCollection,
            // Le status code
            200,
            // Les en-têtes de réponse à ajouter (aucune)
            [],
            ['groups' => 'get_platforms_collection']
        );
    }

    /**
     * @Route("/api/platforms/{id<\d+>}", name="api_platforms_get_platform", methods={"GET"})
     */
    public function getPlatform(Platform $platform = null) 
    {
        if ($platform === null){
            return $this->json(['error' => 'Platform non trouvé', Response::HTTP_NOT_FOUND]);
        }
        return $this->json($platform, Response::HTTP_OK, [], ['groups' => 'get_platforms_collection']);
    }

    /**
     * @Route("/api/platforms/create", name="app_api_create_platforms", methods={"POST"})
     */
    public function createPlatform(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $jsonContent = $request->getContent();

        try {
            $platform = $serializer->deserialize($jsonContent, Platform::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($platform);

        if (count($errors) > 0) {
            $errorsClean = [];
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };
            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $newPlatform = new Platform();

        $newPlatform->setName($platform->getName());

        $items = $platform->getItems();
        foreach ($items as $item) {
            $newPlatform->addItem($item);
        }
        $modes = $platform->getModes();
        foreach ($modes as $mode) {
            $newPlatform->addMode($mode);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($platform);
        $entityManager->flush();

        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $platform,
            // Le status code
            Response::HTTP_CREATED,
            // Les en-têtes de réponse à ajouter (aucune)
            [
                //'Location' => $this->generateUrl('api_platforms_get_platform', ['id' => $platform->getId()])
            ],
            ['groups' => 'get_platforms_collection']
        );
    }

    /**
     * @Route("api/platforms/{id<\d+>}", name="api_platforms_delete", methods={"DELETE"})
     */

    public function deletePlatform(Platform $platform = null, ManagerRegistry $doctrine) 
    {
        // ajouter un token pour autoriser le delete
        if ($platform === null) {
            return $this->json(['errors' => 'Le platform ne peut être supprimé car il n\' existe pas'], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager = $doctrine->getManager();
        
        $entityManager->remove($platform);
        $entityManager->flush();
        return $this->json(Response::HTTP_OK );
    }

    /**
     * @Route("api/platforms/{id<\d+>}", name="api_platforms_edit", methods={"PUT"})
     */
    public function editPlatform(Platform $platform,ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request)
    {
        $jsonContent = $request->getContent();
        $platformEdit = $serializer->deserialize($jsonContent, Platform::class, 'json');

        $platform->setName($platformEdit->getName());

        $modesRemove = $platform->getModes();
        foreach ($modesRemove as $modeRemove) {
            $platform->removeMode($modeRemove);
        }
        
        $modes = $platformEdit->getModes();
        foreach ($modes as $mode) {
            $platform->addMode($mode);
        }
        
        $itemsRemove = $platform->getItems();
        foreach ($itemsRemove as $itemRemove) {
            $platform->removeItem($itemRemove);
        }

        $items = $platformEdit->getItems();
        foreach ($items as $item) {
            $platform->addItem($item);
        }
    
        $entityManager = $doctrine->getManager();
        $entityManager->persist($platform);
        $entityManager->flush();
        return $this->json($platform, Response::HTTP_OK, [], ['groups' => 'get_platforms_collection']);
   
    }
}

