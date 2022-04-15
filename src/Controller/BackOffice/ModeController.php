<?php

namespace App\Controller\BackOffice;

use App\Entity\Mode;
use App\Form\ModeType;
use App\Repository\ModeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/mode")
 */
class ModeController extends AbstractController
{
    /**
     * @Route("/", name="app_mode_index", methods={"GET"})
     */
    public function index(ModeRepository $modeRepository): Response
    {
        return $this->render('mode/index.html.twig', [
            'modes' => $modeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_mode_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ModeRepository $modeRepository): Response
    {
        $mode = new Mode();
        $form = $this->createForm(ModeType::class, $mode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $modeRepository->add($mode);
            $this->addFlash('success', 'Le mode a bien été créé');
            return $this->redirectToRoute('app_mode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('mode/new.html.twig', [
            'mode' => $mode,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_mode_show", methods={"GET"})
     */
    public function show(Mode $mode): Response
    {
        return $this->render('mode/show.html.twig', [
            'mode' => $mode,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_mode_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Mode $mode, ModeRepository $modeRepository): Response
    {
        $form = $this->createForm(ModeType::class, $mode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $modeRepository->add($mode);
            $this->addFlash('success', 'Le mode a bien été édité');
            return $this->redirectToRoute('app_mode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('mode/edit.html.twig', [
            'mode' => $mode,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_mode_delete", methods={"POST"})
     */
    public function delete(Request $request, Mode $mode, ModeRepository $modeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mode->getId(), $request->request->get('_token'))) {
            $modeRepository->remove($mode);
            $this->addFlash('success', 'Le mode a bien été supprimé');
        }

        return $this->redirectToRoute('app_mode_index', [], Response::HTTP_SEE_OTHER);
    }
}
