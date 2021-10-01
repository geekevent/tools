<?php


namespace App\Event;


use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSubscriber implements EventSubscriber
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }


    public function getSubscribedEvents()
    {
        return [Events::preUpdate];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $user = $args->getEntity();
        if (!$user instanceof User || !isset($user->plainPassword) || null === $user->plainPassword) {
            return;
        }

        $password = $this->hasher->hashPassword($user, $user->plainPassword);
        $user->password = $password;
    }
}