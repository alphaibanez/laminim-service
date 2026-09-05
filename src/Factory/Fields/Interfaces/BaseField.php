<?php

namespace Lkt\Factory\Fields\Interfaces;

interface BaseField
{
    /**
     * @laminim
     * Field naming
     * This is the key used by the app
     */
    public function setName(string $name): static;
    public function getName(): string;

    /**
     * @laminim
     * Field column
     * This is the key used by the database
     * In relational fields, points to the other table column heading this table
     */
    public function getColumn(): string;
    public function getLocaleColumn(string $locale): string;
}