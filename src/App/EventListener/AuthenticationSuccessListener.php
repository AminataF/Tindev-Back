<?php

namespace App\EventListener;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * 
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();
        $userEmail = $user->getUserIdentifier();

        if (!$user instanceof UserInterface) {
            return;
        }

        $currentUser = $this->userRepository->findOneBy(['email' => $userEmail]);
        $data['data'] = array(
            'userId' => $currentUser->getId(),
            'userFirstname' => $currentUser->getFirstname()
        );

        $event->setData($data);
    }

}