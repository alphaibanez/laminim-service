<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LKtWebPages20250330104203 extends AbstractMigration
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
        $this->table('lkt_web_pages__web_elements', ['id' => false, 'primary_key' => ['web_page_id', 'web_element_id'], 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('web_page_id', 'integer', ['default' => 0])
            ->addColumn('web_element_id', 'integer', ['default' => 0])
            ->addColumn('position', 'integer', ['default' => 0])
            ->create();

        $items = $this->fetchAll("SELECT id, web_elements FROM lkt_web_pages WHERE web_elements != ''");

        $queries = [];

        foreach ($items as $item) {
            $children = explode(';', $item['web_elements']);

            if (count($children) > 0) {
                foreach ($children as $i => $child) {
                    if ($child) {
                        $queries[] = "({$item['id']}, $child, $i)";
                    }
                }
            }
        }

        if (count($queries) > 0) {
            $str = implode(', ', $queries);
            $query = "INSERT INTO lkt_web_pages__web_elements VALUES {$str}";
            $this->execute($query);
        }


        $this->table('lkt_web_pages')
            ->removeColumn('web_elements')
            ->update();

    }
}
