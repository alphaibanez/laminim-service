<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LKtWebPages20250330104201 extends AbstractMigration
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
        $table = $this->table('lkt_web_pages', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('published_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by', 'integer', ['default' => 0])

            ->addColumn('status', 'integer', ['limit' => 12])
            ->addColumn('name', 'json')
            ->addColumn('summary', 'json')
            ->addColumn('slug', 'json')

            ->addColumn('type', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])
            ->addColumn('parent_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('parent_component', 'string', ['limit' => 250])

            ->addColumn('seo_title', 'json')
            ->addColumn('keywords', 'text', ['null' => true, 'default' => null])
            ->addColumn('web_elements', 'text', ['null' => true, 'default' => null])
        ;

        $table->create();
    }
}