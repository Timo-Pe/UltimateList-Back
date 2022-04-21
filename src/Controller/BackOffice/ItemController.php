<?php

namespace App\Controller\BackOffice;

use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use App\Repository\ModeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/item")
 */
class ItemController extends AbstractController
{
    /**
     * @Route("/", name="app_item_index", methods={"GET"})
     */
    public function index(ItemRepository $itemRepository, ModeRepository $modeRepository): Response
    {
        return $this->render('item/index.html.twig', [
            'items' => $itemRepository->findAll(),
            'modes' => $modeRepository->findAll()
        ]);
    }

    /**
     * @Route("/mode/{modeId}", name="app_item_index_mode", methods={"GET"})
     */
    public function indexByMode(ItemRepository $itemRepository, ModeRepository $modeRepository, $modeId): Response
    {
        $findByMode = $itemRepository->findByMode($modeId);
        return $this->render('item/index.html.twig', [
            'items' => $findByMode,
            'modes' => $modeRepository->findAll(),
            'actualMode' => $modeId
        ]);
    } 

    /**
     * @Route("/added", name="app_item_index_creation", methods={"GET"})
     */
    public function indexByCreationDate(ItemRepository $itemRepository, ModeRepository $modeRepository): Response
    {
        $findByCreationDate = $itemRepository->findByCreationDate();
        return $this->render('item/index.html.twig', [
            'items' => $findByCreationDate,
            'modes' => $modeRepository->findAll()
        ]);
    } 

    /**
     * @Route("/mode/{modeId}/added", name="app_item_index_mode_creation_new", methods={"GET"})
     */
    public function indexByCreationDateMode(ItemRepository $itemRepository, ModeRepository $modeRepository, $modeId): Response
    {
        $findByCreationDateAndMode = $itemRepository->findByCreationDateAndMode($modeId);
        return $this->render('item/index.html.twig', [
            'items' => $findByCreationDateAndMode,
            'modes' => $modeRepository->findAll(),
            'actualMode' => $modeId
        ]);
    } 

    /**
     * @Route("/mode/{modeId}/added/o", name="app_item_index_mode_creation_old", methods={"GET"})
     */
    public function indexByCreationDateModeOld(ItemRepository $itemRepository, ModeRepository $modeRepository, $modeId): Response
    {
        $findByCreationDateAndMode = $itemRepository->findByCreationDateAndModeOld($modeId);
        return $this->render('item/index.html.twig', [
            'items' => $findByCreationDateAndMode,
            'modes' => $modeRepository->findAll(),
            'actualMode' => $modeId
        ]);
    } 

    /**
     * @Route("/mode/{modeId}/alpha", name="app_item_index_mode_alpha", methods={"GET"})
     */
    public function indexByModeAndAlphabeticalOrder(ItemRepository $itemRepository, ModeRepository $modeRepository, $modeId): Response
    {
        $findByAlphabeticalOrderAndMode = $itemRepository->findByAlphabeticalOrderAndMode($modeId);
        return $this->render('item/index.html.twig', [
            'items' => $findByAlphabeticalOrderAndMode,
            'modes' => $modeRepository->findAll(),
            'actualMode' => $modeId
        ]);
    } 

        /**
     * @Route("/alpha", name="app_item_index_alpha", methods={"GET"})
     */
    public function indexByAlphabeticalOrder(ItemRepository $itemRepository, ModeRepository $modeRepository): Response
    {
        $findByAlphabeticalOrder = $itemRepository->findByAlphabeticalOrder();
        return $this->render('item/index.html.twig', [
            'items' => $findByAlphabeticalOrder,
            'modes' => $modeRepository->findAll()
        ]);
    } 

    /**
     * @Route("/new", name="app_item_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ItemRepository $itemRepository): Response
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (is_null($form->get('background_image')->getData())){
                $item->setBackgroundImage($form->get('image')->getData());
            }
            
            $itemRepository->add($item);
            $this->addFlash('success', 'L\'Item a bien été créé');
            return $this->redirectToRoute('app_item_index', [], Response::HTTP_SEE_OTHER);
        }
 
        return $this->renderForm('item/new.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_item_show", methods={"GET"})
     */
    public function show(Item $item): Response
    {
        return $this->render('item/show.html.twig', [
            'item' => $item,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_item_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Item $item, ItemRepository $itemRepository, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (is_null($form->get('background_image')->getData())){
                $item->setBackgroundImage($form->get('image')->getData());
            }

            $entityManager = $doctrine->getManager();
            $itemRepository->add($item);
            $entityManager->flush();
            $this->addFlash('success', 'L\'Item a bien été modifié');
            return $this->redirectToRoute('app_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('item/edit.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_item_delete", methods={"POST"})
     */
    public function delete(Request $request, Item $item, ItemRepository $itemRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$item->getId(), $request->request->get('_token'))) {
            $itemRepository->remove($item);
            $this->addFlash('success', 'L\'Item a bien été supprimé');
        }

        return $this->redirectToRoute('app_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
