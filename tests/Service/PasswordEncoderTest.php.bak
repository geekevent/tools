<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Account\Account;
use App\Service\Security\PasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class PasswordEncoderTest extends KernelTestCase
{
    private static UserPasswordEncoder $passwordEncoder;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        self::getPasswordEncoder();
    }

    protected static function getPasswordEncoder(): void
    {
        /** @var UserPasswordEncoder $encoder */
        $encoder = self::$kernel->getContainer()->get('security.password_encoder');

        if (!$encoder instanceof UserPasswordEncoder) {
            throw new \InvalidArgumentException('get '.\get_class($encoder).' instead of '.UserPasswordEncoder::class);
        }

        self::$passwordEncoder = $encoder;
    }

    protected function tearDown(): void
    {
    }

    public function testEncoder(): void
    {
        $account = new Account();
        $account->setPlainPassword('bla');

        $encoder = new PasswordEncoder(self::$passwordEncoder);
        $encoder->encode($account);
        self::assertNotNull($account->getPassword());
    }
}
