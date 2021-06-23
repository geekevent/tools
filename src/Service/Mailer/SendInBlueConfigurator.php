<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;

class SendInBlueConfigurator
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getInstance(): TransactionalEmailsApi
    {
        return new TransactionalEmailsApi(
            null,
            $this->getConfiguration()
        );
    }

    private function getConfiguration(): Configuration
    {
        return Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->apiKey);
    }
}
