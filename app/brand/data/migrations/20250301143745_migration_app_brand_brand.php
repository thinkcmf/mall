<?php

use think\migration\db\Table;
use think\migration\Migrator;

class MigrationAppBrandBrand extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $tableName = 'brand';
        $table = $this->table($tableName, ['id' => true, 'comment' => '品牌']);
        $table->addColumn('create_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('delete_time', 'integer', ['default' => 0, 'comment' => '删除时间'])
            ->addColumn('status', 'boolean', ['signed' => false, 'default' => 1, 'comment' => '状态:1=可用,0=不可用'])
            ->addColumn('list_order', 'float', ['signed' => false, 'default' => 10000, 'comment' => '排序'])
            ->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '品牌名称'])
            ->addColumn('alias', 'string', ['limit' => 50, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '品牌别名'])
            ->addColumn('logo', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '品牌图片标识'])
            ->addColumn('url', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '品牌网址'])
            ->addColumn('keywords', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '关键词'])
            ->addColumn('description', 'string', ['limit' => 500, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '品牌介绍'])
            ->create();
    }
}