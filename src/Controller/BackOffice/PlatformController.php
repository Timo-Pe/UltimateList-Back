<?php

namespace App\Controller\BackOffice;

use App\Entity\Platform;
use App\Form\PlatformType;
use App\Repository\ModeRepository;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/platform")
 */
class PlatformController extends AbstractController
{
    /**
     * @Route("/", name="app_platform_index", methods={"GET"})
     */
    public function index(PlatformRepository $platformRepository, ModeRepository $modeRepository): Response
    {
        return $this->render('platform/index.html.twig', [
            'platforms' => $platformRepository->findAll(),
            'modes' => $modeRepository->findAll()
        ]);
    }

    /**
     * @Route("/mode/{modeId}", name="app_platform_index_mode", methods={"GET"})
     */
    public function indexByMode(PlatformRepository $platformRepository, ModeRepository $modeRepository, $modeId): Response
    {
        $findByMode = $platformRepository->findByMode($modeId);
        return $this->render('platform/index.html.twig', [
            'platforms' => $findByMode,
            'modes' => $modeRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="app_platform_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PlatformRepository $platformRepository): Response
    {
        $platform = new Platform();
        $form = $this->createForm(PlatformType::class, $platform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $platformRepository->add($platform);
            $this->addFlash('success', 'La plateforme a bien été créée');
            return $this->redirectToRoute('app_platform_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('platform/new.html.twig', [
            'platform' => $platform,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_platform_show", methods={"GET"})
     */
    public function show(Platform $platform): Response
    {
        return $this->render('platform/show.html.twig', [
            'platform' => $platform,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_platform_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, Platform $platform, PlatformRepository $platformRepository): Response
    {
        $form = $this->createForm(PlatformType::class, $platform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $platformRepository->add($platform);
            $this->addFlash('success', 'La plateforme a bien été modifiée');
            return $this->redirectToRoute('app_platform_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('platform/edit.html.twig', [
            'platform' => $platform,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_platform_delete", methods={"POST"})
     */
    public function delete(Request $request, Platform $platform, PlatformRepository $platformRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$platform->getId(), $request->request->get('_token'))) {
            $platformRepository->remove($platform);
            $this->addFlash('success', 'La plateforme a bien été supprimée');
        }

        return $this->redirectToRoute('app_platform_index', [], Response::HTTP_SEE_OTHER);
    }
}
