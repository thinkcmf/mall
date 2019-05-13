<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ccbox <ccbox.net@163.com>
// +----------------------------------------------------------------------
namespace app\order\service;

use think\Db;

class ApiService
{
    /**
     * 获取购物商品信息，并计算总价
     * 
     * @author ccbox <ccbox.net@163.com>
     *
     * @param array $cartList [ [ 'goods_id'=>0,'goods_sku_id'=>0,'selected'=>0,'goods_quantity'=>1 ] ]
     * @return array
     */
    public static function getCartInfo($cartList, $updateCart=false, $checkout=false)
    {
        $return = [
            'cart'      => [],
            'goods'     => [],
            'shops'     => [],
            'shopscart' => [],
            'total'     => 0,
            'selected'  => 0,
            'invalid'  => 0,
            'amount'    => 0.00,
        ];
        if (!empty($cartList)) {

            $goodsSkus = [];
            // $skuIds = array_filter( array_column($cartList, 'goods_sku_id'), function($value){
            //     return $value>0;
            // });
            // if (!empty($skuIds)) {
            //     $skus = Db::name('MallItemSku')->where('id', 'in', $skuIds)->select()->toArray();
            //     foreach ($skus as $sku) {
            //         $goodsSkus[$sku['item_id']]['skus'][$sku['id']] = $sku;
            //     }
            // }

            $goods = [];
            $goodsIds = array_column($cartList, 'goods_id');

            $skus = Db::name('MallItemSku')->where('item_id', 'in', $goodsIds)->select()->toArray();
            if(!empty($skus)){
                foreach ($skus as $sku) {
                    $goodsSkus[$sku['item_id']]['skus'][$sku['id']] = $sku;
                }
            }

            $goodsSrc = Db::name('MallItem')->where('id', 'in', $goodsIds)->field('content',true)->select()->toArray();
            foreach($goodsSrc as $oneGoods){
                $goods[$oneGoods['id']] = $oneGoods;
                if(isset($goodsSkus[$oneGoods['id']])){
                    $goods[$oneGoods['id']]['skus'] = $goodsSkus[$oneGoods['id']]['skus'];
                }else{
                    $oneGoods['spec_info'] = '';
                    $goods[$oneGoods['id']]['skus'] = [0 => $oneGoods];
                }
            }
            $return['goods'] = $goods;
            
            $shops = [];
            $shopIds = array_filter( array_column($goods, 'shop_id'), function($value){ return $value>0; });
            if(!empty($shopIds)){
                $shops = Db::name('shop')->where('id', 'in', $shopIds)->select()->toArray();
            }
            $return['shops'] = $shops;

            $shopscart = [];
            $total = 0;
            $selected = 0;
            $invalid = 0;
            $amount = 0.00;
            foreach($cartList as &$cartItem){
                $itemBeInvalid = 0;
                $itemSelected = 0;
                $itemAmount = 0.00;
                $cartGoods = $goods[$cartItem['goods_id']];
                $cartItem['status'] = 0;
                $shop_id = $goods[$cartItem['goods_id']]['shop_id'];
                if(!isset($shopscart[$shop_id])){
                    $shopscart[$shop_id] = [
                        'goods' => [],
                        'total'     => 0,
                        'selected'  => 0,
                        'invalid'  => 0,
                        'amount'    => 0.00,
                    ];
                }

                if( $cartGoods['status'] ){
                    if($cartItem['goods_sku_id']>0){
                        if (isset($cartGoods['skus'][$cartItem['goods_sku_id']])) {
                            $cartGoodsSku = $cartGoods['skus'][$cartItem['goods_sku_id']];
                            if ($cartGoodsSku['status']) {
                                $cartItem['status'] = 1;
                                $itemBeInvalid = 1;
                                $itemSelected = $cartItem['goods_quantity'];
                                $itemAmount = bcmul($cartGoodsSku['price'], $cartItem['goods_quantity'], 2);
                            }
                        }else{
                            $return['error'] = ['er04', '请选择要结算的商品SKU。'];
                            return $return;
                        }
                    }else{
                        $cartItem['status'] = 1;
                        $itemBeInvalid = 1;
                        $itemSelected = $cartItem['goods_quantity'];
                        $itemAmount = bcmul($cartGoods['price'], $cartItem['goods_quantity'], 2);
                    }
                }
                $shopscart[$shop_id]['goods'][] = $cartItem;
                
                if($itemBeInvalid){
                    $total++;
                    $shopscart[$shop_id]['total']++;
                }else{
                    $invalid++;
                    $shopscart[$shop_id]['invalid']++;
                }
                if($cartItem['selected']){
                    $selected = $selected + $itemSelected;
                    $shopscart[$shop_id]['selected'] = $shopscart[$shop_id]['selected'] + $itemSelected;
                    $amount = bcadd($amount, $itemAmount,2);
                    $shopscart[$shop_id]['amount'] = bcadd($shopscart[$shop_id]['amount'], $itemAmount,2);
                    if($updateCart && !$cartItem['status']){
                        Db::name('OrderCart')->where(['id' => $cartItem['id']])->update(['selected' => 0]);
                    }
                }
            }
            $return['cart'] = $cartList;
            $return['shopscart'] = $shopscart;
            $return['total'] = $total;
            $return['selected'] = $selected;
            $return['invalid'] = $invalid;
            $return['amount'] = $amount;

        }

        if($checkout){
            if (empty($return['cart'])) {
                $return['error'] = ['er01', '请选择要结算的商品。'];
            }elseif($return['invalid']>0){
                $return['error'] = ['er09','提交的商品中存在已失效的商品或者规格，请刷新后重新提交订单。'];
            }elseif( $return['total']<1 || $return['selected']<1 || $return['amount']<0.01 ){
                $return['error'] = ['er02', '系统错误，请联系客服。'];
            }
        }

        return $return;
    }

