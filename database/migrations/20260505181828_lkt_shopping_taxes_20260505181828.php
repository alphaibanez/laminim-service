<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktShoppingTaxes20260505181828 extends AbstractMigration
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
        $table = $this->table('lkt_shopping_taxes', ['collation' => 'utf8mb4_unicode_ci']);

        $table
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by', 'integer', ['default' => 0])

            ->addColumn('is_active', 'boolean', ['default' => 0])
            ->addColumn('currency_id', 'integer', ['default' => 0]) // for fixed amount type
            ->addColumn('country_id', 'integer', ['default' => 0])
            ->addColumn('country_state_id', 'integer', ['default' => 0])
            ->addColumn('tax_type', 'smallinteger', ['default' => 0])
            ->addColumn('tax_target', 'smallinteger', ['default' => 0])
            ->addColumn('tax_amount', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => 0])
            ->addColumn('name', 'text', ['null' => true, 'default' => null, 'limit' => MysqlAdapter::TEXT_LONG, 'collation' => 'utf8mb4_unicode_ci'])

        ;

        $table->addIndex(['country_id']);

        $table->create();
    }
}