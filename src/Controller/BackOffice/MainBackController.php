<?php

namespace App\Controller\BackOffice;

use App\Repository\ItemRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainBackController extends AbstractController
{
    /**
     * @Route("/backoffice", name="app_main_back")
     */
    public function index(ItemRepository $itemRepository, TagRepository $tagRepository): Response
    {
        return $this->render('main_back/index.html.twig', [
            'controller_name' => 'MainBackController',
            'items' => $itemRepository->findAllOrderById(),
            'tags' => $tagRepository->findAll(),
        ]);
    }
}
