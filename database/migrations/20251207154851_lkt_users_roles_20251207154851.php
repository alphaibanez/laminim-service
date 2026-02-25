<?php

use Phinx\Migration\AbstractMigration;

class LktUsersRoles20251207154851 extends AbstractMigration
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
        $items = $this->fetchAll("SELECT id, name FROM lkt_users_roles");

        $lang = \Lkt\Locale\Locale::getLangCode();

        $processed = [];

        foreach ($items as $item) {
            $name = $item['name'];
            $name = [$lang => $name];

            $name = json_encode($name, JSON_UNESCAPED_UNICODE);

            $processed[$item['id']] = $name;
        }

        $table = $this->table('lkt_users_roles');

        $table->addColumn('name_aux', 'json', ['null' => true, 'default' => null, 'after' => 'name']);
        $table->update();

        foreach ($processed as $id => $name) {
            $this->query("UPDATE lkt_users_roles SET name_aux = '{$name}' WHERE id = {$id}");
        }

        $table->removeColumn('name')->update();
        $table->renameColumn('name_aux', 'name')->update();
    }
}
