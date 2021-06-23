<?php

declare(strict_types=1);

namespace App\Service\Email;

use App\Entity\Account\Account;
use App\Service\Mailer\Mailer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;

class AccountCreatedEmail
{
    private Mailer $mailer;

    private RouterInterface $router;

    public function __construct(Mailer $mailer, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    /**
     * @return array<mixed>
     */
    public function createMailData(Account $account): array
    {
        $tokenGenerator = new UriSafeTokenGenerator();
        $token = $tokenGenerator->generateToken();
        $route = $this->router->generate(
            'app_set',
            [
                'token' => $token,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $account->setResetToken($token);

        return [
            'to' => $account->getLogin(),
            'from' => 'no-reply@geekevent.fr',
            'subject' => 'création de compte',
            'textContent' => 'cliquez ici pour activer votre compte :'.$route,
        ];
    }

    /**
     * @param array<mixed> $data
     *
     * @throws \SendinBlue\Client\ApiException
     */
    public function send(array $data): void
    {
        $this->mailer->sendSendSmtpEmail($data);
    }
}
