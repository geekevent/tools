<?php

namespace App\Tests\Command;

use App\Command\CreateAdmin;
use App\Entity\User;
use App\Mailer\Mail\AccountCreated;
use App\Mailer\SendInBlueMailer;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminTest extends WebTestCase
{
    public function testCreateUser()
    {
        self::bootKernel();
        /** @var UserRepository $repository */
        $repository = self::getContainer()->get(UserRepository::class);
        /** @var AccountCreated $mail */
        $mail = self::getContainer()->get(AccountCreated::class);
        $command = new CreateAdmin($repository, $mail);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        $mailer = $this->createMock(SendInBlueMailer::class);
        $mailer->expects($this->once())
            ->method('send');

        $command->setMailer($mailer);

        $command->run($input, $output);



        /** @var User|null $user */
        $user = $repository->findOneBy(['login' => CreateAdmin::DEFAULT_ADMIN]);

        self::assertNotNull($user);

        $repository->remove($user);
        $repository->flush();
    }

    public function testCreateUserWithExistingOne()
    {
        self::bootKernel();
        /** @var UserRepository $repository */
        $repository = self::getContainer()->get(UserRepository::class);
        /** @var AccountCreated $mail */
        $mail = self::getContainer()->get(AccountCreated::class);
        $command = new CreateAdmin($repository, $mail);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        $mailer = $this->createMock(SendInBlueMailer::class);
        $mailer->expects($this->once())
            ->method('send');

        $command->setMailer($mailer);
        $command->run($input, $output);

        $command->run($input, $output);

        /** @var User[] $users */
        $users = $repository->findBy(['login' => CreateAdmin::DEFAULT_ADMIN]);

        self::assertCount(1, $users);

        foreach ($users as $user) {
            $repository->remove($user);
        }
        $repository->flush();
    }
}
