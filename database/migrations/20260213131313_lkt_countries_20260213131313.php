<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktCountries20260213131313 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $exists = $this->hasTable('lkt_countries');
        if ($exists) return;

        $table = $this->table('lkt_countries', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])

            ->addColumn('name', 'json', ['null' => true, 'default' => null])

            ->addColumn('iso_code_cca2', 'char', ['limit' => 2])
            ->addColumn('iso_code_ccn3', 'char', ['limit' => 3])

            ->addColumn('is_active', 'boolean', ['default' => 0])
            ->addColumn('sync_excluded', 'boolean', ['default' => 0])
        ;

        $table->addIndex(['iso_code_cca2']);

        $table->create();
    }
}
