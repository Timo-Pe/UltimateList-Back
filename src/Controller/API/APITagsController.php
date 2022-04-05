<?php

namespace App\Controller\API;

use App\Entity\Tag;
use App\Repository\TagRepository;
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

class APITagsController extends AbstractController
{
    /**
     * @Route("/api/tags", name="app_api_tags")
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
     * @Route("/api/tags/{id<\d+>}", name="api_tags_get_tag", methods={"GET"})
     */
    public function getTag(Tag $tag = null) 
    {
        if ($tag === null){
            return $this->json(['error' => 'Tag non trouvé', Response::HTTP_NOT_FOUND]);
        }
        return $this->json($tag, Response::HTTP_OK, [], ['groups' => 'get_tags_collection']);
    }

    /**
     * @Route("/api/tags/create", name="app_api_create_tags", methods={"POST"})
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
        // les items ne rentre pas en BDD
        $entityManager = $doctrine->getManager();
        $entityManager->persist($tag);
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

    /**
     * @Route("api/tags/{id<\d+>}", name="api_tags_delete", methods={"DELETE"})
     */

    public function deleteTag(Tag $tag = null, ManagerRegistry $doctrine) 
    {
        // ajouter un token pour autoriser le delete
        if ($tag === null) {
            return $this->json(['errors' => 'Le tag ne peut être supprimé car il n\' existe pas'], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager = $doctrine->getManager();
        
        $entityManager->remove($tag);
        $entityManager->flush();
        return $this->json(Response::HTTP_OK );
    }

    /**
     * @Route("api/tags/{id<\d+>}", name="api_tags_edit", methods={"PATCH"})
     */
    public function editTag(Tag $tag,ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request)
    {
        $jsonContent = $request->getContent();
        $tagEdit = $serializer->deserialize($jsonContent, Tag::class, 'json');

        $tag->setName($tagEdit->getName());
        $items = $tagEdit->getItems();
        foreach ($items as $item) {
            $tag->addItem($item);
        }
    
        $entityManager = $doctrine->getManager();
        $entityManager->persist($tag);
        $entityManager->flush();
        return $this->json($tag, Response::HTTP_OK, [], ['groups' => 'get_tags_collection']);
   
    }
}

