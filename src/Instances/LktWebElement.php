<?php

namespace Lkt\Instances;

use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Generated\GeneratedLktWebElement;
use Lkt\WebPages\Enums\WebElementType;
use function Lkt\WebPages\functions\addWebElement;

class LktWebElement extends GeneratedLktWebElement
{
    const COMPONENT = 'lkt-web-element';

    public function addWebElement(LktWebElement $element, int $before = 0, int $after = 0): static
    {
        $data = addWebElement($this->getChildrenIds(), $element->getId(), $before, $after);
        return $this->setChildren($data)->save();
    }

    public function postProcessRead(array $response): array
    {
        $this->ensureRead($response);
        return $response;
    }

    private function ensureRead(array &$webElement)
    {
        $type = $webElement['type'];

        if (!isset($webElement['props']) || !is_array($webElement['props'])) {
            $webElement['props'] = [];;
        }

        if (!isset($webElement['props']['text']) || !is_array($webElement['props']['text'])) {
            $webElement['props']['text'] = [
                'en' => '',
                'es' => '',
            ];
        }

        if ($type === WebElementType::LktTextBanner) {
            if (!isset($webElement['props']['header']) || !is_array($webElement['props']['header'])) {
                $webElement['props']['header'] = [
                    'en' => '',
                    'es' => '',
                ];
            }
            if (!isset($webElement['props']['subHeader']) || !is_array($webElement['props']['subHeader'])) {
                $webElement['props']['subHeader'] = [
                    'en' => '',
                    'es' => '',
                ];
            }
        }

        if (is_numeric($webElement['props']['entity'])) {
            $webElement['props']['entity'] = LktFileEntity::getInstance($webElement['props']['entity'])->autoRead();
        }

        if (is_numeric($webElement['props']['art'])) {
            $webElement['props']['art'] = LktFileEntity::getInstance($webElement['props']['art'])->autoRead();
        }

        if (is_numeric($webElement['props']['media'])) {
            $webElement['props']['media'] = LktFileEntity::getInstance($webElement['props']['media'])->autoRead();
        }
    }

    protected function prepareCrudData(array $data, CrudOperation|null $operation = null): array
    {
        $type = $this->isAnonymous() ? $data['type'] : $this->getType();

        if (count($data['subElements']) > 0) {
            foreach ($data['subElements'] as &$subElement) {
                unset($subElement['id']);
                unset($subElement['value']);
                unset($subElement['label']);
                unset($subElement['keyMoment']);
                unset($subElement['uid']);

                if ($type === WebElementType::LktIcons) {
                    unset($subElement['subElements']);
                    unset($subElement['children']);
                    unset($subElement['component']);
                }
            }
        }

        if ($data['props']['entity']) {
            $data['props']['entity'] = (int)$data['props']['entity']['id'];
        }

        if ($data['props']['art']) {
            $data['props']['art'] = (int)$data['props']['art']['id'];
        }

        if ($data['props']['media']) {
            $data['props']['media'] = (int)$data['props']['media']['id'];
        }

        return $data;
    }
}