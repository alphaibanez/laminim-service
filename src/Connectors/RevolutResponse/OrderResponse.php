<?php

namespace Lkt\Connectors\RevolutResponse;

class OrderResponse
{
    public string $id = '';
    public string $token = '';
    public string $type = '';
    public string $state = '';
    public string $createdAt = '';
    public string $updatedAt = '';

    public float $amount = 0;
    public float $outstandingAmount = 0;
    public string $currency = '';
    public string $captureMode = '';
    public string $checkoutUrl = '';
    public string $enforceChallenge = '';

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->token = $data['token'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->state = $data['state'] ?? '';
        $this->createdAt = $data['created_at'] ?? '';
        $this->updatedAt = $data['updated_at'] ?? '';
        $this->amount = (float)$data['amount'] ?? 0;
        $this->outstandingAmount = (float)$data['outstanding_amount'] ?? 0;
        $this->currency = $data['currency'] ?? '';
        $this->captureMode = $data['capture_mode'] ?? '';
        $this->checkoutUrl = $data['checkout_url'] ?? '';
        $this->enforceChallenge = $data['enforce_challenge'] ?? '';
    }
}