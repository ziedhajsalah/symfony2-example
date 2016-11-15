<?php

namespace EventBundle\Controller;

use EventBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Controller extends BaseController
{
    public function getSecurityAuthorizationChecker()
    {
        return $this->container->get('security.authorization_checker');
    }

    public function getSecurityTokenStorage()
    {
        return $this->container->get('security.token_storage');
    }

    /**
     * @param Event $event
     */
    public function enforceOwnerSecurity(Event $event)
    {
        if ($this->getUser() != $event->getOwner()) {
            throw new AccessDeniedException('Your are not the owner!');
        }
    }

    /**
     * Checks if the user is authenticated and has ROLE_USER or throw an Exception
     * @param string $role
     */
    public function enforceUserSecurity($role = 'ROLE_USER') {
        if(!$this->getSecurityAuthorizationChecker()->isGranted($role)) {
            throw new AccessDeniedException(
                'you need ' . $role . 'to access this page'
            );
        }
    }
}