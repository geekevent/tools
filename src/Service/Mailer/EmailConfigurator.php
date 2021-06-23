<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\SendSmtpEmailSender;
use SendinBlue\Client\Model\SendSmtpEmailTo;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EmailConfigurator
{
    /**
     * @param array<mixed, mixed> $data
     */
    public static function getSendSmtpEmail(array $data): SendSmtpEmail
    {
        self::check($data);

        return (new SendSmtpEmail())
            ->setTo(self::createRecipients($data['to']))
            ->setSender(self::createSender($data['from']))
            ->setSubject($data['subject'])
            ->setTextContent($data['textContent'])
            ;
    }

    /**
     * @param array<mixed> $data
     */
    private static function check(array $data): void
    {
        if (!isset($data['to'])) {
            throw new BadRequestHttpException('Le destinataire est manquant');
        }

        if (!isset($data['from'])) {
            throw new BadRequestHttpException('l\'expéditeur est manquant');
        }

        if (!isset($data['subject'])) {
            throw new BadRequestHttpException('Il manque le sujet du mail');
        }

        if (!isset($data['textContent'])) {
            throw new BadRequestHttpException('Il manque le corps du mail');
        }
    }

    /**
     * @param string|array<string, string> $sender
     */
    private static function createSender($sender): SendSmtpEmailSender
    {
        $data = self::getData($sender);

        return new SendSmtpEmailSender($data);
    }

    /**
     * @param string|array<int|string,array<string,string>|string> $recipients
     *
     * @return SendSmtpEmailTo[]
     */
    private static function createRecipients($recipients): array
    {
        $to = [];
        if (is_iterable($recipients)) {
            foreach ($recipients as $recipient) {
                $to[] = self::createRecipient($recipient);
            }

            return $to;
        }

        return [self::createRecipient($recipients)];
    }

    /**
     * @param string|array<string,string> $recipient
     */
    private static function createRecipient($recipient): SendSmtpEmailTo
    {
        $data = self::getData($recipient);

        return new SendSmtpEmailTo($data);
    }

    /**
     * @param string|array<string, string> $sender
     *
     * @return string[]
     */
    private static function getData($sender): array
    {
        if (\is_array($sender)) {
            return $sender;
        }

        return ['email' => $sender];
    }
}
