<?php

namespace Lkt\Connectors\RevolutResponse;

class PaymentResponse
{
    public string $id = '';
    public string $orderId = '';
    public string $state = '';
    public string $createdAt = '';
    public string $updatedAt = '';
    public string $declineReason = '';
    public string $bankMessage = '';
    public string $token = '';
    public int $amount = 0;
    public int $settledAmount = 0;
    public string $currency = '';
    public string $settledCurrency = '';
    public array $paymentMethod = [];
    public array $authenticationChallenge = [];
    public array $billingAddress = [];
    public array $fees = [];
    public array $payer = [];
    public string $riskLevel = '';

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->token = $data['token'] ?? '';
        $this->orderId = $data['order_id'] ?? '';
        $this->state = $data['state'] ?? '';
        $this->createdAt = $data['created_at'] ?? '';
        $this->updatedAt = $data['updated_at'] ?? '';
        $this->amount = (int)$data['amount'] ?? 0;
        $this->settledAmount = (int)$data['settled_amount'] ?? 0;
        $this->currency = $data['currency'] ?? '';
        $this->settledCurrency = $data['settled_currency'] ?? '';
        $this->declineReason = $data['decline_reason'] ?? '';
        $this->bankMessage = $data['bank_message'] ?? '';
        $this->riskLevel = $data['risk_level'] ?? '';
        $this->paymentMethod = $data['payment_method'] ?? [];
        $this->authenticationChallenge = $data['authentication_challenge'] ?? [];
        $this->billingAddress = $data['billing_address'] ?? [];
        $this->fees = $data['fees'] ?? [];
        $this->payer = $data['payer'] ?? [];
    }
}