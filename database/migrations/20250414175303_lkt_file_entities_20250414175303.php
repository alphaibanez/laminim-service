<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LKtFileEntities20250414175303 extends AbstractMigration
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
        $this->table('lkt_file_entities')
            ->addColumn('lang_specific_src', 'text', ['null' => true, 'default' => null, 'after' => 'name', 'limit' => MysqlAdapter::TEXT_LONG, 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('lang_specific_embed_code', 'text', ['null' => true, 'default' => null, 'after' => 'name', 'limit' => MysqlAdapter::TEXT_LONG, 'collation' => 'utf8mb4_unicode_ci'])
            ->update();
    }
}
