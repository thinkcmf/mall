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

class OrderShipmentValidate extends Validate
{
    protected $rule = [
        'name' => 'require',
        'code'  => 'require',
    ];

    protected $message = [
        'name.require' => '名称不能为空',
        'code.require'  => '代码不能为空',
    ];

}