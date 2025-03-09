<?php
use think\migration\Migrator;

class MigrationAppOrderOrderPayment extends Migrator
{
    public function change(): void
    {
        $table = $this->table('order_payment', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8_unicode_ci', 'comment' => '支付方式表']);
        $table->addColumn('is_online', 'integer', ['limit' => 3, 'signed' => false, 'default' => 1, 'comment' => '是否在线支付'])
              ->addColumn('is_cod', 'integer', ['limit' => 3, 'signed' => false, 'default' => 0, 'comment' => '是否货到付款(cash on delivery (COD))'])
              ->addColumn('is_prepay', 'integer', ['limit' => 3, 'signed' => false, 'default' => 1, 'comment' => '是否先付费;0:否;1:是'])
              ->addColumn('status', 'integer', ['limit' => 3, 'signed' => false, 'default' => 1, 'comment' => '状态(1:开启;0:关闭)'])
              ->addColumn('fee', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00', 'comment' => '手续费'])
              ->addColumn('list_order', 'float', ['precision' => 5, 'scale' => 2, 'signed' => false, 'default' => '0.00', 'comment' => '排序'])
              ->addColumn('code', 'string', ['limit' => 20, 'default' => '', 'collation' => 'utf8_unicode_ci', 'comment' => '支付code'])
              ->addColumn('name', 'string', ['limit' => 30, 'default' => '', 'collation' => 'utf8_unicode_ci', 'comment' => '支付方式名称'])
              ->addColumn('description', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8_unicode_ci', 'comment' => '描述'])
              ->addColumn('tips', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8_unicode_ci', 'comment' => '提示'])
              ->addColumn('config', 'text', ['null' => true, 'collation' => 'utf8_unicode_ci', 'comment' => '配置'])
              ->addIndex(['code'], ['unique' => true, 'name' => 'pay_code'])
              ->create();
    }
}