<?php

declare(strict_types=1);

namespace App\Service\Mailer;

class Mailer
{
    private SendInBlueConfigurator $configurator;

    public function __construct(SendInBlueConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    /**
     * @param array<string|int, mixed> $data
     *
     * @throws \SendinBlue\Client\ApiException
     */
    public function sendSendSmtpEmail(array $data): void
    {
        $instance = $this->configurator->getInstance();

        $instance->sendTransacEmail(EmailConfigurator::getSendSmtpEmail($data));
    }
}
