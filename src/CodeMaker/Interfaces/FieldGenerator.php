<?php

namespace Lkt\CodeMaker\Interfaces;

use Lkt\CodeMaker\DTO\FieldGeneratorData;

interface FieldGenerator
{
    public function getGetters(): string;
    public function getSetters(): string;
    public function getCheckers(): string;
    public function parse(): string;

    public static function generateCode(FieldGeneratorData $data): string;

    public function getAllowedOptionsMethods(): array;

    public function getEnumChoiceClass(): string;
}