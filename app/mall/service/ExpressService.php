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

use app\mall\model\ExpressModel;
use app\mall\model\ExpressFeeModel;
use think\Db;

/**
 * 服务提供： 物流（后台）
 * 
 */
class ExpressService extends BaseService
{

    /**
     * 获取数据列表
     */
    public static function get($map = [], $orderby = 'list_order', $field = '', $deleted = false)
    {
        if ($deleted) {
            $map[] = ['delete_time', '>', 0];
        } else {
            $map[] = ['delete_time', '=', 0];
        }

        $model = ExpressModel::where($map);

        $model = $model->order($orderby);

        if (!empty($field)) {
            $model = $model->field($field);
        }

        return $model->select();
    }

    /**
     * 获取物流费用数据列表
     */
    public static function getFee($expressId, $map = [], $orderby = 'list_order', $field = '', $deleted = false)
    {
        $map[] = ['express_id', '=', $expressId];

        if ($deleted) {
            $map[] = ['delete_time', '>', 0];
        } else {
            $map[] = ['delete_time', '=', 0];
        }

        $model = ExpressFeeModel::where($map);

        $model = $model->order($orderby);

        if (!empty($field)) {
            $model = $model->field($field);
        }

        return $model->select();
    }

    /**
     * 获取省市地区信息
     */
    public static function getProvinces($turnKv = false)
    {
        $data = hook_one('area_provinces');
        if(false === $data){

            return '没有安装地区插件';
        }
        if ($turnKv) {
            $data = array_column($data, 'name', 'id');
        }
        return $data;
    }

    /**
     * 按省份计算物流费用
     * 重量单位：KG
     */
    public static function getShipfee(string $province, float $weight, int $expressId)
    {
        $provinceHead = mb_substr($province, 0, 2);

        $fee = ExpressFeeModel::get(function ($query) use ($expressId, $province, $provinceHead) {
            $query->where('express_id', $expressId);
            $query->where(function ($que) use ($province, $provinceHead) {
                $que->where('province', 'like', '%' . $province . '%');
                $que->whereOr('province', 'like', '%' . $provinceHead . '%');
            });
        });

        if (empty($fee)) {
            $com = ExpressModel::get(function ($query) use ($expressId) {
                $query->where('id', '=', $expressId);
                $query->where('status', '=', 1);
                $query->where('delete_time', '=', 0);
            });
            $fee = $com;
        } else {
            $com = $fee->express;
        }

        if (empty($com) || $com->status != 1 || $com->delete_time > 0) {
            return ['error' => [$expressId => '查询的物流公司失效。'], 'data' => []];
        }

        if ($weight > $fee->base_weight) {
            $base_weight = $fee->base_weight;
            $next_weight = $weight - $fee->base_weight;
            $next_times = ceil($next_weight / $fee->next_weight);
        } else {
            $base_weight = $weight;
            $next_weight = 0;
            $next_times = 0;
        }
        $next_fee = $next_times * $fee->next_fee;
        $result = $fee->base_fee + $next_fee;

        return [
            'error' => null,
            'data' => [
                'shipfee' => $result,
                'shipfee_info' => [
                    'weight' => $weight,
                    'base_weight' => $base_weight,
                    'base_fee' => $fee->base_fee,
                    'next_weight' => $next_weight,
                    'next_fee' => $next_fee,
                ],
                'express_info' => [
                    'id' => $com->id,
                    'name' => $com->name,
                    'alias' => $com->alias,
                    'base_weight' => $fee->base_weight,
                    'base_fee' => $fee->base_fee,
                    'next_weight' => $fee->next_weight,
                    'next_fee' => $fee->next_fee,
                ]
            ]
        ];
    }
}
