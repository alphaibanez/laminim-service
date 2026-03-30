<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktPushDevices20260113191919 extends AbstractMigration
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
        $exists = $this->hasTable('lkt_push_devices');
        if ($exists) return;

        $table = $this->table('lkt_push_devices', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by', 'integer', ['default' => 0])

            ->addColumn('status', 'smallinteger', ['default' => 0])
            ->addColumn('platform', 'smallinteger', ['default' => 0])
            ->addColumn('latest_claimed_by_user_id', 'integer', ['default' => 0])

            ->addColumn('created_at_app_version', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('app_version', 'string', ['limit' => 255, 'default' => ''])
        ;

        $table->create();
    }
}
