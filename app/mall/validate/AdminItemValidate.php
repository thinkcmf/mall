<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 五五 <15093565100@163.com>
// +----------------------------------------------------------------------
namespace app\mall\validate;

use think\Validate;

class AdminItemValidate extends Validate
{
    protected $rule = [
        'category_id'    => 'require',
        'title'          => 'require',
        'brand_id'       => 'require',
        'thumbnail'      => 'require',
        'price'          => 'require',
        'original_price' => 'require',
        'quantity'       => 'require',
        'content'        => 'require',

    ];
    protected $message = [
        'category_id.require'    => '请选择宝贝分类!',
        'title.require'          => '宝贝标题不能为空!',
        'brand_id.require'       => '请选择宝贝品牌!',
        'thumbnail.require'      => '请上传宝贝图片!',
        'price.require'          => '请填写宝贝价格!',
        'original_price.require' => '请填写宝贝原价!',
        'quantity.require'       => '宝贝数量不能为空!',
        'content.require'        => '请填写宝贝描述!',
    ];

}