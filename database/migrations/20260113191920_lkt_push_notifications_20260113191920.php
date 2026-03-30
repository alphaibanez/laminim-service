<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktPushNotifications20260113191920 extends AbstractMigration
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
        $exists = $this->hasTable('lkt_push_notifications');
        if ($exists) return;

        $table = $this->table('lkt_push_notifications', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by', 'integer', ['default' => 0])

            ->addColumn('name', 'text', ['limit' => MysqlAdapter::BLOB_LONG, 'null' => true, 'default' => null])
            ->addColumn('description', 'text', ['limit' => MysqlAdapter::BLOB_LONG, 'null' => true, 'default' => null])
            ->addColumn('data', 'text', ['limit' => MysqlAdapter::BLOB_LONG, 'null' => true, 'default' => null])

            ->addColumn('priority', 'smallinteger', ['default' => 0])
            ->addColumn('target_type', 'smallinteger', ['default' => 0])
            ->addColumn('status', 'smallinteger', ['default' => 0])

            ->addColumn('target_user_ids', 'text', ['limit' => MysqlAdapter::BLOB_LONG, 'null' => true, 'default' => null])
            ->addColumn('target_app_role_ids', 'text', ['limit' => MysqlAdapter::BLOB_LONG, 'null' => true, 'default' => null])
            ->addColumn('target_admin_role_ids', 'text', ['limit' => MysqlAdapter::BLOB_LONG, 'null' => true, 'default' => null])
        ;

        $table->create();
    }
}
