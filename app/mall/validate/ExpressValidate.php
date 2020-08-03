<?php
// +----------------------------------------------------------------------
// | CMFMall_2020
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 达达 <ccbox.net@163.com>
// +----------------------------------------------------------------------
namespace app\mall\validate;

use think\Validate;

class ExpressValidate extends Validate
{
    protected $rule = [
        'name'          => 'require',
    ];

    protected $message = [
        'name.require'      => '名称不能为空',
    ];
}
