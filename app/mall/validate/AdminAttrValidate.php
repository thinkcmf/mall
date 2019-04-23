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
use app\mall\model\MallModelModel;
use think\Validate;

class AdminAttrValidate extends Validate
{
    protected $rule = [
        'model_id' => 'require|checkModelId',
        'name'     => 'require|checkName',
        'type'     => 'require|checkType',
    ];
    protected $message = [
        'name.require'     => '属性名称不能为空!',
        'type.require'     => '请选择属性类型!',
        'model_id.require' => '请指定商品模型'
    ];

    protected function checkType($value)
    {
        $types = [1, 2];
        if (in_array($value, $types)) {
            return true;
        } else {
            return '属性值非法！';
        }
    }

    protected function checkName($name, $rule,$data)
    {
        $name = trim($name);
        if (empty($name)) {
            return '属性名称不能为空！';
        }

        $attrModel = new MallAttrModel();
        $findAttr  = $attrModel->where('model_id', intval($data['model_id']))->where('name', $name)->find();

        if (!empty($findAttr)) {
            return '模型属性名称已经存在！';
        }

        return true;
    }

    protected function checkModelId($value)
    {
        $modelModel = MallModelModel::get($value);

        if (empty($modelModel)) {
            return '模型不存在！';
        }

        return true;
    }


}