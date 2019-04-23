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

class UserAddressValidate extends Validate
{
    protected $rule = [
        'consignee' => 'require',
        'province'  => 'require',
        'address'   => 'require',
        'mobile'    => 'require',
    ];
    protected $message = [
        'consignee.require' => '收件人不能为空!',
        'province.require'  => '收件址不能为空!',
        'address.require'   => '详细地址不能为空!',
        'mobile.require'    => '手机不能为空!',
    ];

    protected $scene = [
    ];
}