<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktUsers20251207154848 extends AbstractMigration
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
        $exists = $this->hasTable('lkt_users');
        if ($exists) return;

        $table = $this->table('lkt_users', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null, 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by', 'integer', ['default' => 0])

            ->addColumn('status', 'smallinteger', ['default' => 1])
            ->addColumn('firstname', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('lastname', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('email', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('credential_id', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('password', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('preferred_language', 'string', ['limit' => 10, 'default' => ''])
            ->addColumn('preferred_theme_mode', 'smallinteger', ['default' => 0])
            ->addColumn('app_roles', 'text', ['null' => true, 'default' => null])
            ->addColumn('admin_roles', 'text', ['null' => true, 'default' => null])
            ->addColumn('session_token', 'string', ['limit' => 255, 'default' => ''])
            ->addColumn('is_administrator', 'boolean', ['default' => 0])
            ->addColumn('can_receive_push_notifications', 'boolean', ['default' => 1])
            ->addColumn('can_receive_mail_notifications', 'boolean', ['default' => 1])
        ;

        $table->create();
    }
}
