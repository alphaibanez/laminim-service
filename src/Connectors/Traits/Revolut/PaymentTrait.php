<?php

namespace Lkt\Connectors\Traits\Revolut;

use Lkt\Connectors\Enums\RevolutUrl;
use Lkt\Connectors\RevolutResponse\ErrorResponse;
use Lkt\Connectors\RevolutResponse\OrderResponse;
use Lkt\Connectors\RevolutResponse\PaymentResponse;

trait PaymentTrait
{
    /**
     * @param string $paymentId
     * @return PaymentResponse|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/retrieve-payment-details
     */
    public function getPayment(string $paymentId): PaymentResponse|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/payments/{$paymentId}";

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
                return new PaymentResponse(json_decode($result, true));

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }
}