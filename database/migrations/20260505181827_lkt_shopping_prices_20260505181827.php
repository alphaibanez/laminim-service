<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktShoppingPrices20260505181827 extends AbstractMigration
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
        $table = $this->table('lkt_shopping_prices', ['collation' => 'utf8mb4_unicode_ci']);

        $table
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])

            ->addColumn('is_active', 'boolean', ['default' => 0])
            ->addColumn('product_id', 'integer', ['default' => 0])
            ->addColumn('component_id', 'integer', ['default' => 0])
            ->addColumn('country_id', 'integer', ['default' => 0])
            ->addColumn('currency_id', 'integer', ['default' => 0])
            ->addColumn('attached_criteria', 'smallinteger', ['default' => 0])
            ->addColumn('price_type', 'smallinteger', ['default' => 0])

            ->addColumn('price_unit', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => 0])
            ->addColumn('tax_amount', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => 0])

        ;

        $table->addIndex(['product_id', 'component_id', 'is_active', 'country_id']);

        $table->create();
    }
}