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
use app\mall\model\MallItemSkuModel;
use app\mall\model\MallModelModel;
use think\Validate;

class AdminSkuValidate extends Validate
{
    protected $rule = [
        'item_id'        => 'require',
        'attrs'          => 'checkAttrs',
        'price'          => 'require',
        'original_price' => 'require',
        'cost_price'     => 'require',
    ];
    protected $message = [
        'item_id.require'        => '请选择商品!',
        'attrs.checkAttrs'       => '商品属性不正确!',
        'price.require'          => '请输入商品价格!',
        'cost_price.require'     => '请输入商品成本价!',
        'original_price.require' => '请输入商品原价！'
    ];

    protected function checkAttrs($attrs, $rule, $data)
    {
        $isEmpty = true;
        $notFind = false;

        $keyArr = [];

        $attrValueModel = new MallAttrValueModel();

        foreach ($attrs as $attrId => $valueId) {
            if (!empty($valueId)) {
                $isEmpty = false;
                array_push($keyArr, "$attrId:$valueId");
            }

            $findAttrValue = $attrValueModel->where('id', $valueId)->where('attr_id', $attrId)->find();

            if (empty($findAttrValue)) {
                $notFind = false;
            }
        }

        if ($notFind) {
            return '商品属性不正确!';
        }

        if ($isEmpty) {
            return '商品属性不能全为空!';
        }

        $key = implode(';', $keyArr);


        $itemSkuModel = new MallItemSkuModel();
        $skuId        = empty($data['id']) ? 0 : intval($data['id']);
        $findSku      = $itemSkuModel->where('delete_time', 0)->where('id', 'neq', $skuId)->where('item_id', intval($data['item_id']))->where('key', $key)->find();

        if (!empty($findSku)) {
            return "此商品规格已经存在！";
        }

        return true;
    }

    protected function checkType($value)
    {
        $types = [1, 2];
        if (in_array($value, $types)) {
            return true;
        } else {
            return '属性值非法！';
        }
    }

    protected function checkName($name, $rule, $data)
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