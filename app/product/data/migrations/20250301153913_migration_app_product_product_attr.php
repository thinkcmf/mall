<?php

use think\migration\Migrator;

class MigrationAppProductProductAttr extends Migrator
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
        $table = $this->table('product_attr', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品品属性']);
        $table->addColumn('model_id', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '商品模型ID'])
            ->addColumn('type', 'integer', ['limit' => 3, 'default' => 1, 'comment' => '类型:1=自然属性,2=销售属性'])
            ->addColumn('has_thumbnail', 'integer', ['limit' => 4, 'default' => 0, 'comment' => '是否有缩略图'])
            ->addColumn('update_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('input_type', 'integer', ['limit' => 4, 'default' => 0, 'comment' => '输入组件类型:1=下拉列表,2=单行文本框,3=多行文本'])
            ->addColumn('searchable', 'integer', ['limit' => 3, 'default' => 0, 'comment' => '是否可搜索:0=不可以,1=可以'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '状态'])
            ->addColumn('list_order', 'float', ['default' => 10000, 'comment' => '排序'])
            ->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('remark', 'string', ['limit' => 50, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '备注'])
            ->addColumn('options', 'text', ['null' => true, 'comment' => '属性可选值，以英文逗号分隔，如果允许上传缩略图，代表此属性为自定义属性，商品可以自由设置'])
            ->create();
    }
}