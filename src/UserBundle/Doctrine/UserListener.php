<?php

namespace UserBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use UserBundle\Entity\User;

class UserListener
{
    private $encoderFactory;

    public function __construct(EncoderFactory $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $this->handleEvent($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $this->handleEvent($entity);
        }
    }

    private function handleEvent(User $user)
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $plainPassword = $user->getPlainPassword();
        $encoder = $this->encoderFactory
            ->getEncoder($user);

        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
    }
}