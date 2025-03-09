<?php

use think\migration\Migrator;

class MigrationAppProductProduct extends Migrator
{
    public function change(): void
    {
        $table = $this->table('product', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品']);
        $table ->addColumn('shop_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '店铺id'])
            ->addColumn('category_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '商品分类ID'])
            ->addColumn('brand_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '品牌ID'])
            ->addColumn('model_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '商品模型ID'])
            ->addColumn('quantity', 'integer', ['signed' => false, 'default' => 0, 'comment' => '商品数量'])
            ->addColumn('spec_type', 'integer', ['signed' => false, 'default' => 0, 'comment' => '规格类型:0=单规格,1=多规格'])
            ->addColumn('title', 'string', ['limit' => 100, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品标题'])
            ->addColumn('subtitle', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品副标题'])
            ->addColumn('product_sn', 'string', ['limit' => 45, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品编号'])
            ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '商品价格'])
            ->addColumn('cost_price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '成本价格'])
            ->addColumn('original_price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '商品原价'])
            ->addColumn('video', 'string', ['limit' => 100, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品视频'])
            ->addColumn('thumbnail', 'string', ['limit' => 100, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '缩略图'])
            ->addColumn('list_order', 'float', ['default' => 0, 'comment' => '排序'])
            ->addColumn('view_count', 'biginteger', ['limit' => 20, 'signed' => false, 'default' => 0, 'comment' => '查看数'])
            ->addColumn('sell_count', 'biginteger', ['limit' => 20, 'signed' => false, 'default' => 0, 'comment' => '虚拟销量'])
            ->addColumn('favorite_count', 'biginteger', ['limit' => 20, 'signed' => false, 'default' => 0, 'comment' => '收藏数'])
            ->addColumn('like_count', 'biginteger', ['limit' => 20, 'signed' => false, 'default' => 0, 'comment' => '点赞数'])
            ->addColumn('create_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '最后更新时间'])
            ->addColumn('delete_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '删除时间'])
            ->addColumn('is_top', 'integer', ['limit' => 3, 'signed' => false, 'default' => 0, 'comment' => '是否置顶:1=置顶,0=不置顶'])
            ->addColumn('recommended', 'integer', ['limit' => 3, 'signed' => false, 'default' => 0, 'comment' => '是否推荐:1=推荐,0=不推荐'])
            ->addColumn('new_arrival', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '是否新品上市'])
            ->addColumn('is_hot', 'integer', ['limit' => 4, 'default' => 0, 'comment' => '是否为热卖商品'])
            ->addColumn('is_virtual', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '是否是虚拟商品'])
            ->addColumn('is_self', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '是否自营'])
            ->addColumn('barcode', 'string', ['limit' => 128, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品级别的条形码'])
            ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '状态'])
            ->addColumn('use_platform', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '使用平台:0=全部,1=pc,2=手机'])
            ->addColumn('content', 'text', ['null' => true, 'collation' => 'utf8mb4_unicode_ci', 'comment' => '内容'])
            ->addColumn('more', 'text', ['null' => true, 'collation' => 'utf8mb4_unicode_ci', 'comment' => '扩展属性'])
            ->create();
    }
}