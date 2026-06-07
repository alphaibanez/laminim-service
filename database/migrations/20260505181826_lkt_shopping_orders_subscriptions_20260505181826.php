<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LKtShoppingOrdersSubscriptions20260505181826 extends AbstractMigration
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
        $table = $this->table('lkt_shopping_orders__subscriptions', ['collation' => 'utf8mb4_unicode_ci', 'id' => false, 'primary_key' => ['order_id', 'subscription_id']]);

        $table
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])

            ->addColumn('order_id', 'integer', ['default' => 0])
            ->addColumn('subscription_id', 'integer', ['default' => 0])
            ->addColumn('position', 'integer', ['default' => 0])
        ;

        $table->addIndex(['order_id']);

        $table->create();
    }
}