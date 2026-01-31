<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktMenusEntries20260107070708 extends AbstractMigration
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
        $exists = $this->hasTable('lkt_menus_entries');
        if ($exists) return;

        $table = $this->table('lkt_menus_entries', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by', 'integer', ['default' => 0])

            ->addColumn('name', 'json', ['null' => true, 'default' => null])
            ->addColumn('status', 'smallinteger', ['default' => 1])
            ->addColumn('type', 'smallinteger', ['default' => 1])
            ->addColumn('component', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('url', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('item_id', 'integer', ['default' => 0])
            ->addColumn('access_level', 'smallinteger', ['default' => 0])
            ->addColumn('required_roles', 'text', ['null' => true, 'default' => null])
            ->addColumn('forbidden_roles', 'text', ['null' => true, 'default' => null])
        ;

        $table->create();
    }
}
