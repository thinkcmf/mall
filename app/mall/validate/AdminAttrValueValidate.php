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

use app\mall\model\MallAttrModel;
use app\mall\model\MallAttrValueModel;
use think\Validate;

class AdminAttrValueValidate extends Validate
{
    protected $rule = [
        'attr_id' => 'require|checkAttrId',
        'value'   => 'require|checkValue',
    ];
    protected $message = [
        'value.require'   => '属性值不能为空!',
        'attr_id.require' => '请指定商品模型属性'
    ];

    protected function checkValue($value, $rule, $data)
    {
        $value = trim($value);
        if (empty($value)) {
            return '请删除多余空格！';
        }

        $attrValueModel = new MallAttrValueModel();
        $findAttrValue  = $attrValueModel->where('attr_id', intval($data['attr_id']))->where('value', $value)->find();

        if (!empty($findAttrValue)) {
            return '模型属性值已经存在！';
        }

        return true;
    }

    protected function checkAttrId($value)
    {
        $attr = MallAttrModel::get($value);

        if (empty($attr)) {
            return '模型属性不存在！';
        }

        return true;
    }


}