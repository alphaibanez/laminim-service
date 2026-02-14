<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktMenuEntry;
use Lkt\Translations\Translations;

class LktMenuEntry extends GeneratedLktMenuEntry
{
    const COMPONENT = 'lkt-menu-entry';

    public function getReadMenuTo(): string
    {
        if ($this->typeIsRelativeUrl()) return $this->getUrl();
        if ($this->typeIsFullUrl()) return $this->getUrl();

        if ($this->typeIsWebItems()) {
            return "/admin/web-items/{$this->getComponent()}";
        }

        if ($this->typeIsWebPages()) {
            return "/admin/web-pages/{$this->getComponent()}";
        }

        if ($this->typeIsAppRoute()) {
            return "route:{$this->getRoute()}";
        }
        return '';
    }

    public function getReadMenuType(): string
    {
        if ($this->typeIsHeader()) return 'header';
        return 'entry';
    }

    public function postProcessRead(array $data): array
    {
        if ($this->accessPolicy?->name === 'r-app-menu') {

            if ($this->typeIsHeader()) {
                return [
                    'type' => $this->getReadMenuType(),
                    'header' => [
                        'text' => $data['text']
                    ],
                ];
            }

            if ($this->typeIsParent()) {
                return [
                    'type' => $this->getReadMenuType(),
                    'anchor' => [
                        'text' => $data['text'],
                    ],
                    'children' => $data['children'],
                ];
            }

            if ($this->typeIsWebItems() && $this->isAnonymous() && !$this->getName()) {
                $component = $this->getComponent();
                $text = Translations::get("webItems.{$component}");
                $data['text'] = $text ?? $this->getComponent();
            }

            return [
                'type' => $this->getReadMenuType(),
                'anchor' => $data,
            ];
        }
        return $data;
    }
}