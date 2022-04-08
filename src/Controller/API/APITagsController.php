<?php

namespace App\Controller\API;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class APITagsController extends AbstractController
{
    /**
     * @Route("/api/tags", name="app_api_tags")
     * Affiche la liste des tags
     * Besoin Front : pour les recommandations et les recherches
     */
    public function tagList(TagRepository $tagsList): Response
    {
        $tagsCollection = $tagsList->findAll();
        //$tag = $serializer->serialize($tagsCollection, 'json');
       
        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $tagsCollection,
            // Le status code
            200,
            // Les en-têtes de réponse à ajouter (aucune)
            [],
            ['groups' => 'get_tags_collection']
        );
    }

    /**
     * @Route("/api/tags/create", name="app_api_create_tags", methods="POST")
     * Créer un tag
     * Besoin Front : (future version) proposition d'un nouveau tag
     */
    public function createTag(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $jsonContent = $request->getContent();

        try {
            $tag = $serializer->deserialize($jsonContent, Tag::class, 'json');
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validator->validate($tag);

        if (count($errors) > 0) {
            $errorsClean = [];
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };
            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $newTag = new Tag();

        $newTag->setName($tag->getName());
        $items = $tag->getItems();

        foreach ($items as $item) {
            $newTag->addItem($item);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($newTag);
        $entityManager->flush();

        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $tag,
            // Le status code
            Response::HTTP_CREATED,
            // Les en-têtes de réponse à ajouter (aucune)
            [
                //'Location' => $this->generateUrl('api_tags_get_tag', ['id' => $tag->getId()])
            ],
            ['groups' => 'get_tags_collection']
        );
    }

}

