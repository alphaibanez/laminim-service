<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class LktAccessToken20260212121212 extends AbstractMigration
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
        $exists = $this->hasTable('lkt_access_token');
        if ($exists) return;

        $table = $this->table('lkt_access_token', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('created_at', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('expires_at', 'datetime', ['null' => true, 'default' => null])

            ->addColumn('user_id', 'integer', ['default' => 0])

            ->addColumn('purpose', 'smallinteger', ['default' => 0])
            ->addColumn('duration', 'smallinteger', ['default' => 0])

            ->addColumn('token', 'string', ['limit' => 64, 'default' => ''])
        ;

        $table->addIndex(['purpose', 'token']);

        $table->create();
    }
}
