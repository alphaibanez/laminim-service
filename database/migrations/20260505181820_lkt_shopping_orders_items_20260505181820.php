<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LKtShoppingOrdersItems20260505181820 extends AbstractMigration
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
        $table = $this->table('lkt_shopping_orders__items', ['collation' => 'utf8mb4_unicode_ci']);

        $table
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])

            ->addColumn('order_id', 'integer', ['default' => 0])
            ->addColumn('product_id', 'integer', ['default' => 0])
            ->addColumn('component_id', 'integer', ['default' => 0])
            ->addColumn('sku', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('name', 'string', ['limit' => 255, 'default' => ''])

            ->addColumn('price_unit', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => 0])
            ->addColumn('quantity', 'integer', ['default' => 0])
            ->addColumn('tax_amount', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => 0])
            ->addColumn('discount_amount', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => 0])
            ->addColumn('total', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => 0])
        ;

        $table->addIndex(['order_id']);

        $table->create();
    }
}