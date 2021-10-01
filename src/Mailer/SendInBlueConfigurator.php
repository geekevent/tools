<?php


namespace App\Mailer;


use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;

class SendInBlueConfigurator
{
    private string $apiKey;

    public function __construct(string $sendInBlueApiKey)
    {
        $this->apiKey = $sendInBlueApiKey;
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