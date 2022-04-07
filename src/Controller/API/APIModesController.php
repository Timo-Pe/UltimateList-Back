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

}

