<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktWebPage;
use function Lkt\WebPages\functions\addWebElement;

class LktWebPage extends GeneratedLktWebPage
{
    const COMPONENT = 'lkt-web-page';

    public function addWebElement(LktWebElement $element, int $before = 0, int $after = 0): static
    {
        $data = addWebElement($this->getWebElementsIds(), $element->getId(), $before, $after);
        return $this->setWebElements($data)->save();
    }
}