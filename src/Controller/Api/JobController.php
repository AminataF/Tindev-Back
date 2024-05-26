<?php

namespace App\Controller\Api;

use App\Entity\Job;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JobController extends AbstractController
{
   
    /**
     * @Route("/api/jobs", name="app_api_job", methods={"GET"})
     */
    public function browseJobs(JobRepository $jobRepository): JsonResponse
    {
        $allJobs = $jobRepository->findAll();

        return $this->json([
            $allJobs
        ],
            200,
            [],
            [
                "groups" => ["browse_job"]
            ]
        );
    }

    /**
     * @Route("/api/jobs/{id}", name="app_api_availability_add", methods={"PUT", "PATCH"})
     */
    public function edit(
        Job $job = null,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse
    {

        // Check if the user exists
        if ($job === null) {
            return $this->json(["message" => "Disponibilité non trouvé"],Response::HTTP_NOT_FOUND);
        }

        // Get the request content
        $jsonContent = $request->getContent();

        // Deserialize the request content into the user object
        try {
            $serializer->deserialize(
                $jsonContent,
                Job::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $job]
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
        $e = $validator->validate($job);

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
