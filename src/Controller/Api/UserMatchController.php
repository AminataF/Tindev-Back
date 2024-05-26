<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Competence;
use App\Entity\Job;
use App\Entity\UserMatch;
use App\Repository\UserMatchRepository;
use App\Repository\AvailabilityRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class UserMatchController extends AbstractController
{
    /**
     * @Route("/api/users/{id}/matches/sent", name="app_api_user_matches_sent", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function readUserSentMatches(UserMatchRepository $userMatchRepository, int $id): JsonResponse
    {
        // Retrieve the user's matches from the database
        $userMatches = $userMatchRepository->findBy(['userMatcher' => $id]);

        // Check if the user has matches
        if (count($userMatches) === 0) {
            return $this->json(
                [
                    "message" => "L'utilisateur n'a pas envoyé de demandes de matchs"
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $userMatches,
            Response::HTTP_OK,
            [],
            [
                "groups" => ["user_match_read"]
            ]
        );
    }

    /**
     * @Route("/api/users/{id}/matches/received", name="app_api_user_matches_received", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function readUserReceivedMatches(UserMatchRepository $userMatchRepository, int $id): JsonResponse
    {
        // Retrieve the user's received matches from the database
        $userReceivedMatches = $userMatchRepository->findBy(['userMatched' => $id]);

        // Check if the user has received matches
        if (count($userReceivedMatches) === 0) {
            return $this->json(
                [
                    "message" => "L'utilisateur n'a pas de matchs reçus"
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $userReceivedMatches,
            Response::HTTP_OK,
            [],
            [
                "groups" => ["user_match_read"]
            ]
        );
    }

    /**
     * @Route("/api/users/{id}/latest-matches", name="app_api_last_user_matches", methods={"GET"})
     */
    public function lastUserMatches(UserMatchRepository $userMatchRepository, int $id): JsonResponse
    {
        // Show a list of latest matches for one user, ordered by the date of the match
        $latestMatches = $userMatchRepository->findBy(['userMatched' => $id], ['date' => 'DESC']);

        return $this->json(
            $latestMatches,
            Response::HTTP_OK,
            [],
            [
                'groups' => 'user_match_read'
            ]
        );
    }

    /**
     * @Route("/api/{id}/search", name="app_api_search", methods={"POST"})
     * @ParamConverter("user", options={"id" = "id"})
     */

    public function search(User $user, Request $request, UserRepository $userRepository, AvailabilityRepository $availabilityRepository): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);
        $userId = $user->getId(); // get the user ID from the ParamConverter

        $jobId = $jsonData['job_id'] ?? null;
        $pricing = $jsonData['pricing'] ?? null;
        $competenceIds = $jsonData['competence_ids'] ?? [];
        $availabilityId = $jsonData['availability_id'] ?? null;

        $users = $pricing !== null ? $userRepository->findByPricingLessThanOrEqual($pricing) : $userRepository->findAll();

        // Filter out the current user from the search results
        $users = array_filter($users, function ($user) use ($userId) {
            return $user->getId() !== $userId;
        });
        ;

        if (!empty($competenceIds)) {
            $users = array_filter($users, function ($user) use ($competenceIds) {
                $userCompetenceIds = array_map(function ($competence) {
                    return $competence->getId();
                }, $user->getCompetences()->toArray());

                return count(array_diff($competenceIds, $userCompetenceIds)) === 0;
            });
        }

        if ($availabilityId !== null) {
            $users = array_filter($users, function ($user) use ($availabilityId) {
                $availability = $user->getAvailability();
                return $availability !== null && $availability->getId() === $availabilityId;
            });
        }

        if ($jobId !== null) {
            $users = array_filter($users, function ($user) use ($jobId) {
                $job = $user->getJob();
                return $job !== null && $job->getId() === $jobId;
            });
        }

        $resultArray = [];

        foreach ($users as $user) {
            $resultArray[] = [
                'user_id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'profile_picture' => $user->getProfilePicture(),
                'availability' => $user->getAvailability(),
                'competences' => $user->getCompetences(),
                'job' => $user->getJob(),
                'pricing' => $user->getPricing(),
            ];
        }

        if (empty($resultArray)) {
            return $this->json(['message' => 'Aucun matchs trouvés']);
        }

        return $this->json(['results' => $resultArray], Response::HTTP_OK, [], ['groups' => 'user_read']);
    }

    /**
     * @Route("/api/{id}/search/results", name="app_api_match", methods={"POST"})
     * @ParamConverter("user", class="App\Entity\User", options={"id" = "id"})
     */
    public function match(Request $request, EntityManagerInterface $entityManager, UserMatchRepository $userMatchRepository, User $user): JsonResponse
    {
        // Get the JSON data from the request
        $jsonData = json_decode($request->getContent(), true);

        // Get the IDs of the users to match
        $userMatcherId = $user->getId();
        $userMatchedId = $jsonData['user_matched_id'];

        // Get the user objects from the database
        $userMatcher = $entityManager->getRepository(User::class)->find($userMatcherId);
        $userMatched = $entityManager->getRepository(User::class)->find($userMatchedId);

        // Check if both users exist
        if (!$userMatcher || !$userMatched) {
            return $this->json(['message' => 'Un ou plusieurs utilisateurs n\'existent pas'], Response::HTTP_NOT_FOUND);
        }

       // Check if there is a pending match already existing between the users (initiator -> receiver)
      $existingPendingMatch = $userMatchRepository->findMatchByStatusAndInitiatorReceiver('en attente', $userMatcher, $userMatched);

        // If the match already exists, return an error
        if ($existingPendingMatch) {
            return $this->json(['message' => 'Vous avez déjà envoyé une demande de match à cet utilisateur'], Response::HTTP_CONFLICT);
        }

        // Check if there is a match already existing between the users
        $existingMatchAccepted = $userMatchRepository->findMatchByStatusAndUsers('en cours', $userMatcher, $userMatched);

        // If the match already exists and is accepted, return an error
        if ($existingMatchAccepted) {
            return $this->json(['message' => 'Un match existe déjà et est en cours'], Response::HTTP_CONFLICT);
        }

        // Check if there is a pending match between the users
        $existingMatchPending = $userMatchRepository->findMatchByStatusAndUsers('en attente', $userMatcher, $userMatched);

        // If a pending match exists, update the match status to "en cours" and create a new match in the opposite direction
        if ($existingMatchPending) {
            $existingMatchPending->setStatus('en cours');
            $newMatch = new UserMatch();
            $newMatch->setStatus('en cours');
            $newMatch->setUserMatcher($userMatcher); // Swap userMatcher and userMatched
            $newMatch->setUserMatched($userMatched); // Swap userMatcher and userMatched
            $newMatch->setDate(new \DateTime());
            $entityManager->persist($existingMatchPending);
            $entityManager->persist($newMatch);
            $entityManager->flush();
            return $this->json($newMatch, Response::HTTP_CREATED, [], ['groups' => 'user_match_read']);
        }

        // If there is no pending or accepted match, create a new pending match
        $newMatch = new UserMatch();
        $newMatch->setStatus('en attente');
        $newMatch->setUserMatcher($userMatcher);
        $newMatch->setUserMatched($userMatched);
        $newMatch->setDate(new \DateTime());
        $entityManager->persist($newMatch);
        $entityManager->flush();
        return $this->json($newMatch, Response::HTTP_CREATED, [], ['groups' => 'user_match_read']);
    }


    /**
     * @Route("/api/users/matches", name="app_user_match_update_status", methods={"PUT"})
     */

    public function updateMatchStatus(Request $request, EntityManagerInterface $entityManager, UserMatchRepository $userMatchRepository): JsonResponse
    {
        // Get the JSON data from the request
        $jsonData = json_decode($request->getContent(), true);

        // Get the ID of the UserMatch object from the request data
        $userMatchId = $jsonData['match_id'];

        // Get the UserMatch object from the database
        $userMatch = $userMatchRepository->find($userMatchId);

        // Check if the UserMatch object exists
        if (!$userMatch) {
            return $this->json(['message' => 'Le match n\'existe pas'], Response::HTTP_NOT_FOUND);
        }

        // Update the status of the UserMatch object
        $userMatch->setStatus($jsonData['status']);

        // Update the UserMatch object in the database
        $entityManager->flush();
        

        // Return the updated UserMatch object as a JSON response
        return $this->json($userMatch, Response::HTTP_OK, [], ['groups' => 'user_match_read']);
    }


}
