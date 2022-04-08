<?php

namespace App\Controller\API;

use App\Entity\Platform;
use App\Repository\PlatformRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class APIPlatformsController extends AbstractController
{
    /**
     * @Route("/api/platforms", name="app_api_platforms")
     * Affiche la liste des plateformes
     * Besoin Front : pour les recherches
     */
    public function platformList(PlatformRepository $platformsList): Response
    {
        $platformsCollection = $platformsList->findAll();
       
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
     * @Route("/api/platforms/create", name="app_api_create_platforms", methods="POST")
     * Créer une plateforme
     * Besoin Front : (future version) proposition d'une nouvelle plateforme
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

}

