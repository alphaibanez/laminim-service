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
     * In relational fields, it can be:
     *  a. the column in this table storing the related id/ids (foreign key/pivot)
     *  b. the column in the related table containing this table item id (related)
     */
    public function getColumn(): string;
    public function getLocaleColumn(string $locale): string;
}