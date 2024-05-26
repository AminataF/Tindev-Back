<?php

namespace App\Controller\Backoffice;

use App\Entity\Competence;
use App\Form\CompetenceType;
use App\Repository\CompetenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/competence")
 */
class CompetenceController extends AbstractController
{
    /**
     * @Route("/", name="app_backoffice_competence_index", methods={"GET"})
     */
    public function index(CompetenceRepository $competenceRepository): Response
    {
        return $this->render('backoffice/competence/index.html.twig', [
            'competences' => $competenceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_backoffice_competence_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CompetenceRepository $competenceRepository): Response
    {
        $competence = new Competence();
        $form = $this->createForm(CompetenceType::class, $competence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competenceRepository->add($competence, true);

            return $this->redirectToRoute('app_backoffice_competence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/competence/new.html.twig', [
            'competence' => $competence,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_competence_show", methods={"GET"})
     */
    public function show(Competence $competence): Response
    {
        return $this->render('backoffice/competence/show.html.twig', [
            'competence' => $competence,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_backoffice_competence_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Competence $competence, CompetenceRepository $competenceRepository): Response
    {
        $form = $this->createForm(CompetenceType::class, $competence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competenceRepository->add($competence, true);

            return $this->redirectToRoute('app_backoffice_competence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/competence/edit.html.twig', [
            'competence' => $competence,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_competence_delete", methods={"POST"})
     */
    public function delete(Request $request, Competence $competence, CompetenceRepository $competenceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$competence->getId(), $request->request->get('_token'))) {
            $competenceRepository->remove($competence, true);
        }

        return $this->redirectToRoute('app_backoffice_competence_index', [], Response::HTTP_SEE_OTHER);
    }
}
