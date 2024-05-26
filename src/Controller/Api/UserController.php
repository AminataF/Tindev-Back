<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\Job;
use App\Service\ProfileCompletionCalculator;
use App\Repository\JobRepository;
use App\Repository\CompetenceRepository;
use App\Repository\AvailabilityRepository;
use App\Repository\YearExperienceRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Security\Voter\UserProfileAccessVoter;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="app_api_users", methods={"GET"})
     */
    public function browse(UserRepository $userRepository): JsonResponse
    {

        // BDD, injection repository, browse all users
        $allUser = $userRepository->findAll();


        return $this->json(
            $allUser,
            Response::HTTP_OK,
            [],
            [
                "groups" =>
                [
                    "user_browse"
                ]
            ]
        );
    }

    /**
     * @Route("/api/users/{id}", name="app_api_user_read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(UserRepository $userRepository, int $id, UserProfileAccessVoter $voter, ProfileCompletionCalculator $profileCompletionCalculator): JsonResponse
    {
        // Retrieve the user's information from the database
        $user = $userRepository->find($id);

        // Check if the user exists
        if (!$user) {
            return $this->json(
                [
                    "message" => "Utilisateur non trouvé"
                ],
                Response::HTTP_OK,
            );
        }

        // Check if the current user has access to the requested user's profile
        $this->denyAccessUnlessGranted(UserProfileAccessVoter::VIEW, $user);

        // Calculate the user's profile completion percentage
        $profileCompletion = $profileCompletionCalculator->calculateProfileCompletion($user);

        return $this->json(
            [
                $user,
                $profileCompletion,
            ],
            Response::HTTP_OK,
            [],
            [
                "groups" => ["user_read"]
            ]
        );
    }

    /**
     * @Route("/api/users/{id}", name="app_api_user_update", methods={"PUT", "PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(
        int $id,
        UserRepository $userRepository,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserProfileAccessVoter $voter
    ): JsonResponse {
        // Retrieve the user's information from the database
        $user = $userRepository->find($id);

        // Check if the user exists
        if (!$user) {
            return $this->json(
                [
                    "message" => "Utilisateur non trouvé"
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        // Check if the current user has access to the requested user's profile
        $this->denyAccessUnlessGranted(UserProfileAccessVoter::VIEW, $user);

        // Get the request content
        $jsonContent = $request->getContent();

        // Deserialize the request content into the user object
        try {
            $serializer->deserialize(
                $jsonContent,
                User::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
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
        $e = $validator->validate($user);

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
     * @Route("/api/users/profile/{id}", name="app_api_user_read_profile", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function userProfile(UserRepository $userRepository, int $id): JsonResponse
    {
        // Retrieve the user's information from the database
        $user = $userRepository->find($id);

        // Check if the user exists
        if (!$user) {
            return $this->json(
                [
                    "message" => "Utilisateur non trouvé"
                ],
                Response::HTTP_OK,
            );
        }


        return $this->json(
            [$user],
            Response::HTTP_OK,
            [],
            [
                "groups" => ["user_profile"]
            ]
        );
    }


    /**
     * @Route("/api/users", name="app_api_user_add", methods={"POST"})
     */
     public function add(
         Request $request,
         SerializerInterface $serializer,
         ValidatorInterface $validator,
         EntityManagerInterface $entityManager
     ) {
         $jsonData = $request->getContent();

         // Deserialize the JSON payload into a User object
         $user = $serializer->deserialize(
             $jsonData,
             User::class,
             'json'
         );

         // Validate the User object
         $e = $validator->validate($user);
         if (count($e) > 0) {
             return $this->json($e, Response::HTTP_BAD_REQUEST);
         }

         // Save the new User object to the database
         $entityManager->persist($user);
         $entityManager->flush();

         // Return the newly created User object to the client
         return $this->json($user, Response::HTTP_CREATED, [], [
             'groups' => ['user_read']
         ]);
     }

    /**
     * @Route("/api/users/{id}", name="app_api_user_delete", requirements={"id"="\d+"}, methods={"DELETE"}, requirements={"id"="\d+"})
      */
    public function delete(User $user = null, EntityManagerInterface $entityManager, UserProfileAccessVoter $voter)
    {

        if ($user === null) {
            // paramConverter didn't find the user entity : 404
            return $this->json("Utilisateur non trouvé", Response::HTTP_NOT_FOUND);
        }

        // Check if the current user has access to the requested user's profile
        $this->denyAccessUnlessGranted(UserProfileAccessVoter::VIEW, $user);


        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

     /**
        * @Route("/api/latest-users", name="app_api_last_created_users", methods={"GET"})
         */
    public function lastCreatedUsers(UserRepository $userRepository): JsonResponse
    {
        // Show a list of users, ordered by their date of creation
        $lastCreatedUsers = $userRepository->findBy([], ['createdAt' => 'DESC'], 5);

        return $this->json(
            $lastCreatedUsers,
            200,
            [],
            [
                'groups' => 'latest_users'
            ]
        );
    }

    /**
         * @Route("/api/inscription", name="app_api_inscription", methods={"POST"})
         */
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasherInterface
    ) {
        $data = $request->getContent();

        $user = $serializer->deserialize($data, User::class, 'json');
        $password = $user->getPassword();

        $hashedpassword = $userPasswordHasherInterface->hashPassword($user, $password);

        $user->setPassword($hashedpassword);

        // Set the created_at field to the current datetime
        $user->setCreatedAt(new \DateTime());

        // Assign the ROLE_USER role to the user
        $user->setRoles(["ROLE_USER"]);

        // Check if the email is already in use
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
        if ($existingUser) {
            return new Response('Email déjà utilisé', 400);
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('User created', 201);
    }

        /**
     * @Route("/api/users/{id}/password", name="app_api_update_password", methods={"PUT"}, requirements={"id"="\d+"})
     */
        public function updateUserPassword(
            Request $request,
            User $user,
            UserPasswordHasherInterface $userPasswordHasherInterface,
            EntityManagerInterface $entityManager,
            UserProfileAccessVoter $voter
        ) {

            // Check if the current user has access to the requested user's profile
            $this->denyAccessUnlessGranted(UserProfileAccessVoter::VIEW, $user);

            $data = json_decode($request->getContent(), true);
            $oldPassword = $data['old_password'];
            $newPassword = $data['new_password'];


            // Verify that the provided old password matches the user's current password
            if (!$userPasswordHasherInterface->isPasswordValid($user, $oldPassword)) {
                return new Response('Ancien mot de passe invalide', 400);
            }

            $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $newPassword);

            $user->setPassword($hashedPassword);

            $entityManager->flush();

            return new Response('Nouveau mot de passe enregistré', 200);
        }


}
