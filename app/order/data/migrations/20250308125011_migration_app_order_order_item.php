<?php
use think\migration\Migrator;

class MigrationAppOrderOrderItem extends Migrator
{
    public function change(): void
    {
        $table = $this->table('order_item', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '订单商品表']);
        $table->addColumn('user_id', 'biginteger', ['signed' => false, 'default' => 0, 'comment' => '用户 ID'])
              ->addColumn('order_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '订单id'])
              ->addColumn('product_id', 'integer', ['signed' => false, 'null' => false, 'comment' => '商品id'])
              ->addColumn('product_sku_id', 'integer', ['signed' => false, 'null' => false, 'comment' => '商品SKU id'])
              ->addColumn('create_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '创建时间'])
              ->addColumn('update_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '更新时间'])
              ->addColumn('confirm_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '订单确认时间'])
              ->addColumn('expire_time', 'integer', ['signed' => false, 'default' => 0, 'comment' => '支付过期时间'])
              ->addColumn('product_quantity', 'integer', ['signed' => false, 'default' => 1, 'comment' => '购买数量'])
              ->addColumn('product_quantity_locked', 'integer', ['signed' => false, 'default' => 0, 'comment' => '已锁定商品数量'])
              ->addColumn('original_price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '商品原价'])
              ->addColumn('product_price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '实际支付价格'])
              ->addColumn('reward_score', 'integer', ['limit' => 8, 'default' => 0, 'comment' => '购买商品赠送积分'])
              ->addColumn('table_name', 'string', ['limit' => 50, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '订购物品实体以前所在表，不带前缀'])
              ->addColumn('product_name', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '物品名称'])
              ->addColumn('product_thumbnail', 'string', ['limit' => 100, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品缩略图'])
              ->addColumn('product_spec', 'string', ['limit' => 500, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品规格'])
              ->addColumn('product_sn', 'string', ['limit' => 60, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '商品货号'])
              ->addColumn('more', 'text', ['null' => true, 'collation' => 'utf8mb4_unicode_ci', 'comment' => '扩展属性'])
              ->create();
    }
}