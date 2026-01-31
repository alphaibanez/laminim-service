<?php

namespace Lkt\Instances;

use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktWebElement;
use Lkt\WebPages\Enums\WebElementType;
use function Lkt\WebPages\functions\addWebElement;

class LktWebElement extends GeneratedLktWebElement
{
    const COMPONENT = 'lkt-web-element';

    public function read()
    {
        $fields = Schema::get(static::COMPONENT)->getAllFields();
        $r = $this->readFields($fields);
        $this->ensureRead($r);
        return $r;
    }

//    public function readAsRelated(): array
//    {
//        $r = parent::readAsRelated();
//        $this->ensureRead($r);
//        return $r;
//    }

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
    }

    private function prepareData(array $data)
    {
        $type = $this->isAnonymous() ? $data['type'] : $this->getType();
        if ($type === WebElementType::LktTextBanner) {
            if (!$data['props']['art'] || !is_array($data['props']['art'])|| count($data['props']['art']) === 0) {
                $data['props']['art'] = ['src' => ''];
            }
            if (!$data['props']['media'] || !is_array($data['props']['media'])|| count($data['props']['media']) === 0) {
                $data['props']['media'] = ['src' => ''];
            }
        }

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

        return $data;
    }
}