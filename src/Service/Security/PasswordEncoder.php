<?php

declare(strict_types=1);

namespace App\Service\Security;

use App\Entity\Account\Account;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoder
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function encode(Account $account): void
    {
        $plainPassword = $account->getPlainPassword();
        if (null === $plainPassword) {
            return;
        }

        $password = $this->encoder->encodePassword($account, $plainPassword);

        $account
            ->setPassword($password)
            ->eraseCredentials()
        ;
    }
}
