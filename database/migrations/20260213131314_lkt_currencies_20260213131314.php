<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktCurrencies20260213131314 extends AbstractMigration
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
        $exists = $this->hasTable('lkt_currencies');
        if ($exists) return;

        $table = $this->table('lkt_currencies', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])

            ->addColumn('name', 'text', ['null' => true, 'default' => null, 'after' => 'name', 'limit' => MysqlAdapter::TEXT_LONG, 'collation' => 'utf8mb4_unicode_ci'])

            ->addColumn('iso_code_alpha3', 'char', ['limit' => 3])
            ->addColumn('iso_code_numeric3', 'char', ['limit' => 3])

            ->addColumn('is_active', 'boolean', ['default' => 0])
            ->addColumn('sync_excluded', 'boolean', ['default' => 0])

            ->addColumn('factor_to_default', 'decimal', ['precision' => 20, 'scale' => 5, 'default' => 0])
        ;

        $table->addIndex(['iso_code_alpha3']);

        $table->create();
    }
}
