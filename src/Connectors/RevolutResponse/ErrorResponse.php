<?php

namespace Lkt\Connectors\RevolutResponse;

class ErrorResponse
{
    public string $code = '';
    public string $message = '';
    public int $timestamp = 0;
    public function __construct(array $data)
    {
        $this->code = $data['code'] ?? '';
        $this->message = $data['message'] ?? '';
        $this->timestamp = (int)$data['timestamp'] ?? 0;
    }
}