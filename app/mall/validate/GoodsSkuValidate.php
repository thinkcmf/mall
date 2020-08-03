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

class GoodsSkuValidate extends Validate
{
    protected $rule = [
        'title'         => 'require',
        'price'         => 'require',
        // TODO: 不指导为啥，多个条件验证都是失败的结果
        // 'price'         => 'require｜number|gt:0.00',
    ];

    protected $message = [
        'title.require'     => '名称不能为空',
        'price.require'     => '价格不能为空',
        'price.number'      => '价格必须为数字',
        'price.gt'          => '价格必须大于0',
    ];
}
