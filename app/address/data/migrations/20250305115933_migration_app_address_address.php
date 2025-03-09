<?php

use think\migration\Migrator;

class MigrationAppAddressAddress extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('address_user', ['id' => true, 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci']);
        $table->addColumn('user_id', 'integer', ['default' => 0, 'comment' => '用户id'])
            ->addColumn('country', 'integer', ['default' => 0, 'comment' => '国家'])
            ->addColumn('province', 'integer', ['default' => 0, 'comment' => '省份'])
            ->addColumn('city', 'integer', ['default' => 0, 'comment' => '城市'])
            ->addColumn('district', 'integer', ['default' => 0, 'comment' => '地区'])
            ->addColumn('town', 'integer', ['default' => 0, 'comment' => '乡镇'])
            ->addColumn('province_name', 'string', ['limit' => 20, 'default' => '', 'comment' => '省份名'])
            ->addColumn('city_name', 'string', ['limit' => 20, 'default' => '', 'comment' => '市名'])
            ->addColumn('district_name', 'string', ['limit' => 20, 'default' => '', 'comment' => '地区名'])
            ->addColumn('town_name', 'string', ['limit' => 20, 'default' => '', 'comment' => '乡镇名'])
            ->addColumn('consignee', 'string', ['limit' => 20, 'default' => '', 'comment' => '收货人'])
            ->addColumn('alias', 'string', ['limit' => 20, 'default' => '', 'comment' => '地址别名(方便用户记忆)'])
            ->addColumn('email', 'string', ['limit' => 60, 'default' => '', 'comment' => '邮箱地址'])
            ->addColumn('address', 'string', ['limit' => 120, 'default' => '', 'comment' => '地址'])
            ->addColumn('zip_code', 'string', ['limit' => 10, 'default' => '', 'comment' => '邮政编码'])
            ->addColumn('mobile', 'string', ['limit' => 30, 'default' => '', 'comment' => '手机'])
            ->addColumn('mobile2', 'string', ['limit' => 30, 'default' => '', 'comment' => '备用手机号'])
            ->addColumn('is_default', 'tinyinteger', ['default' => 0, 'comment' => '是否为默认收货地址:0=否,1=是'])
            ->create();
    }
}