<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class LktWebPagesCategories20260411121212 extends AbstractMigration
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
        $table = $this->table('lkt_web_pages__categories', ['collation' => 'utf8mb4_unicode_ci']);

        $table
            ->addColumn('created_by', 'integer', ['default' => 0])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('name', 'text', ['null' => true, 'default' => null, 'limit' => MysqlAdapter::TEXT_LONG, 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('config', 'text', ['null' => true, 'default' => null])
        ;

        $table->addIndex(['name']);

        $table->create();
    }
}
