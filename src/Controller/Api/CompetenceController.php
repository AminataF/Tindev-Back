<?php

namespace App\Controller\Api;

use App\Repository\CompetenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Competence;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CompetenceController extends AbstractController
{

    /**
     * @Route("/api/competences", name="app_api_competence", methods={"GET"})
     * 
     */
    public function browseAllCompetenceByCategory(CompetenceRepository $competenceRepository): JsonResponse
    {
        $AllCompetences = $competenceRepository->findAll();

        return $this->json([
            // je peux ajouter fournir d'autre groupe en passant pas un tableau
            $AllCompetences,
        ],
            200,
            [],
            [
                "groups" => ["browse_competence"]
            ]
        );
    }

    /**
     * @Route("/api/competences/{id}", name="app_api_competence_read", methods={"GET"}, requirements={"id"="\d+"}, )
     * 
     */
    public function read(Competence $competence = null): JsonResponse
    {
        // if user provide a faulse ID it's a 404
         if ($competence === null){
            return $this->json(
                [
                    "message" => "Cette compétence n'existe pas"
                ], 
                // on doit préciser la 404
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $competence,
            Response::HTTP_OK,
            [],
            [
                "groups" => 
                [
                    "competence_read"
                ]
            ]
        );

    }

    /**
     * @Route("/api/competences/{id}", name="app_api_competence_edit", methods={"PUT","PATCH"}, requirements={"id"="\d+"})
     * 
     */
    public function edit(
        Competence $competence = null,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse
    {

        // Check if the user exists
        if ($competence === null) {
            return $this->json(["message" => "Compétence non trouvée"],Response::HTTP_NOT_FOUND);
        }

        // Get the request content
        $jsonContent = $request->getContent();

        // Deserialize the request content into the user object
        try {
            $serializer->deserialize(
                $jsonContent,
                Competence::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $competence]
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

        // Validate the updated user object
        $e = $validator->validate($competence);

        if (count($e) > 0) {
            // Handle validation errors
            return $this->json(
                $e,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Save the updated user object to the database
        $entityManager->flush();

        // Return a 204 No Content response
        return $this->json(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     * @Route("/api/competences", name="app_api_competence_add", methods={"POST"})
     * 
     */
    public function add(Request $request, SerializerInterface $serializer, CompetenceRepository $competenceRepository, ValidatorInterface $validator): JsonResponse
    {
        $contentJson = $request->getContent();
        
        try {

            $competenceFromJsn = $serializer->deserialize(
                $contentJson,
                Competence::class,
                'json'
            );
        } catch (\Throwable $e){
            
            return $this->json(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY

            );
        }
        
        $listError = $validator->validate($competenceFromJsn);

        if (count($listError) > 0){
           
            return $this->json(
                
                $listError,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $competenceRepository->add($competenceFromJsn, true);

        return $this->json(
            $competenceFromJsn,
            Response::HTTP_CREATED,
            [],
            
            [
                "groups" => 
                [
                    "add_competence"
                ]
            ]
        );
    }

        /**
     * @Route("/api/competences/{id}", name="app_api_competence_delete", requirements={"id"="\d+"}, methods={"DELETE"} )
     * 
     */
    public function delete(Competence $competence= null, CompetenceRepository $competenceRepository): JsonResponse
    {
        if ($competence === null){
            return $this->json("Compétence non trouvée", Response::HTTP_NOT_FOUND);
        }

        $competenceRepository->remove($competence, true);

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT
        );

    }
    // voici un commentaire
}