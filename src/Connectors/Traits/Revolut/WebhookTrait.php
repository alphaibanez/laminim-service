<?php

namespace Lkt\Connectors\Traits\Revolut;

use Lkt\Connectors\Enums\RevolutUrl;
use Lkt\Connectors\Enums\RevolutWebhookEvent;
use Lkt\Connectors\RevolutResponse\ErrorResponse;
use Lkt\Connectors\RevolutResponse\WebhookResponse;

trait WebhookTrait
{

    /**
     * @param string $callbackUrl
     * @param RevolutWebhookEvent[] $events
     * @return ErrorResponse|WebhookResponse|null
     * @see https://developer.revolut.com/docs/merchant/create-webhook
     */
    public function createWebhook(string $callbackUrl, array $events = []): WebhookResponse|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/1.0/webhooks";

        $postFields = [
            'url' => $callbackUrl,
            'events' => [],
        ];
        foreach ($events as $event) {
            $postFields['events'][] = $event?->value ?? $event;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                "Revolut-Api-Version: {$this->apiVersion->value}",
                "Authorization: Bearer {$this->clientSecret}"
            ],
        ]);

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($curl === false) {
            return null;
        }

        switch ($httpCode) {
            case 200:
            case 201:
                return new WebhookResponse(json_decode($result, true));

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }


    /**
     * @param string $webhookId
     * @return WebhookResponse|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/retrieve-webhook
     */
    public function getWebhook(string $webhookId): WebhookResponse|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/1.0/webhooks/{$webhookId}";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                "Revolut-Api-Version: {$this->apiVersion->value}",
                "Authorization: Bearer {$this->clientSecret}"
            ],
        ]);

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($curl === false) {
            return null;
        }

        switch ($httpCode) {
            case 200:
            case 201:
                return new WebhookResponse(json_decode($result, true));

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }
}