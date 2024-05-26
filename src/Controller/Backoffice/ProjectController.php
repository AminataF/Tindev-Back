<?php

namespace App\Controller\Backoffice;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/project")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="app_backoffice_project_index", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('backoffice/project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_backoffice_project_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProjectRepository $projectRepository): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->add($project, true);

            return $this->redirectToRoute('app_backoffice_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_project_show", methods={"GET"})
     */
    public function show(Project $project): Response
    {
        return $this->render('backoffice/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_backoffice_project_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->add($project, true);

            return $this->redirectToRoute('app_backoffice_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_project_delete", methods={"POST"})
     */
    public function delete(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $projectRepository->remove($project, true);
        }

        return $this->redirectToRoute('app_backoffice_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
