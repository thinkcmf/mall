<?php
use think\migration\Migrator;

class MigrationAppOrderOrderCart extends Migrator
{
    public function change(): void
    {
        $table = $this->table('order_cart', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '购物车']);
        $table->addColumn('product_id', 'integer', ['signed' => false, 'null' => false, 'comment' => '商品id'])
              ->addColumn('product_sku_id', 'integer', ['signed' => false, 'null' => false, 'comment' => '商品SKU id'])
              ->addColumn('product_price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '商品价格'])
              ->addColumn('product_quantity', 'integer', ['signed' => false, 'default' => 0, 'comment' => '商品数量'])
              ->addColumn('product_name', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品名称'])
              ->addColumn('product_thumbnail', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品缩略图'])
              ->addColumn('product_spec', 'text', ['null' => true, 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品规格'])
              ->addColumn('product_sn', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品编号'])
              ->create();
    }
}