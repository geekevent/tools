<?php

declare(strict_types=1);

namespace App\Event\Doctrine\Account;

use App\Entity\Account\Account;
use App\Service\Email\AccountCreatedEmail;
use App\Service\Security\PasswordEncoder;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;

class AccountSubscriber implements EventSubscriber
{
    /**
     * @var array<int, mixed>
     */
    private array $emails = [];

    private AccountCreatedEmail $email;

    private PasswordEncoder $passwordEncoder;

    public function __construct(AccountCreatedEmail $email, PasswordEncoder $passwordEncoder)
    {
        $this->email = $email;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getSubscribedEvents()
    {
        return [Events::prePersist, Events::postFlush, Events::preUpdate];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Account) {
            return;
        }

        $this->emails[] = $this->email->createMailData($object);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Account) {
            return;
        }

        $this->passwordEncoder->encode($object);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->emails) || 'test' === $_ENV['APP_ENV']) {
            return;
        }

        foreach ($this->emails as $email) {
            $this->email->send($email);
        }
    }
}
