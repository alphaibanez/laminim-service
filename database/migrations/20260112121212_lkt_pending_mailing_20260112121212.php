<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktPendingMailing20260112121212 extends AbstractMigration
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
        $exists = $this->hasTable('lkt_mailing_queue');
        if ($exists) return;

        $table = $this->table('lkt_mailing_queue', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by', 'integer', ['default' => 0])

            ->addColumn('priority', 'smallinteger', ['default' => 0])
            ->addColumn('email', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('subject', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('message', 'text', ['limit' => MysqlAdapter::BLOB_LONG, 'null' => true, 'default' => null])
        ;

        $table->create();
    }
}
