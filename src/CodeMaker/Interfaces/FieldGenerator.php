<?php

namespace Lkt\CodeMaker\Interfaces;

use Lkt\CodeMaker\DTO\FieldGeneratorData;
use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Schemas\Schema;

interface FieldGenerator
{
    public function getGetters(): string;
    public function getSetters(): string;
    public function getCheckers(): string;
    public function parse(): string;

    public static function generateCode(FieldGeneratorData $data): string;

    public static function generateTraitsUsageCode(Field $field): array;

    public function getAllowedOptionsMethods(): array;

    public function getEnumChoiceClass(): string;

    public static function queryBuilder(Field $field, Schema $schema): static;
}