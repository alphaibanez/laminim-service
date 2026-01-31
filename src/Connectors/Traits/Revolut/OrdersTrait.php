<?php

namespace Lkt\Connectors\Traits\Revolut;

use Lkt\Connectors\Enums\RevolutUrl;
use Lkt\Connectors\RevolutResponse\ErrorResponse;
use Lkt\Connectors\RevolutResponse\OrderResponse;

trait OrdersTrait
{

    /**
     * @param array $payload
     * @return OrderResponse|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/create-order
     */
    public function createOrder(array $payload): OrderResponse|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/orders";

        $postFields = json_encode($payload);

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
                return new OrderResponse(json_decode($result, true));

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }


    /**
     * @param string $orderId
     * @return OrderResponse|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/retrieve-order
     */
    public function getOrder(string $orderId): OrderResponse|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/orders/{$orderId}";

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
                return new OrderResponse(json_decode($result, true));

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }


    /**
     * @param string $orderId
     * @param array $payload
     * @return OrderResponse|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/update-order
     */
    public function updateOrder(string $orderId, array $payload): OrderResponse|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/orders/{$orderId}";

        $postFields = json_encode($payload);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
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
                return new OrderResponse(json_decode($result, true));

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }


    /**
     * @param string $orderId
     * @return OrderResponse|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/refund-order
     */
    public function refundOrder(string $orderId): OrderResponse|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/orders/{$orderId}/refund";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
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
                return new OrderResponse(json_decode($result, true));

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }


    /**
     * @param string $orderId
     * @return array|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/retrieve-payment-list
     */
    public function getOrderPayments(string $orderId): array|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/orders/{$orderId}/payments";

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
                return json_decode($result, true);

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }
}