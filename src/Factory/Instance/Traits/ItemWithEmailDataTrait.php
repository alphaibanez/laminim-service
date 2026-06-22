<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\EmailDataController;
use Lkt\Factory\Instance\DataControllers\StringDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithEmailDataTrait
{
    private EmailDataController $emailData;

    private function initEmailData(Schema $schema, Item $item, array $rawData): static
    {
        $this->emailData = new EmailDataController($schema, $item, $rawData);
        return $this;
    }
}