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
        LktDateFormat::mkIfNot(['format' => 'yyyy-mm-dd']);
        LktDateFormat::mkIfNot(['format' => 'yyyy/mm/dd']);
        LktDateFormat::mkIfNot(['format' => 'dd-mm-yyyy']);
        LktDateFormat::mkIfNot(['format' => 'dd/mm/yyyy']);
        LktDateFormat::mkIfNot(['format' => 'mm-dd-yyyy']);
        LktDateFormat::mkIfNot(['format' => 'mm/dd/yyyy']);
    }
}