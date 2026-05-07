<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LKtShoppingCoupons20260505181822 extends AbstractMigration
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
        $table = $this->table('lkt_shopping_coupons', ['collation' => 'utf8mb4_unicode_ci']);

        $table
            ->addColumn('created_by', 'integer', ['default' => 0])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])

            ->addColumn('type', 'integer', ['default' => 0])
            ->addColumn('discount_type', 'integer', ['default' => 0])
            ->addColumn('value', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => 0])
            ->addColumn('currency_id', 'integer', ['default' => 0])


            ->addColumn('starts_at', 'datetime', ['null' => true])
            ->addColumn('ends_at', 'datetime', ['null' => true])
            ->addColumn('usage_limit', 'integer', ['default' => 0])
            ->addColumn('usage_limit_per_user', 'integer', ['default' => 0])
            ->addColumn('minimum_order_amount', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => 0])


            ->addColumn('is_active', 'boolean', ['default' => 0])
            ->addColumn('stackable', 'boolean', ['default' => 0])
        ;

        $table->addIndex(['created_by']);
        $table->addIndex(['created_by', 'is_active']);

        $table->create();
    }
}