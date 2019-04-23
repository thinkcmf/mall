<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <catmant@thinkcmf.com>
// +----------------------------------------------------------------------
namespace app\mall\model;

use think\Model;

class MallItemSkuModel extends Model
{
    /**
     * 开启时间字段自动写入
     */
    protected $autoWriteTimestamp = true;

    protected $type = [
        'more' => 'array',
    ];

    /**
     * content 自动转化
     * @param $value
     * @return string
     */
    public function getAttrValuesAttr($value, $data)
    {
        $attrValues = [];
        if (!empty($data['key'])) {

            $key = explode(';', $data['key']);
            foreach ($key as $attrValue) {
                $attrValue                 = explode(':', $attrValue);
                $attrValues[$attrValue[0]] = $attrValue[1];
            }

        }

        return $attrValues;

    }

}