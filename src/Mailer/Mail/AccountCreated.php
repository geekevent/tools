<?php


namespace App\Mailer\Mail;


use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;

class AccountCreated
{

    private RouterInterface $router;

    private string $from;

    public function __construct(RouterInterface $router, string $defaultFromMailer)
    {
        $this->router = $router;
        $this->from = $defaultFromMailer;
    }

    public function createMailData(User $user, ?string $baseUrl = null): array
    {
        $tokenGenerator = new UriSafeTokenGenerator();
        $token = $tokenGenerator->generateToken();
        $route = $this->generateRoute($baseUrl, $token);

        $user->resetToken = $token;

        return [
            'to' => [$user->login],
            'from' => $this->from,
            'subject' => 'Création de compte',
            'body' => 'cliquez ici pour activer votre compte :'.$route,
        ];
    }

    /**
     * @param string|null $baseUrl
     * @param string $token
     * @return string
     */
    private function generateRoute(?string $baseUrl, string $token): string
    {
        if (null === $baseUrl) {
            return $this->router->generate(
                'user_password_reset',
                [
                    'token' => $token,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        $path = $this->router->generate(
            'user_password_reset',
            [
                'token' => $token,
            ],
            UrlGeneratorInterface::ABSOLUTE_PATH
        );

        return sprintf('%s%s', $baseUrl, $path);

    }
}