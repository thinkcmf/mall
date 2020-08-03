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

use app\mall\model\MallGoodsModel;
use app\mall\model\MallGoodsSkuModel;

/**
 * 服务提供： 商品（后台）
 * 
 */
class GoodsService extends BaseService
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

        $model = MallGoodsModel::where($map);

        $model = $model->order($orderby);

        if (!empty($field)) {
            $model = $model->field($field);
        }

        return $model->select();
    }

    /**
     * 获取商品的SKU数据列表
     */
    public static function getSku($goodsId, $map = [], $orderby = 'list_order', $field = '', $deleted = false)
    {
        $map[] = ['goods_id', '=', $goodsId];

        if ($deleted) {
            $map[] = ['delete_time', '>', 0];
        } else {
            $map[] = ['delete_time', '=', 0];
        }

        $model = MallGoodsSkuModel::where($map);

        $model = $model->order($orderby);

        if (!empty($field)) {
            $model = $model->field($field);
        }

        return $model->select();
    }

    /**
     * 更新商品价格字段
     */
    public static function priceRefresh($goodsId)
    {
        $skus = self::getSku($goodsId, [], 'price', 'id,price_market,price');

        if($skus){
            $skuCount = $skus->count();
            $minSku = $skus[0];
            $maxSku = $skus[$skuCount - 1];

            $data = [
                'id' => $goodsId,
                'price_min' => $minSku['price'],
                'price_max' => $maxSku['price'],
                'price_market_min' => $minSku['price_market'],
                'price_market_max' => $maxSku['price_market']
            ];

            MallGoodsModel::update($data);
        }
    }
}
