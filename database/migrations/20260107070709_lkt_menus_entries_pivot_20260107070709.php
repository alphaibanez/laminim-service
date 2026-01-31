<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktMenusEntriesPivot20260107070709 extends AbstractMigration
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
        $exists = $this->hasTable('lkt_menus__entries');
        if ($exists) return;

        $table = $this->table('lkt_menus__entries', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('menu_id', 'integer', ['default' => 0])
            ->addColumn('entry_id', 'integer', ['default' => 0])
            ->addColumn('position', 'integer', ['default' => 0])
        ;

        $table->create();
    }
}
