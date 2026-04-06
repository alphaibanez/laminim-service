<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktHttpRequestsLogs20260405171717 extends AbstractMigration
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
        $exists = $this->hasTable('lkt_http_requests_logs');
        if ($exists) return;

        $table = $this->table('lkt_http_requests_logs', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])

            ->addColumn('route', 'string', ['limit' => 255, 'default' => 'get'])
            ->addColumn('method', 'string', ['limit' => 10, 'default' => 'get'])
            ->addColumn('response_status', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])
            ->addColumn('payload', 'text', ['null' => true, 'default' => null, 'limit' => MysqlAdapter::TEXT_LONG, 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('request', 'text', ['null' => true, 'default' => null, 'limit' => MysqlAdapter::TEXT_LONG, 'collation' => 'utf8mb4_unicode_ci'])

            ->addColumn('client_protocol', 'string', ['limit' => 10, 'default' => 'http'])
            ->addColumn('client_useragent', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('client_ip_address', 'string', ['limit' => 50, 'default' => ''])
            ->addColumn('client_os', 'string', ['limit' => 100, 'default' => ''])
            ->addColumn('client_browser', 'string', ['limit' => 100, 'default' => ''])
            ->addColumn('client_browser_version', 'string', ['limit' => 100, 'default' => ''])

            ->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])
        ;

        $table->addIndex(['route']);
        $table->addIndex(['method']);

        $table->create();
    }
}