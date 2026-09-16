<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LKtWebElements20250330104202 extends AbstractMigration
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
        $this->table('lkt_web_elements__web_elements', ['id' => false, 'primary_key' => ['parent_id', 'child_id'], 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('parent_id', 'integer', ['default' => 0])
            ->addColumn('child_id', 'integer', ['default' => 0])
            ->addColumn('position', 'integer', ['default' => 0])
            ->create();

        $items = $this->fetchAll("SELECT id, children FROM lkt_web_elements WHERE children != ''");

        $queries = [];

        foreach ($items as $item) {
            $children = explode(';', $item['children']);

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
            $query = "INSERT INTO lkt_web_elements__web_elements VALUES {$str}";
            $this->execute($query);
        }


        $this->table('lkt_web_elements')
            ->removeColumn('children')
            ->update();

    }
}
