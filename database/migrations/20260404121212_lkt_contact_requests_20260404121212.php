<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class LktContactRequests20260404121212 extends AbstractMigration
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
        $table = $this->table('lkt_contact_requests', ['collation' => 'utf8mb4_unicode_ci']);

        $table
            ->addColumn('created_by', 'integer', ['default' => 0])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])

            ->addColumn('contact_reason_id', 'integer', ['default' => 0])
            ->addColumn('name', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('email', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('phone', 'string', ['limit' => 25, 'default' => ''])
            ->addColumn('message', 'text', ['null' => true, 'default' => ''])

            ->addColumn('client_protocol', 'string', ['limit' => 10, 'default' => 'http'])
            ->addColumn('client_useragent', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('client_ip_address', 'string', ['limit' => 50, 'default' => ''])
            ->addColumn('client_os', 'string', ['limit' => 100, 'default' => ''])
            ->addColumn('client_browser', 'string', ['limit' => 100, 'default' => ''])
            ->addColumn('client_browser_version', 'string', ['limit' => 100, 'default' => ''])
        ;

        $table->create();
    }
}
