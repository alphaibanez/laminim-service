<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class LktWebPagesMetas20260411121214 extends AbstractMigration
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
        $this->table('lkt_web_pages__slugs')
            ->rename('lkt_web_pages__metas')
            ->update();

        $this
            ->table('lkt_web_pages__metas')
            ->addColumn('description', 'text', ['null' => true, 'default' => null])
            ->addColumn('keywords', 'text', ['null' => true, 'default' => null])
            ->addColumn('prevent_robots_index', 'boolean', ['default' => 0])
            ->addColumn('prevent_robots_follow', 'boolean', ['default' => 0])
            ->update();
    }
}
