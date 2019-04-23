<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 <449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\mall\validate;

use think\Validate;

class AdminBrandValidate extends Validate
{
    protected $rule    = [
        'name'  => 'require',
        'alias' => 'require',
        'url'   => 'url',
        'logo'  => 'require',
    ];
    protected $message = [
        'name.require'  => '品牌名称不能为空!',
        'alias.require' => '品牌别名不能为空!',
        'url'           => '品牌网址格式不对!请带上：http或者https',
        'logo.require'  => '请上传品牌Logo!',
    ];

}