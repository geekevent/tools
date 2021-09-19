<?php


namespace App\Command;

use App\Entity\User;
use App\Mailer\Mail\AccountCreated;
use App\Mailer\SendInBlueMailer;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Service\Attribute\Required;

class CreateAdmin extends Command
{
    protected static $defaultName = 'geekevents:user:admin';

    public const DEFAULT_ADMIN = 'technique@geekevent.fr';

    private UserRepository $repository;

    private SendInBlueMailer $mailer;

    private AccountCreated $mail;

    private ?string $baseUrl;

    public function __construct(UserRepository $repository, AccountCreated $mail, ?string $baseUrl = null)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->mail = $mail;
        $this->baseUrl = $baseUrl;
    }

    #[Required]
    public function setMailer(SendInBlueMailer $mailer): void
    {
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->repository->findOneBy([
            'login' => self::DEFAULT_ADMIN,
        ]);

        if (null !== $user) {
            return 0;
        }

        $user = new User();
        $user->login = self::DEFAULT_ADMIN;
        $user->roles = [User::ROLE_ADMIN];
        $user->givenName = 'Super';
        $user->familyName = 'admin';

        $mailData = $this->getMailData($user);

        $this->repository->persist($user);
        $this->repository->flush();

        $this->mailer->send($mailData);

        return 0;
    }

    private function getMailData(User $user): array
    {
        return $this->mail->createMailData($user, $this->baseUrl);
    }
}