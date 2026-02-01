<?php

namespace Lkt\Http;

use Lkt\Http\Enums\NotificationCategory;
use function Lkt\Tools\Parse\clearInput;

class Notification
{
    public NotificationCategory $category = NotificationCategory::Toast;

    public static string $defaultSuccessIcon = '';
    public static string $defaultFailIcon = '';
    public static string $defaultWarningIcon = '';

    public static string $defaultSuccessClass = '';
    public static string $defaultFailClass = '';
    public static string $defaultWarningClass = '';

    readonly public string $text;
    readonly public string $details;
    readonly public string $class;

    readonly public string $to;
    readonly public bool $replace;
    readonly public bool $syncAppSetup;
    readonly public bool $syncAppI18n;

    public string $icon = '';

    protected function __construct(NotificationCategory $category, array $payload)
    {
        $this->category = $category;

        $this->text = isset($payload['text']) ? clearInput($payload['text']) : '';
        $this->class = isset($payload['class']) ? clearInput($payload['class']) : '';
        $this->details = isset($payload['details']) ? clearInput($payload['details']) : '';
        $this->icon = isset($payload['icon']) ? clearInput($payload['icon']) : '';
        $this->to = isset($payload['to']) ? clearInput($payload['to']) : '';
        $this->replace = isset($payload['replace']) && (bool)$payload['replace'];
        $this->syncAppSetup = isset($payload['syncAppSetup']) && (bool)$payload['syncAppSetup'];
        $this->syncAppI18n = isset($payload['syncAppI18n']) && (bool)$payload['syncAppI18n'];
    }

    public static function sendToast(array $payload): static
    {
        $instance = new static( NotificationCategory::Toast, $payload);
        Router::addPendingNotification($instance);
        return $instance;
    }

    public static function sendSuccessToast(array $payload): static
    {
        $p = [];
        if (static::$defaultSuccessClass) $p['class'] = static::$defaultSuccessClass;
        if (static::$defaultSuccessIcon) $p['icon'] = static::$defaultSuccessIcon;

        $instance = new static( NotificationCategory::Toast, [...$p, ...$payload]);
        Router::addPendingNotification($instance);
        return $instance;
    }

    public static function sendFailToast(array $payload): static
    {
        $p = [];
        if (static::$defaultFailClass) $p['class'] = static::$defaultFailClass;
        if (static::$defaultFailIcon) $p['icon'] = static::$defaultFailIcon;

        $instance = new static( NotificationCategory::Toast, [...$p, ...$payload]);
        Router::addPendingNotification($instance);
        return $instance;
    }

    public static function sendWarningToast(array $payload): static
    {
        $p = [];
        if (static::$defaultWarningClass) $p['class'] = static::$defaultWarningClass;
        if (static::$defaultWarningIcon) $p['icon'] = static::$defaultWarningIcon;

        $instance = new static( NotificationCategory::Toast, [...$p, ...$payload]);
        Router::addPendingNotification($instance);
        return $instance;
    }

    public static function sendRedirect(string $to, bool $replace = false): static
    {
        $instance = new static( NotificationCategory::Redirect, [
            'to' => $to,
            'replace' => $replace,
        ]);
        Router::addPendingNotification($instance);
        return $instance;
    }

    public static function sendSyncAppResource(bool $setup, bool $i18n = false): static
    {
        $instance = new static( NotificationCategory::SyncAppResource, [
            'syncAppSetup' => $setup,
            'syncAppI18n' => $i18n,
        ]);
        Router::addPendingNotification($instance);
        return $instance;
    }

    public function toArray(): array
    {
        $payload = [];

        switch ($this->category) {
            case NotificationCategory::Toast:
                if ($this->text !== '') $payload['text'] = $this->text;
                if ($this->details !== '') $payload['details'] = $this->details;
                if ($this->class !== '') $payload['class'] = $this->class;
                if ($this->icon !== '') $payload['icon'] = $this->icon;
                break;

            case NotificationCategory::Redirect:
                if ($this->to !== '') $payload['to'] = $this->to;
                $payload['replace'] = $this->replace;
                break;

            case NotificationCategory::SyncAppResource:
                $payload['setup'] = $this->syncAppSetup;
                $payload['i18n'] = $this->syncAppI18n;
                break;
        }

        return [
            'category' => $this->category->value,
            'payload' => $payload,
        ];
    }
}