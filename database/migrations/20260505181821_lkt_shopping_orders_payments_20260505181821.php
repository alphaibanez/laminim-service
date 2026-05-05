<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LKtShoppingOrdersPayments20260505181821 extends AbstractMigration
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
        $table = $this->table('lkt_shopping_orders__payments', ['collation' => 'utf8mb4_unicode_ci']);

        $table
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('paid_at', 'datetime', ['null' => true, 'default' => null])

            ->addColumn('order_id', 'integer', ['default' => 0])
            ->addColumn('status', 'integer', ['default' => 0])
            ->addColumn('payment_method', 'integer', ['default' => 0])
            ->addColumn('amount', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => 0])
            ->addColumn('transaction_id', 'string', ['limit' => 255, 'default' => ''])
        ;

        $table->addIndex(['order_id']);
        $table->addIndex(['status']);
        $table->addIndex(['order_id', 'status']);

        $table->create();
    }
}