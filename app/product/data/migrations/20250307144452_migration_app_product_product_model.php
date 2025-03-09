<?php

use think\migration\Migrator;

class MigrationAppProductProductModel extends Migrator
{
    public function change()
    {
        $table = $this->table('product_model', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品模型']);
        $table->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '名称'])
            ->addColumn('status', 'integer', ['limit' => 4, 'default' => 1, 'comment' => '状态:0=禁用,1=正常'])
            ->create();
    }
}