<?php
use think\migration\Migrator;

class MigrationAppShipmentShipment extends Migrator
{
    public function change(): void
    {
        $table = $this->table('shipment', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '快递']);
        $table->addColumn('status', 'integer', ['limit' => 3, 'signed' => false, 'default' => 0, 'comment' => '是否开启(1:开启;0:禁用)'])
              ->addColumn('list_order', 'float', ['default' => 10000, 'comment' => '排序'])
              ->addColumn('code', 'string', ['limit' => 20, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '快递代号'])
              ->addColumn('name', 'string', ['limit' => 30, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '快递名称'])
              ->addColumn('description', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '描述'])
              ->addIndex(['code', 'status'], ['name' => 'shipping_code'])
              ->create();
    }
}