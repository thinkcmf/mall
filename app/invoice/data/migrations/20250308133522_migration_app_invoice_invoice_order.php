<?php
use think\migration\Migrator;

class MigrationAppInvoiceInvoiceOrder extends Migrator
{
    public function change(): void
    {
        $table = $this->table('invoice_order', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '发票订单']);
        $table->addColumn('invoice_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '发票 id'])
              ->addColumn('order_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '订单 id'])
              ->addIndex(['invoice_id'], ['name' => 'invoice_id'])
              ->addIndex(['order_id'], ['name' => 'order_id'])
              ->create();
    }
}