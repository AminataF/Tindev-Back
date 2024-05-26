<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Validation\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class ProjectController extends AbstractController
{
    /**
     * @Route("/api/projects", name="app_api_project", methods={"GET"})
     * browse all projects
     */
    public function browseAllProject(ProjectRepository $projectRepository): JsonResponse
    {
        $AllProjects = $projectRepository->findAll();

        return $this->json(
            [
            $AllProjects
        ],
            200,
            [],
            [
                "groups" => ["browse_projects"]
            ]
        );
    }

    /**
     * @Route("/api/projects/{id}", name="app_api_project_read", requirements={"id"="\d+"}, methods={"GET"})
     *
     */
    public function readOneProject(Project $project = null): JsonResponse
    {
        // if user provide a faulse ID it's a 404
        if ($project === null) {
            return $this->json(
                [
                    "message" => "Ce projet n'existe pas"
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $project,
            Response::HTTP_OK,
            [],
            [
                "groups" =>
                [
                    "project_read"
                ]
            ]
        );

    }



    /**
      @Route("/api/projects", name="app_api_project_add", methods={"POST"})
    */
        public function add(
            Request $request,
            SerializerInterface $serializer,
            ProjectRepository $projectRepository,
            ValidatorInterface $validator,
            UserInterface $user // Inject the authenticated user
        ): JsonResponse {
            $contentJson = $request->getContent();

            try {
                $projectFromJson = $serializer->deserialize(
                    $contentJson,
                    Project::class,
                    'json'
                );
            } catch (\Throwable $e) {
                return $this->json(
                    $e->getMessage(),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $listError = $validator->validate($projectFromJson);

            if (count($listError) > 0) {
                return $this->json(
                    $listError,
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Set the user on the project
            $projectFromJson->setUser($user);

            $projectRepository->add($projectFromJson, true);

            return $this->json(
                $projectFromJson,
                Response::HTTP_CREATED,
                [],
                [
                    "groups" => ["add_project"]
                ]
            );
    }



    /**
     * @Route("/api/projects/{id}", name="app_api_project_edit", methods={"PUT","PATCH"}, requirements={"id"="\d+"})
     *
     */
    public function edit(
        Project $project = null,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {

        // Check if the project exists
        if ($project === null) {
            return $this->json(["message" => "Projet non trouvé"], Response::HTTP_NOT_FOUND);
        }

        // Get the request content
        $jsonContent = $request->getContent();

        // Deserialize the request content into the project object
        try {
            $serializer->deserialize(
                $jsonContent,
                Project::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $project]
            );
        } catch (\Throwable $error) {
            // Handle deserialization errors
            return $this->json(
                [
                    "message" => $error->getMessage()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Validate the updated project object
        $e = $validator->validate($project);

        if (count($e) > 0) {
            // Handle validation errors
            return $this->json(
                $e,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Save the updated project object to the database
        $entityManager->flush();

        // Return a 204 No Content response
        return $this->json(
            null,
            Response::HTTP_NO_CONTENT
        );
    }


    /**
     * @Route("/api/projects/{id}", name="app_api_project_delete", requirements={"id"="\d+"}, methods={"DELETE"} )
     *
     */
    public function delete(Project $project = null, ProjectRepository $projectRepository): JsonResponse
    {
        if ($project === null) {
            return $this->json("Projet non trouvé", Response::HTTP_NOT_FOUND);
        }

        $projectRepository->remove($project, true);

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT
        );

    }
}
