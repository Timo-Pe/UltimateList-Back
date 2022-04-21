<?php

namespace App\Controller\BackOffice;

use App\Entity\ListItem;
use App\Entity\User;
use App\Form\ListItemType;
use App\Repository\ItemRepository;
use App\Repository\ListItemRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/list_item")
 */
class ListItemController extends AbstractController
{
    /**
     * @Route("/", name="app_list_item_index", methods={"GET"})
     */
    public function index(ListItemRepository $listItemRepository, UserRepository $userRepository): Response
    {
        return $this->render('list_item/index.html.twig', [
            'list_items' => $listItemRepository->findAll(),
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * @Route("/user/{userId}", name="app_list_item_index_user", methods={"GET"})
     */
    public function indexByUser(ListItemRepository $listItemRepository, UserRepository $userRepository, $userId): Response
    {
        $findByUser = $listItemRepository->findByUser($userId);

        return $this->render('list_item/index.html.twig', [
            'list_items' => $findByUser,
            'users' => $userRepository->findAll(),
            'actualUser' => $userId
        ]);
    }

    /**
     * @Route("/added", name="app_list_item_index_creation", methods={"GET"})
     */
    public function indexByCreationDate(ListItemRepository $listItemRepository, UserRepository $userRepository): Response
    {
        $findByCreationDate = $listItemRepository->findByCreationDate();
        return $this->render('list_item/index.html.twig', [
            'list_items' => $findByCreationDate,
            'users' => $userRepository->findAll()
        ]);
    } 

    /**
     * @Route("/user/{userId}/added", name="app_list_item_index_user_creation_new", methods={"GET"})
     */
    public function indexByCreationDateUser(ListItemRepository $listItemRepository, UserRepository $userRepository, $userId): Response
    {
        $findByCreationDateAndUser = $listItemRepository->findByCreationDateAndUser($userId);
        return $this->render('list_item/index.html.twig', [
            'list_items' => $findByCreationDateAndUser,
            'users' => $userRepository->findAll(),
            'actualUser' => $userId
        ]);
    } 

    /**
     * @Route("/user/{userId}/added/o", name="app_list_item_index_user_creation_old", methods={"GET"})
     */
    public function indexByCreationDateUserOld(ListItemRepository $listItemRepository, UserRepository $userRepository, $userId): Response
    {
        $findByCreationDateAndUser = $listItemRepository->findByCreationDateAndUserOld($userId);
        return $this->render('list_item/index.html.twig', [
            'list_items' => $findByCreationDateAndUser,
            'users' => $userRepository->findAll(),
            'actualUser' => $userId
        ]);
    } 

        /**
     * @Route("/user/{userId}/alpha", name="app_list_item_index_user_alpha", methods={"GET"})
     */
    public function indexByUserAndAlphabeticalOrder(ListItemRepository $listItemRepository, UserRepository $userRepository, $userId): Response
    {
        $findByAlphabeticalOrderAndUser = $listItemRepository->findByAlphabeticalOrderAndUser($userId);
        return $this->render('list_item/index.html.twig', [
            'list_items' => $findByAlphabeticalOrderAndUser,
            'users' => $userRepository->findAll(),
            'actualUser' => $userId
        ]);
    } 

        /**
     * @Route("/alpha", name="app_list_item_index_alpha", methods={"GET"})
     */
    public function indexByAlphabeticalOrder(ListItemRepository $listItemRepository, UserRepository $userRepository): Response
    {
        $findByAlphabeticalOrder = $listItemRepository->findByAlphabeticalOrder();
        return $this->render('list_item/index.html.twig', [
            'list_items' => $findByAlphabeticalOrder,
            'users' => $userRepository->findAll()
        ]);
    } 

    /**
     * @Route("/new", name="app_list_item_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ListItemRepository $listItemRepository, ManagerRegistry $doctrine, ItemRepository $itemRepository): Response
    {
        $listItem = new ListItem();
        $form = $this->createForm(ListItemType::class, $listItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listItem->setItemAddedAt(new DateTimeImmutable("NOW"));
            $listItem->setItemStatus(0);
            $listItem->setMode($listItem->getItem()->getMode());

            $listItemRepository->add($listItem);
            $this->addFlash('success', 'Le listItem a bien été créé');
            return $this->redirectToRoute('app_list_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('list_item/new.html.twig', [
            'list_item' => $listItem,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_list_item_show", methods={"GET"})
     */
    public function show(ListItem $listItem): Response
    {
        return $this->render('list_item/show.html.twig', [
            'list_item' => $listItem,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_list_item_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ListItem $listItem, ListItemRepository $listItemRepository): Response
    {
        $form = $this->createForm(ListItemType::class, $listItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listItemRepository->add($listItem);
            $this->addFlash('success', 'Le listItem a bien été modifié');
            return $this->redirectToRoute('app_list_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('list_item/edit.html.twig', [
            'list_item' => $listItem,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_list_item_delete", methods={"POST"})
     */
    public function delete(Request $request, ListItem $listItem, ListItemRepository $listItemRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$listItem->getId(), $request->request->get('_token'))) {
            $listItemRepository->remove($listItem);
            $this->addFlash('success', 'Le listItem a bien été supprimé');
        }

        return $this->redirectToRoute('app_list_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
