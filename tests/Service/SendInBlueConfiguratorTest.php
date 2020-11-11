<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\Mailer\EmailConfigurator;
use App\Service\Mailer\SendInBlueConfigurator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SendInBlueConfiguratorTest extends TestCase
{
    public function testGetConfig(): void
    {
        $configurator = new SendInBlueConfigurator('api-key');
        $instance = $configurator->getInstance();
        self::assertEquals('api-key', $instance->getConfig()->getApiKey('api-key'));
    }

    public function testSendSmtpEmail(): void
    {
        $email = EmailConfigurator::getSendSmtpEmail([
            'from' => 'no-reply@geekevent.fr',
            'to' => 'test@test.test',
            'subject' => 'subject',
            'textContent' => 'content',
        ]);

        self::assertEquals('no-reply@geekevent.fr', $email->getSender()->getEmail());
        self::assertEquals('test@test.test', $email->getTo()[0]->getEmail());
        self::assertEquals('subject', $email->getSubject());
        self::assertEquals('content', $email->getTextContent());
    }

    public function testSendComplexSmtpEmail(): void
    {
        $email = EmailConfigurator::getSendSmtpEmail([
            'from' => [
                'email' => 'no-reply@geekevent.fr',
                'name' => 'Geek Event',
            ],
            'to' => [
                'test@test.test',
            ],
            'subject' => 'subject',
            'textContent' => 'content',
        ]);

        self::assertEquals('no-reply@geekevent.fr', $email->getSender()->getEmail());
        self::assertEquals('test@test.test', $email->getTo()[0]->getEmail());
        self::assertEquals('subject', $email->getSubject());
        self::assertEquals('content', $email->getTextContent());
    }

    public function testSendComplexRecipientSmtpEmail(): void
    {
        $email = EmailConfigurator::getSendSmtpEmail([
            'from' => [
                'email' => 'no-reply@geekevent.fr',
                'name' => 'Geek Event',
            ],
            'to' => [
                [
                    'email' => 'test@test.test',
                    'name' => 'test',
                ],
            ],
            'subject' => 'subject',
            'textContent' => 'content',
        ]);

        self::assertEquals('no-reply@geekevent.fr', $email->getSender()->getEmail());
        self::assertEquals('test@test.test', $email->getTo()[0]->getEmail());
        self::assertEquals('test', $email->getTo()[0]->getName());
        self::assertEquals('subject', $email->getSubject());
        self::assertEquals('content', $email->getTextContent());
    }

    public function testExpectExceptions(): void
    {
        self::expectException(BadRequestHttpException::class);

        EmailConfigurator::getSendSmtpEmail([
            'from' => 'no-reply@geekevent.fr',
            'to' => 'test@test.test',
            'textContent' => 'content',
        ]);
    }
}
