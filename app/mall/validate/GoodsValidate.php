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

class GoodsValidate extends Validate
{
    protected $rule = [
        'title'         => 'require',
        'thumbnail'     => 'require',
    ];

    protected $message = [
        'title.require'     => '名称不能为空',
        'thumbnail.require' => '缩略图不能为空',
    ];
}
