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
namespace api\mall\service;

use api\mall\model\CartModel;
use app\mall\model\MallGoodsSkuModel;

/**
 * 服务提供： 购物车
 *
 */
class CartService
{

    /**
     * 购物车商品列表
     */
    public static function getCart($userId)
    {
        $data = CartModel::all(
            function ($query) use ($userId) {
                $query->where('user_id', $userId);
                $query->where('status', 1);
                $query->order([
                    'update_time' => 'desc'
                ]);
            },
            ['goods' => function ($query) {
                $query->field('id,title,subtitle,status,thumbnail');
            }, 'sku' => function ($query) {
                $query->field('id,title,status,subtitle,thumbnail,price_market,price,stock');
            }]
        )->map(function ($item) {
            return self::cartItemFilter($item);
        });

        return $data;
    }

    /**
     * 购物车商品列表数据过滤
     */
    public static function cartItemFilter($item)
    {
        $goods = $item->goods;
        $goods['thumbnail_url'] = cmf_get_image_url($goods['thumbnail']);
        $sku = $item->sku;
        $sku['thumbnail_url'] = cmf_get_image_url($sku['thumbnail']);

        return [
            'id'          => $item->id,
            // 'create_time' => $item->create_time,
            // 'update_time' => $item->update_time,
            // 'delete_time' => $item->delete_time,
            'status'      => $item->status,
            'list_order'  => $item->list_order,
            // 'user_id'     => $item->user_id,
            // 'goods_table' => $item->goods_table,
            'goods_id'    => $item->goods_id,
            // 'sku_table'   => $item->sku_table,
            'sku_id'      => $item->sku_id,
            'quantity'    => $item->quantity,
            'selected'    => $item->selected,
            'goods'       => $goods,
            'sku'         => $sku,
        ];
    }

    /**
     * 购物车商品操作
     */
    public static function modifyCart($userId, $goodsId, $skuId, $number = 1, $selected = 1)
    {
        $res = [
            'error' => null,
            'data' => []
        ];

        if ($userId < 1 || $goodsId < 1 || $skuId < 1 || $number < 1) {
            $res['error'] = '参数错误';
            return $res;
        }

        $checkSku = self::checkSku($goodsId, $skuId);
        if ($checkSku['error'] && $checkSku['data'] == 999) {
            return $checkSku;
        }

        $map = [];
        $map['user_id'] = $userId;
        // $map['goods_id'] = $goodsId;
        $map['sku_id'] = $skuId;

        $itemInCart = CartModel::where($map)->find();

        $status = 1;
        if ($itemInCart) {
            $cart = $itemInCart;
            if ($checkSku['error']) {
                $res = $checkSku;
                // $status   = 0;
                $selected = 0;
            }
        } else {
            if ($checkSku['error']) {
                return $checkSku;
            }
            $cart = new CartModel();
        }

        $cart->status   = $status;
        $cart->user_id  = $userId;
        $cart->goods_id = $goodsId;
        $cart->sku_id   = $skuId;
        $cart->quantity = $number;
        $cart->selected = $selected ? 1 : 0;

        $data = $cart->save();

        $res['data'] = $data;
        return $res;
    }

    /**
     * 购物车操作： 删除、选中、取消选中
     */
    public static function setCart($action, $userId, $goodsId, $skuId)
    {
        $res = [
            'error'  => null,
            'data'   => [],
            'action' => '',
        ];

        if ($userId < 1 || $goodsId < 1 || $skuId < 1) {
            $res['error'] = '参数错误';
            return $res;
        }

        $map = [];
        $map['user_id'] = $userId;
        // $map['goods_id'] = $goodsId;
        $map['sku_id'] = $skuId;

        switch ($action) {
            case 'delete':
                $data['status'] = 0;
                $data['quantity'] = 0;
                $data['selected'] = 0;
                $res['action'] = '删除';
                break;
            case 'select':
                // $data['status'] = 0;
                // $data['quantity'] = 0;
                $data['selected'] = 1;
                $res['action'] = '选中';
                break;
            case 'unselect':
                // $data['status'] = 0;
                // $data['quantity'] = 0;
                $data['selected'] = 0;
                $res['action'] = '取消选中';
                break;
            default:
                $res['error'] = '非法操作';
                break;
        }

        if ($res['error']) {
            return $res;
        }

        $res['data'] = CartModel::where($map)->update($data);

        return $res;
    }

    /**
     * 检查SKU是否正常
     */
    public static function checkSku($goodsId, $skuId)
    {
        $res = [
            'error' => null,
            'data' => null
        ];
        $sku = MallGoodsSkuModel::get($skuId);

        if (empty($sku) || $sku->status != 1 || $sku->delete_time > 0) {
            $res['error'] = '所选产品SKU已经下架';
            $res['data'] = 1;
            return $res;
        }

        if ($sku->goods_id != $goodsId) {
            $res['error'] = '非法数据：商品ID与SKU不匹配';
            $res['data'] = 999;
            return $res;
        }

        $goods = $sku->goods;
        if ($goods->status != 1 || $goods['delete_time'] != 0) {
            $res['error'] = '所选产品已经下架';
            $res['data'] = 2;
            return $res;
        }

        $res['data'] = [
            'goods' => $goods,
            'sku' => $sku
        ];
        return $res;
    }
}
