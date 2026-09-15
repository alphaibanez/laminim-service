<?php

use Phinx\Migration\AbstractMigration;

class LktUsers20251207154852 extends AbstractMigration
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
        $this->table('lkt_users__roles_app', ['id' => false, 'primary_key' => ['user_id', 'role_id'], 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('user_id', 'integer', ['default' => 0])
            ->addColumn('role_id', 'integer', ['default' => 0])
            ->addColumn('position', 'integer', ['default' => 0])
            ->create();

        $this->table('lkt_users__roles_admin', ['id' => false, 'primary_key' => ['user_id', 'role_id'], 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('user_id', 'integer', ['default' => 0])
            ->addColumn('role_id', 'integer', ['default' => 0])
            ->addColumn('position', 'integer', ['default' => 0])
            ->create();

        $items = $this->fetchAll("SELECT id, app_roles, admin_roles FROM lkt_users WHERE app_roles != '' OR admin_roles != ''");

        $queriesApp = [];
        $queriesAdmin = [];

        foreach ($items as $item) {
            $appRoles = explode(';', $item['app_roles']);
            $adminRoles = explode(';', $item['admin_roles']);

            if (count($appRoles) > 0) {
                foreach ($appRoles as $i => $role) {
                    if ($role) {
                        $queriesApp[] = "({$item['id']}, $role, $i)";
                    }
                }
            }

            if (count($adminRoles) > 0) {
                foreach ($adminRoles as $i => $role) {
                    if ($role) {
                        $queriesAdmin[] = "({$item['id']}, $role, $i)";
                    }
                }
            }
        }

        if (count($queriesApp) > 0) {
            $str = implode(', ', $queriesApp);
            $query = "INSERT INTO lkt_users__roles_app VALUES {$str}";
            $this->execute($query);
        }

        if (count($queriesAdmin) > 0) {
            $str = implode(', ', $queriesAdmin);
            $query = "INSERT INTO lkt_users__roles_admin VALUES {$str}";
            $this->execute($query);
        }


        $this->table('lkt_users')
            ->removeColumn('app_roles')
            ->removeColumn('admin_roles')
            ->update();
    }
}
