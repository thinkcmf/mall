<?php
use think\migration\Migrator;

class MigrationAppInvoiceInvoiceUser extends Migrator
{
    public function change(): void
    {
        $table = $this->table('invoice_user', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '用户发票信息']);
        $table->addColumn('user_id', 'biginteger', ['signed' => false, 'default' => 0, 'comment' => '用户 id'])
              ->addColumn('type', 'integer', ['limit' => 3, 'signed' => false, 'default' => 1, 'comment' => '发票类型;1: 个人;2:增值税普通发票;3:增值税专用发票;'])
              ->addColumn('title', 'string', ['limit' => 255, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '发票抬头，（个人姓名，或公司名称）'])
              ->addColumn('taxpayer_id', 'string', ['limit' => 30, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '纳税人识别码'])
              ->addColumn('phone', 'string', ['limit' => 20, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '公司电话'])
              ->addColumn('address', 'string', ['limit' => 100, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '公司地址'])
              ->addColumn('bank_name', 'string', ['limit' => 60, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '开户行'])
              ->addColumn('bank_account', 'string', ['limit' => 30, 'default' => '', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '银行账号'])
              ->addColumn('consignee_info', 'text', ['null' => true, 'collation' => 'utf8mb4_unicode_ci', 'comment' => '收件人信息'])
              ->create();
    }
}