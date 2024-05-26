<?php

namespace App\Controller\Api;

use App\Entity\Availability;
use App\Repository\AvailabilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AvailabilityController extends AbstractController
{
    /**
     * @Route("/api/availability", name="app_api_availability", methods={"GET"})
     */
    public function browseAvaibility(AvailabilityRepository $availabilityRepository): JsonResponse
    {
        $allAvailability = $availabilityRepository->findAll();

        return $this->json([
            $allAvailability
        ],
            200,
            [],
            [
                "groups" => ["browse_availability"]
            ]
        );
    }

    /**
     * @Route("/api/availability/{id}", name="app_api_availability_add", methods={"PUT", "PATCH"})
     */
    public function edit(
        Availability $availability = null,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse
    {

        // Check if the user exists
        if ($availability === null) {
            return $this->json(["message" => "Disponibilité non trouvé"],Response::HTTP_NOT_FOUND);
        }

        // Get the request content
        $jsonContent = $request->getContent();

        // Deserialize the request content into the user object
        try {
            $serializer->deserialize(
                $jsonContent,
                Availability::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $availability]
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
        $e = $validator->validate($availability);

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
