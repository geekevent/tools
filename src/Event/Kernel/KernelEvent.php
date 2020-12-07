<?php

declare(strict_types=1);

namespace App\Event\Kernel;

use App\Entity\Account\Account;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class KernelEvent implements EventSubscriberInterface
{
    private ManagerRegistry $registry;

    private TokenStorageInterface $storage;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        ManagerRegistry $registry,
        TokenStorageInterface $storage,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->registry = $registry;
        $this->storage = $storage;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelController',
        ];
    }

    public function onKernelController(RequestEvent $event): void
    {
        $token = $this->storage->getToken();
        if (null === $token) {
            return;
        }

        $user = $token->getUser();
        if (!$user instanceof Account) {
            return;
        }

        $user = $this->refreshUser($user);
        $endDate = $user->getEndDate();
        if ($user->isValid() && (!$endDate || $endDate >= new \DateTime())) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_logout')));
    }

    private function refreshUser(Account $user): Account
    {
        $account = $this->getAccountRepository()->find($user->getId());
        if (null === $account || !$account instanceof Account) {
            throw new \InvalidArgumentException('Unable to find user');
        }

        return $account;
    }

    /**
     * @return ObjectRepository<Account>
     */
    private function getAccountRepository(): ObjectRepository
    {
        return $this->registry->getRepository(Account::class);
    }
}
