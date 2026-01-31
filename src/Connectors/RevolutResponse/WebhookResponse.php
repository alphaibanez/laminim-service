<?php

namespace Lkt\Connectors\RevolutResponse;

use Lkt\Connectors\Enums\RevolutWebhookEvent;

class WebhookResponse
{
    public string $id = '';
    public string $url = '';
    public string $signingSecret = '';

    /** @var RevolutWebhookEvent[] */
    public array $events = [];

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->url = $data['url'] ?? '';
        $this->signingSecret = $data['signing_secret'] ?? '';

        $_events = [];
        foreach ($data['events'] as $event) {
            $v = RevolutWebhookEvent::tryFrom($event);
            if ($v !== null) {
                $_events[] = $v;
            }
        }

        $this->events = $_events;
    }
}