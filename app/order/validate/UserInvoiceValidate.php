<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\order\validate;

use think\Validate;

class UserInvoiceValidate extends Validate
{
    protected $rule = [
        'title'        => 'require',
        'taxpayer_id'  => 'require',
        'address'      => 'require',
        'phone'        => 'require',
        'bank_name'    => 'require',
        'bank_account' => 'require',
        'consignee'    => 'require',
    ];
    protected $message = [
        'title.require'        => '单位名称不能为空!',
        'taxpayer_id.require'  => '纳税人识别码不能为空!',
        'address.require'      => '注册地址不能为空!',
        'phone.require'        => '注册电话不能为空!',
        'bank_name.require'    => '开户银行不能为空!',
        'bank_account.require' => '银行账户不能为空!',
        'consignee.require'    => '请填写收票人！'
    ];

    protected $scene = [
        'default' => ['title'],
        'normal'  => ['title', 'taxpayer_id', 'consignee'],
        'special' => ['title', 'taxpayer_id', 'phone', 'address', 'bank_name', 'bank_account', 'consignee'],
    ];
}