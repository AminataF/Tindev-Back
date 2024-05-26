<?php

namespace App\Controller\Api;

use App\Entity\YearExperience;
use App\Repository\YearExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class YearExperienceController extends AbstractController
{

   /**
     * @Route("/api/experiences", name="app_api_experience", methods={"GET"})
     */
    public function browseExperiences(YearExperienceRepository $experiences): JsonResponse
    {
        $allExperiences = $experiences->findAll();

        return $this->json([
            $allExperiences
        ],
            200,
            [],
            [
                "groups" => ["browse_yearExperience"]
            ]
        );
    }

    /**
     * @Route("/api/experiences/{id}", name="app_api_experiences", methods={"PUT", "PATCH"})
     */
    public function edit(
        YearExperience $experiences = null,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse
    {

        // Check if the user exists
        if ($experiences === null) {
            return $this->json(["message" => "Disponibilité non trouvé"],Response::HTTP_NOT_FOUND);
        }

        // Get the request content
        $jsonContent = $request->getContent();

        // Deserialize the request content into the user object
        try {
            $serializer->deserialize(
                $jsonContent,
                YearExperience::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $experiences]
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
        $e = $validator->validate($experiences);

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
}
