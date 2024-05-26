<?php

namespace App\Security\Voter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProfileAccessVoter extends Voter
{
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->getId() === $subject->getId();
    }
}
