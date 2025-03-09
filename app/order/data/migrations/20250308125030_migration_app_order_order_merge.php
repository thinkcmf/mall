<?php
use think\migration\Migrator;

class MigrationAppOrderOrderMerge extends Migrator
{
    public function change(): void
    {
        $table = $this->table('order_merge', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '合并订单表']);
        $table->addColumn('order_ids', 'text', ['null' => false, 'collation' => 'utf8mb4_unicode_ci', 'comment' => '合并的订单ID列表'])
              ->addColumn('pay_status', 'integer', ['limit' => 4, 'default' => 0, 'comment' => '支付状态(0:未支付;1:已支付;10:等待支付)'])
              ->create();
    }
}