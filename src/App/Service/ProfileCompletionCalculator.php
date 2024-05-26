<?php

namespace App\Service;

use App\Entity\User;

class ProfileCompletionCalculator
{
    public function calculateProfileCompletion(User $user): float
{
    $totalWeight = 0;
    $completedWeight = 0;

    // Check if user has provided basic information
    if (!empty($user->getFirstname()) && !empty($user->getEmail()) && !empty($user->getLastname())) {
        $totalWeight += 15;
        $completedWeight += 15;
    } else {
        $totalWeight += 15;
    }

    // Check if user has provided bio
    if (!empty($user->getDescription())) {
        $totalWeight += 20;
        $completedWeight += 20;
    } else {
        $totalWeight += 20;
    }

    // Check if user has provided his job
    if (!empty($user->getJob())) {
        $totalWeight += 15;
        $completedWeight += 15;
    } else {
        $totalWeight += 15;
    }

    // Check if user has provided competences
    if (!empty($user->getCompetences()->toArray())) {
        $totalWeight += 20;
        $completedWeight += 20;
    } else {
        $totalWeight += 20;
    }

    // Check if user has provided years experience
    if (!empty($user->getYearExp())) {
        $totalWeight += 15;
        $completedWeight += 15;
    } else {
        $totalWeight += 15;
    }

     // Check if user has provided availability
     if (!empty($user->getAvailability())) {
        $totalWeight += 15;
        $completedWeight += 15;
    } else {
        $totalWeight += 15;
    }

    // Calculate percentage of completion
    if ($totalWeight > 0) {
        return round(($completedWeight / $totalWeight) * 100, 0);
    } else {
        return 0;
    }
}

}
