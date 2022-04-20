<?php

namespace App\Controller\BackOffice;

use App\Repository\ItemRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainBackController extends AbstractController
{
    /**
     * @Route("/backoffice", name="app_main_back")
     */
    public function index(ItemRepository $itemRepository, TagRepository $tagRepository, UserRepository $userRepository): Response
    {
        return $this->render('main_back/index.html.twig', [
            'controller_name' => 'MainBackController',
            'items' => $itemRepository->findAllOrderById(),
            'users' => $userRepository->findAllOrderById(),
            'tags' => $tagRepository->findAll(),
        ]);
    }
}
