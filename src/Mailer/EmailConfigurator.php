<?php


namespace App\Mailer;


use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\SendSmtpEmailSender;
use SendinBlue\Client\Model\SendSmtpEmailTo;

class EmailConfigurator
{
    public static function getSendSmtpEmail(array $data): SendSmtpEmail
    {
        self::check($data);

        return (new SendSmtpEmail())
            ->setTo(self::createRecipients($data['to']))
            ->setSender(self::createSender($data['from']))
            ->setSubject($data['subject'])
            ->setTextContent($data['body'])
            ;
    }

    private static function check(array $data): void
    {
        if (!isset($data['to']) || !is_array($data['to'])) {
            throw new \InvalidArgumentException('"to" must be an array of recipient');
        }

        if (!isset($data['from'])) {
            throw new \InvalidArgumentException('"from" must be an email or an array with sender configuration');
        }

        if (!isset($data['subject'])) {
            throw new \InvalidArgumentException('"subject" must be a string');
        }

        if (!isset($data['body'])) {
            throw new \InvalidArgumentException('"body" must be a string');
        }
    }

    private static function createRecipients(array $recipients): array
    {
        $to = [];
        foreach ($recipients as $recipient) {
            $to[] = self::createRecipient($recipient);
        }

        return $to;
    }

    private static function createRecipient(string|array $data): SendSmtpEmailTo
    {
        $recipient = self::getData($data);

        return new SendSmtpEmailTo($recipient);
    }

    private static function createSender(string|array $data): SendSmtpEmailSender
    {
        return new SendSmtpEmailSender(self::getData($data));
    }

    private static function getData(string|array $data): array
    {
        if (is_array($data)) {
            return $data;
        }

        return ['email' => $data];
    }
}