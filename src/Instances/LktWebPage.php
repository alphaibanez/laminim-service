<?php

namespace Lkt\Instances;

use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktWebPage;
use function Lkt\WebPages\functions\addWebElement;

class LktWebPage extends GeneratedLktWebPage
{
    const COMPONENT = 'lkt-web-page';

    public function read()
    {
        $fields = Schema::get(static::COMPONENT)->getAllFields();
        return $this->readFields($fields);
    }

    public function addWebElement(LktWebElement $element, int $before = 0, int $after = 0): static
    {
        $data = addWebElement($this->getWebElementsIds(), $element->getId(), $before, $after);
        return $this->setWebElements($data)->save();
    }
}