<?php

namespace Lkt\Connectors\RevolutResponse;

class CustomerResponse
{
    public string $id = '';
    public string $fullName = '';
    public string $businessName = '';
    public string $email = '';
    public string $phone = '';
    public string $dateOfBirth = '';
    public string $createdAt = '';
    public string $updatedAt = '';
    public array $paymentMethods = [];

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->fullName = $data['full_name'] ?? '';
        $this->businessName = $data['business_name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->dateOfBirth = $data['date_of_birth'] ?? '';
        $this->createdAt = $data['created_at'] ?? '';
        $this->updatedAt = $data['updated_at'] ?? '';
        $this->paymentMethods = (array)$data['payment_methods'] ?? [];
    }
}