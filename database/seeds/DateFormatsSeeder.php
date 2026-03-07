<?php

use Lkt\Instances\LktDateFormat;
use Phinx\Seed\AbstractSeed;

class DateFormatsSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run() : void
    {
        LktDateFormat::createIfMissing(['format' => 'yyyy-mm-dd']);
        LktDateFormat::createIfMissing(['format' => 'yyyy/mm/dd']);
        LktDateFormat::createIfMissing(['format' => 'dd-mm-yyyy']);
        LktDateFormat::createIfMissing(['format' => 'dd/mm/yyyy']);
        LktDateFormat::createIfMissing(['format' => 'mm-dd-yyyy']);
        LktDateFormat::createIfMissing(['format' => 'mm/dd/yyyy']);
    }
}