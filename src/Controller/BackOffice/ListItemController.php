<?php

namespace App\Controller\BackOffice;

use App\Entity\ListItem;
use App\Form\ListItemType;
use App\Repository\ItemRepository;
use App\Repository\ListItemRepository;
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
    public function index(ListItemRepository $listItemRepository): Response
    {
        return $this->render('list_item/index.html.twig', [
            'list_items' => $listItemRepository->findAll(),
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

            $listItemRepository->add($listItem);
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
        }

        return $this->redirectToRoute('app_list_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
