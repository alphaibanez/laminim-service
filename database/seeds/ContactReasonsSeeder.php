<?php

use Lkt\Instances\LktContactReason;
use Lkt\WebPages\Enums\WebPageStatus;
use Phinx\Seed\AbstractSeed;

class ContactReasonsSeeder extends AbstractSeed
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
        LktContactReason::mkIfNot([
            'name' => [
                'es' => 'Otro', 'en' => 'Other',
            ],
            'status' => WebPageStatus::Public->value
        ]);

        LktContactReason::mkIfNot([
            'name' => [
                'es' => 'Dudas o preguntas', 'en' => 'Questions',
            ],
            'status' => WebPageStatus::Public->value
        ]);

        LktContactReason::mkIfNot([
            'name' => [
                'es' => 'Sugerencias', 'en' => 'Suggestions',
            ],
            'status' => WebPageStatus::Public->value
        ]);

        LktContactReason::mkIfNot([
            'name' => [
                'es' => 'Informar de un error', 'en' => 'Report an error',
            ],
            'status' => WebPageStatus::Public->value
        ]);

        LktContactReason::mkIfNot([
            'name' => [
                'es' => 'Mi cuenta', 'en' => 'My account',
            ],
            'status' => WebPageStatus::Public->value
        ]);
    }
}