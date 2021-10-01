<?php


namespace App\Mailer;


class SendInBlueMailer
{
    private SendInBlueConfigurator $configurator;

    public function __construct(SendInBlueConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function send(array $data): void
    {
        $instance = $this->configurator->getInstance();
        $instance->sendTransacEmail(EmailConfigurator::getSendSmtpEmail($data));
    }
}