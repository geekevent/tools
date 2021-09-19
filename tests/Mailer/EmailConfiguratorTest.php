<?php

namespace App\Tests\Mailer;

use App\Mailer\EmailConfigurator;
use PHPUnit\Framework\TestCase;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\SendSmtpEmailSender;
use SendinBlue\Client\Model\SendSmtpEmailTo;

class EmailConfiguratorTest extends TestCase
{
    public function testCreateSmtpEmail()
    {
        $email = EmailConfigurator::getSendSmtpEmail([
            'to' => ['admin@admin.com'],
            'from' => ['noreply@geekevent.fr'],
            'subject' => 'subject',
            'body' => 'body',
        ]);

        self::assertInstanceOf(SendSmtpEmail::class, $email);
        self::assertCount(1, $email->getTo());
        foreach ($email->getTo() as $recipient) {
            self::assertInstanceOf(SendSmtpEmailTo::class, $recipient);
        }

        self::assertInstanceOf(SendSmtpEmailSender::class, $email->getSender());
        self::assertEquals('subject', $email->getSubject());
        self::assertEquals('body', $email->getTextContent());
    }

    public function testCreateSmtpEmailWithoutTo()
    {
        self::expectException(\InvalidArgumentException::class);
        EmailConfigurator::getSendSmtpEmail([]);
    }

    public function testCreateSmtpEmailWithoutSender()
    {
        self::expectException(\InvalidArgumentException::class);
        EmailConfigurator::getSendSmtpEmail([
            'to' => ['admin@admin.com']
        ]);
    }

    public function testCreateSmtpEmailWithoutSubject()
    {
        self::expectException(\InvalidArgumentException::class);
        EmailConfigurator::getSendSmtpEmail([
            'to' => ['admin@admin.com'],
            'from' => 'foo@bar.com',
        ]);
    }

    public function testCreateSmtpEmailWithoutBody()
    {
        self::expectException(\InvalidArgumentException::class);
        EmailConfigurator::getSendSmtpEmail([
            'to' => ['admin@admin.com'],
            'from' => 'foo@bar.com',
            'subject' => 'foobar'
        ]);
    }
}
