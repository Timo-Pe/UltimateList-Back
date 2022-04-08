<?php

namespace App\Controller\API;

use App\Repository\ModeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class APIModesController extends AbstractController
{
    /**
     * @Route("/api/modes", name="app_api_modes")
     * Affiche la liste des modes
     * Besoin Front : pour la page d'accueil et la navigation
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
}

