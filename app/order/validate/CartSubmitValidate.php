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

class CartSubmitValidate extends Validate
{
    protected $rule = [
        'user_address_id' => 'require',
    ];
    protected $message = [
        'user_address_id.require' => '请选择收货地址！',
    ];

    protected $scene = [
    ];
}