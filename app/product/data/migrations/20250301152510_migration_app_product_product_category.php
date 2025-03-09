<?php

use think\migration\Migrator;

class MigrationAppProductProductCategory extends Migrator
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
    public function change()
    {
        $tableName = 'product_category';
        $table = $this->table($tableName, ['id' => true, 'comment' => '商品分类']);
        $table
            ->addColumn('parent_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '父级ID'])
            ->addColumn('model_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '商品类型ID'])
            ->addColumn('create_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('delete_time', 'integer', ['default' => 0, 'comment' => '删除时间'])
            ->addColumn('status', 'boolean', ['signed' => false, 'default' => 1, 'comment' => '状态:1=可用,0=不可用'])
            ->addColumn('list_order', 'float', ['signed' => false, 'default' => 10000, 'comment' => '排序'])
            ->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '名称'])
            ->addColumn('keywords', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '关键词'])
            ->addColumn('description', 'string', ['limit' => 500, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '分类描述'])
            ->addColumn('thumbnail', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '一级分类logo'])
            ->addColumn('path', 'string', ['limit' => 100, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '分类层级关系路径'])
            ->addColumn('more', 'text', ['null' => true, 'collation' => 'utf8mb4_unicode_ci', 'comment' => '扩展属性'])
            ->create();
    }
}