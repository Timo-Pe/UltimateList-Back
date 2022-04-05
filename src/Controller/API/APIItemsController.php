<?php

namespace App\Controller\API;

use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class APIItemsController extends AbstractController
{
    /**
     * @Route("/api/items", name="app_api_items")
     */
    public function itemsList(ItemRepository $itemsList): Response
    {
        $itemsCollection = $itemsList->findAll();

        return $this->json(
            // Les données à sérialiser (à convertir en JSON)
            $itemsCollection,
            // Le status code
            200,
            // Les en-têtes de réponse à ajouter (aucune)
            []
        );
    }
}