    /**
     * 获取用户购物车列表
     * 
     * @author ccbox <ccbox.net@163.com>
     *
     * @param int $userId
     * @return void
     */
    public static function getCartItems($userId, $checkout=false)
    {
        if($userId<1){
            return ['error' => ['er00','参数错误。']];
        }

        $map['user_id'] = $userId;
        if($checkout){
            $map['selected'] = 1;
            $updateCart = false;
        }else{
            $updateCart = true;
        }
        $cartList = Db::name('OrderCart')->where($map)->select()->toArray();
        return self::getCartInfo($cartList, $updateCart, $checkout);
    }
    
    /**
     * 获取立刻购买商品信息
     * 
     * @author ccbox <ccbox.net@163.com>
     *
     * @param int $itemId
     * @param int $skuId
     * @param int $number
     * @return void
     */
    public static function getBuynowItems($itemId, $skuId=0, $number=1)
    {
        if($itemId<1){
            return ['error' => ['er00','参数错误。']];
        }
        if($skuId<1){
            $hadSku = Db::name('MallItemSku')->where(['item_id'=>$itemId])->count();
            if($hadSku>0){
                return ['error' => ['er04','参数错误：SKU的ID错误。']];
            }
        }else{
            $hadSku = Db::name('MallItemSku')->where(['item_id'=>$itemId,'id'=>$skuId])->count();
            if($hadSku<1){
                return ['error' => ['er04','参数错误：传入的SKU错误。']];
            }
        }
        $cartList[] = [
            'goods_id'      => $itemId,
            'goods_sku_id'  => $skuId,
            'selected'      => 1,
            'goods_quantity'=> $number
        ];
        
        return self::getCartInfo($cartList, false, true);
    }
    
    /**
     * 获取订单结算商品信息
     * 
     * @author ccbox <ccbox.net@163.com>
     *
     * @param int $itemId
     * @param int $skuId
     * @param int $number
     * @return void
     */
    public static function getSubmitItems($items)
    {
        if(empty($items)){
            return ['error' => ['er00','参数错误。']];
        }
        $return = self::getCartInfo($items, false, true);
        return $return;
    }
    
}