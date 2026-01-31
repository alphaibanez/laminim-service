<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktAuthenticationLogs20251207154850 extends AbstractMigration
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
        $exists = $this->hasTable('lkt_authentication_logs');
        if ($exists) return;

        $table = $this->table('lkt_authentication_logs', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])

            ->addColumn('performed_action', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])
            ->addColumn('attempted_credential', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('attempted_password', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('attempted_successfully', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])
            ->addColumn('attempts_counter', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])

            ->addColumn('client_protocol', 'string', ['limit' => 10, 'default' => 'http'])
            ->addColumn('client_useragent', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('client_ip_address', 'string', ['limit' => 50, 'default' => ''])
            ->addColumn('client_os', 'string', ['limit' => 100, 'default' => ''])
            ->addColumn('client_browser', 'string', ['limit' => 100, 'default' => ''])
            ->addColumn('client_browser_version', 'string', ['limit' => 100, 'default' => ''])

            ->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])
            ->addColumn('user_status', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])
        ;

        $table->create();
    }
}