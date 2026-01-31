<?php

namespace Lkt\Connectors\Traits\Revolut;

use Lkt\Connectors\Enums\RevolutUrl;
use Lkt\Connectors\RevolutResponse\CustomerResponse;
use Lkt\Connectors\RevolutResponse\ErrorResponse;

trait CustomerTrait
{

    /**
     * @param array $payload
     * @return CustomerResponse|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/create-customer
     */
    public function createCustomer(array $payload): CustomerResponse|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/1.0/customers";

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
                return new CustomerResponse(json_decode($result, true));

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }


    /**
     * @param string $customerId
     * @return CustomerResponse|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/retrieve-customer
     */
    public function getCustomer(string $customerId): CustomerResponse|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/1.0/customers/{$customerId}";

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
                return new CustomerResponse(json_decode($result, true));

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }


    /**
     * @param string $customerId
     * @param array $payload
     * @return CustomerResponse|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/update-customer
     */
    public function updateCustomer(string $customerId, array $payload): CustomerResponse|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/1.0/customers/{$customerId}";

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
                return new CustomerResponse(json_decode($result, true));

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }


    /**
     * @return CustomerResponse[]|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/retrieve-all-customers
     */
    public function listCustomers(): array|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/1.0/customers";

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
                $data = json_decode($result, true);
                $r = [];
                foreach ($data as $customer) {
                    $r[] = new CustomerResponse($customer);
                }
                return $r;

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }


    /**
     * @param string $customerId
     * @param array $payload
     * @return ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/delete-customer
     */
    public function deleteCustomer(string $customerId, array $payload): ErrorResponse|bool
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/1.0/customers/{$customerId}";

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
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => [
                "Revolut-Api-Version: {$this->apiVersion->value}",
                "Authorization: Bearer {$this->clientSecret}"
            ],
        ]);

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($curl === false) {
            return false;
        }

        switch ($httpCode) {
            case 200:
            case 201:
            case 204:
                return true;

            default:
                return new ErrorResponse(json_decode($result, true));
        }
    }


    /**
     * @param string $customerId
     * @return array|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/retrieve-all-payment-methods
     */
    public function getCustomerPaymentMethods(string $customerId): array|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/1.0/customers/{$customerId}/payment-methods";

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


    /**
     * @param string $customerId
     * @param string $paymentMethodId
     * @return array|ErrorResponse|null
     * @see https://developer.revolut.com/docs/merchant/retrieve-payment-method
     */
    public function getCustomerPaymentMethod(string $customerId, string $paymentMethodId): array|ErrorResponse|null
    {
        $url = $this->sandbox ? RevolutUrl::SandboxMerchantAPI->value : RevolutUrl::MerchantAPI->value;
        $url = "{$url}/api/1.0/customers/{$customerId}/payment-methods/{$paymentMethodId}";

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