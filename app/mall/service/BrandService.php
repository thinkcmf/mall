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
namespace app\mall\service;

use app\mall\model\MallBrandModel;

/**
 * 服务提供： 品牌
 * 
 */
class BrandService extends BaseService
{

    /**
     * 获取数据列表
     */
    public static function get($map = [], $orderby='list_order', $field = '', $deleted = false)
    {
        if($deleted){
            $map[] = ['delete_time', '>', 0];
        }else{
            $map[] = ['delete_time', '=', 0];
        }

        $model = MallBrandModel::where($map);

        $model = $model->order($orderby);

        if(!empty($field)){
            $model = $model->field($field);
        }
        
        return $model->select();
    }

    /**
     * 获取KeyVal列表
     */
    public static function getKv($valField='name', $keyField='id')
    {
        $data = self::get()->toArray();
        return array_column($data, $valField, $keyField);
    }

}