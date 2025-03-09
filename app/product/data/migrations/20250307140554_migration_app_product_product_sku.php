<?php

use think\migration\Migrator;

class MigrationAppProductProductSku extends Migrator
{
    public function change()
    {
        $table = $this->table('product_sku', ['id' => true,'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品SKU']);
            $table->addColumn('product_id', 'integer', ['signed' => false, 'null' => false, 'comment' => '商品id'])
            ->addColumn('shop_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '店铺id'])
            ->addColumn('model_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '商品模型ID'])
            ->addColumn('category_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '商品类目ID'])
            ->addColumn('brand_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '品牌'])
            ->addColumn('quantity', 'integer', ['signed' => false, 'default' => 0, 'comment' => '商品数量'])
            ->addColumn('status', 'integer', ['limit' => 4, 'default' => 1, 'comment' => '状态:0=下架,1=上架'])
            ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '商品价格'])
            ->addColumn('cost_price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '成本价'])
            ->addColumn('original_price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '原价'])
            ->addColumn('weight', 'decimal', ['precision' => 20, 'scale' => 3, 'default' => '0.000', 'comment' => '商品重量'])
            ->addColumn('volume', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '体积,单位ml'])
            ->addColumn('create_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('delete_time', 'integer', ['default' => 0, 'comment' => '删除时间'])
            ->addColumn('list_order', 'float', ['default' => 10000, 'comment' => '排序'])
            ->addColumn('title', 'string', ['limit' => 90, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品标题'])
            ->addColumn('sn', 'string', ['limit' => 30, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品编号'])
            ->addColumn('barcode', 'string', ['limit' => 32, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '条形码'])
            ->addColumn('thumbnail', 'string', ['limit' => 100, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品默认图'])
            ->addColumn('key', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '销售属性值组成的key(从小到大用排序)'])
            ->addColumn('spec_info', 'text', ['null' => true, 'collation' => 'utf8mb4_unicode_ci', 'comment' => '规格描述'])
            ->addColumn('more', 'text', ['null' => true, 'collation' => 'utf8mb4_unicode_ci', 'comment' => '扩展属性'])
            ->create();
    }
}