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
namespace app\order\controller;

use cmf\controller\UserBaseController;
use think\Db;

class CartController extends UserBaseController
{

    /**
     * 购物车首页
     *
     * @return void
     */
    public function index()
    {
        $userId      = cmf_get_current_user_id();
        $getCart = \app\order\service\ApiService::getCartItems($userId);
        if(isset($getCart['error'])){
            $this->error($getCart['error'][1]);
        }

        $this->assign('goods', $getCart['goods']);
        $this->assign('shops_cart', $getCart['shopscart']);
        $this->assign('shops', $getCart['shops']);
        $this->assign('total_amount', $getCart['amount']);
        return $this->fetch();
    }

    /**
     * 购物下单页面
     * 增加了直接购买的流程，购物提交商品采用统一接口获取和计算
     * 
     * @author ccbox <ccbox.net@163.com>
     * 
     * @return void
     */
    public function confirm()
    {
        $userId      = cmf_get_current_user_id();
        $currentTime = time();

        $buynow       = $this->request->param('buynow', 0, 'intval');
        $this->assign('buynow', $buynow);

        if($buynow==1){
            // 直接购买流程
            $itemId       = $this->request->param('id', 0, 'intval');
            $skuId       = $this->request->param('sku', 0, 'intval');
            $number       = $this->request->param('num', 0, 'intval');
            if($itemId>0 && $number>0){
                $getCart = \app\order\service\ApiService::getBuynowItems($itemId, $skuId, $number);
                if(isset($getCart['error'])){
                    $this->error($getCart['error'][1]);
                }
                if( $getCart['goods'][$itemId]['status']<1 
                    || $getCart['goods'][$itemId]['skus'][$skuId]['status']<1 
                ){
                    $this->error('商品已失效或下架。');
                }
            }else{
                $this->error('传入参数错误。');
            }
        }else{
            // 购物车购买流程
            $getCart = \app\order\service\ApiService::getCartItems($userId, true);
            if(!isset($getCart['error']) && $getCart['invalid']>0){
                $getCart['error'] = ['er09','提交的商品中存在已失效的商品或者规格。'];
            }
        }

        if (isset($getCart['error'])) {
            $this->error($getCart['error'][1]);
        }

        $userAddresses = Db::name('order_user_address')->where('user_id', $userId)->select();
        if (!empty($userAddresses)) {
            $areaIds = [];

            foreach ($userAddresses as $address) {
                array_push($areaIds, $address['province']);
                if (!empty($address['city'])) {
                    array_push($areaIds, $address['city']);
                }
                if (!empty($address['district'])) {
                    array_push($areaIds, $address['district']);
                }
            }

            $areas       = Db::name('Area')->where('id', 'in', $areaIds)->column('id,name', 'id');

            $this->assign('user_addresses', $userAddresses);
            $this->assign('areas', $areas);
        }

        $invoiceInfo = Db::name('order_user_invoice')->where('user_id', $userId)->find();

        if (!empty($invoiceInfo)) {
            $invoiceInfo['consignee_info'] = json_decode($invoiceInfo['consignee_info'], true);
            $this->assign('invoice_info', $invoiceInfo);
        }
        
        $this->assign('goods', $getCart['goods']);
        $this->assign('shops_cart', $getCart['shopscart']);
        $this->assign('shops', $getCart['shops']);
        $this->assign('total_amount', $getCart['amount']);
        return $this->fetch();
    }

    /**
     * 购物订单提交页面
     * 购买商品采用统一接口获取和计算
     * 
     * @author ccbox <ccbox.net@163.com>(update)
     * 
     * @return void
     */
    public function submit()
    {
        if ($this->request->isPost()) {

            $data   = $this->request->param();

            $result = $this->validate($data, 'CartSubmit');
            if ($result !== true) {
                $this->error($result);
            }

            $userId = cmf_get_current_user_id();

            $userAddressId = $this->request->param('user_address_id', 0, 'intval');
            $userAddress   = Db::name('order_user_address')->where(['id' => $userAddressId, 'user_id' => $userId])->find();
            if (empty($userAddress)) {
                $this->error('收货地址不存在！');
            }

//            $shipmentId = $this->request->param('shipment_id', 0, 'intval');
            // $shipment = Db::name('order_shipment')->find();
            $shipment = [
                'code' => '',
                'name' => ''
            ];
//            if (empty($shipment)) {
//                $this->error('物流方式不存在！');
//            }


            $items   = $this->request->param('items/a', '');
            if(empty($items)){
                $this->error('你没有选择商品！');
            }
            $buyItems = \array_map(function($val){
                $return['goods_id'] = intval($val['id']);
                $return['goods_sku_id'] = intval($val['sku']);
                $return['selected'] = 1;
                $return['goods_quantity'] = intval($val['num']);
                return $return;
            },$items);

            $getCart = \app\order\service\ApiService::getSubmitItems($buyItems);

            if (isset($getCart['error'])) {
                $this->error($getCart['error'][1]);
            }

            $shopsGoods = $getCart['shopscart'];

            // $expireTime  = $goods[0]['expire_time'];
            // 订单超时，一般是下单后24小时，或者自定义
            $expireTime = time() + 60*60*24;
            $invoiceId   = $this->request->param('invoice_id', 0, 'intval');
            $userNotes   = $this->request->param('user_note/a', '');
            $userInvoice = Db::name('order_user_invoice')->where(['user_id' => $userId, 'id' => $invoiceId])->find();

            $orderMore         = "";
            $invoiceTitle      = '';
            $invoiceTaxpayerId = 0;

            if (!empty($userInvoice)) {
                $invoiceTitle                  = $userInvoice['title'];
                $invoiceTaxpayerId             = $userInvoice['taxpayer_id'];
                $userInvoice['consignee_info'] = json_decode($userInvoice['consignee_info'], true);
                $orderMore                     = json_encode(['invoice' => $userInvoice]);
            }

            $shippingStatus = 0;

            try {
                $paymentType = Db::name('crm_customer')->where('user_id', $userId)->value('payment_type');

                if ($paymentType == 2) {//后款可以直接发货
                    $shippingStatus = 10;
                }

            } catch (\Exception $e) {

            }

            Db::startTrans();

            $currentTime = time();

            $orderIds = [];

            try {
                foreach ($shopsGoods as $shopId => $shopGoods) {
                    $goodsAmount = $shopGoods['amount'];
                    $orderSn     = cmf_get_order_sn();
                    $userNote    = $userNotes[$shopId];
                    $orderId     = Db::name('Order')->insertGetId([
                        'user_id'             => $userId,
                        'shop_id'             => $shopId,
                        'province'            => $userAddress['province'],
                        'city'                => $userAddress['city'],
                        'district'            => $userAddress['district'],
                        'goods_amount'        => $goodsAmount,
                        'shipping_status'     => $shippingStatus,
                        'express_fee'         => 0,
                        'order_amount'        => $goodsAmount,
                        'total_amount'        => $goodsAmount,
                        'create_time'         => $currentTime,
                        'expire_time'         => $expireTime,
                        'discount'            => 0,
                        'order_sn'            => $orderSn,
                        'consignee'           => $userAddress['consignee'],
                        'address'             => $userAddress['address'],
                        'zip_code'            => $userAddress['zip_code'],
                        'email'               => $userAddress['email'],
                        'mobile'              => $userAddress['mobile'],
                        'mobile2'             => $userAddress['mobile2'],
                        'shipment_code'       => $shipment['code'],
                        'shipment_name'       => $shipment['name'],
                        'invoice_title'       => $invoiceTitle,
                        'invoice_taxpayer_id' => $invoiceTaxpayerId,
                        'more'                => $orderMore,
                        'user_note'           => $userNote
                    ]);

                    $orderItems = [];

                    foreach ($shopGoods['goods'] as $item) {
                        $nowSku = $getCart['goods'][$item['goods_id']]['skus'][$item['goods_sku_id']];
                        array_push($orderItems, [
                            'order_id'        => $orderId,
                            'goods_id'        => $item['goods_id'],
                            'goods_sku_id'    => $item['goods_sku_id'],
                            // 'expire_time'     => $item['expire_time'],
                            'expire_time'     => $expireTime,
                            'goods_quantity'  => $item['goods_quantity'],
                            'original_price'  => $nowSku['original_price'],
                            'goods_price'     => $nowSku['price'],
                            'table_name'      => isset($item['table_name'])?$item['table_name']:'mall_item',
                            'goods_sku_table' => isset($item['goods_sku_table'])?$item['goods_sku_table']:'mall_item_sku',
                            'goods_name'      => $nowSku['title'],
                            'goods_thumbnail' => $getCart['goods'][$item['goods_id']]['thumbnail'],
                            'goods_spec'      => $nowSku['spec_info'],
                            'more'            => $nowSku['more']
                        ]);
                    }

                    Db::name('OrderItem')->insertAll($orderItems);

                    array_push($orderIds, $orderId);
                }

                Db::name('OrderCart')->where(['user_id' => $userId, 'selected' => 1])->delete();

                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error('订单提交失败！');
            }


            $this->success('订单提交成功，正在跳转...', url('order/Cart/pay', ['order_id' => join(',', $orderIds)]));

        }
    }

    public function virtualSubmit()
    {

        $data = $this->request->param();

        $userId = cmf_get_current_user_id();

        $shipment = [
            'code' => '',
            'name' => ''
        ];
//            if (empty($shipment)) {
//                $this->error('物流方式不存在！');
//            }

        $goods = Db::name('OrderCart')->where(['user_id' => $userId, 'selected' => 1])
            ->where('expire_time', 'gt', time())->order('expire_time ASC')->select();
        if ($goods->isEmpty()) {
            $this->error('你没有选择商品！');
        }

        $shopIds = ['1' => 1];

        $shopsGoods = [];

        foreach ($goods as $item) {
            if (!empty($item['shop_id'])) {
                $shopId                    = $item['shop_id'];
                $shopIds[$item['shop_id']] = $shopId;
            } else {
                $shopId = $item['shop_id'];
            }

            if (empty($shopsGoods[$shopId])) {
                $shopsGoods[$shopId] = [
                    'amount' => 0,
                    'items'  => []
                ];
            }

            $shopsGoods[$shopId]['amount'] += $item['goods_price'] * $item['goods_quantity'];
            array_push($shopsGoods[$item['shop_id']]['items'], $item);
        }


        $expireTime  = $goods[0]['expire_time'];
        $invoiceId   = $this->request->param('invoice_id', 0, 'intval');
        $userNotes   = $this->request->param('user_note/a', '');
        $userInvoice = Db::name('order_user_invoice')->where(['user_id' => $userId, 'id' => $invoiceId])->find();

        $orderMore         = "";
        $invoiceTitle      = '';
        $invoiceTaxpayerId = 0;

        if (!empty($userInvoice)) {
            $invoiceTitle                  = $userInvoice['title'];
            $invoiceTaxpayerId             = $userInvoice['taxpayer_id'];
            $userInvoice['consignee_info'] = json_decode($userInvoice['consignee_info'], true);
            $orderMore                     = json_encode(['invoice' => $userInvoice]);
        }

        $shippingStatus = 0;

        Db::startTrans();

        $currentTime = time();

        $orderIds = [];

        try {
            foreach ($shopsGoods as $shopId => $shopGoods) {
                $goodsAmount = $shopGoods['amount'];
                $orderSn     = cmf_get_order_sn();
                $userNote    = $userNotes[$shopId];
                $orderId     = Db::name('Order')->insertGetId([
                    'user_id'             => $userId,
                    'shop_id'             => $shopId,
                    'province'            => '',
                    'city'                => '',
                    'district'            => '',
                    'goods_amount'        => $goodsAmount,
                    'shipping_status'     => 1,
                    'shipping_price'      => 0,
                    'order_amount'        => $goodsAmount,
                    'total_amount'        => $goodsAmount,
                    'create_time'         => $currentTime,
                    'expire_time'         => $expireTime,
                    'discount'            => 0,
                    'order_sn'            => $orderSn,
                    'consignee'           => '',
                    'address'             => '',
                    'zip_code'            => '',
                    'email'               => '',
                    'mobile'              => '',
                    'mobile2'             => '',
                    'shipment_code'       => '',
                    'shipment_name'       => '',
                    'invoice_title'       => $invoiceTitle,
                    'invoice_taxpayer_id' => $invoiceTaxpayerId,
                    'more'                => $orderMore,
                    'user_note'           => $userNote
                ]);

                $orderItems = [];

                foreach ($shopGoods['items'] as $item) {
                    array_push($orderItems, [
                        'order_id'        => $orderId,
                        'goods_id'        => $item['goods_id'],
                        'goods_sku_id'    => $item['goods_sku_id'],
                        'expire_time'     => $item['expire_time'],
                        'goods_quantity'  => $item['goods_quantity'],
                        'original_price'  => $item['goods_price'],
                        'goods_price'     => $item['goods_price'],
                        'table_name'      => $item['table_name'],
                        'goods_sku_table' => $item['goods_sku_table'],
                        'goods_name'      => $item['goods_name'],
                        'goods_thumbnail' => $item['goods_thumbnail'],
                        'goods_spec'      => $item['goods_spec'],
                        'more'            => $item['more']
                    ]);
                }

                Db::name('OrderItem')->insertAll($orderItems);

                array_push($orderIds, $orderId);
            }

            Db::name('OrderCart')->where(['user_id' => $userId, 'selected' => 1])->delete();

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error('订单提交失败！');
        }


        $this->success('订单提交成功，正在跳转...', url('order/Cart/pay', ['order_id' => join(',', $orderIds)]));

    }

    public function pay()
    {
        $orderIds = $this->request->param('order_id');

        if (empty($orderIds)) {
            $this->error('参数错误！');
        }

        $orderIds = explode(',', $orderIds);

        $orderId = $orderIds[0];
        $userId  = cmf_get_current_user_id();
        $order   = Db::name('Order')->where(['id' => $orderId, 'user_id' => $userId])->find();

        if (empty($order)) {
            $this->error('订单不存在！');
        }

        $payments = Db::name('OrderPayment')->where(['status' => 1])->select();

        $this->assign("order", $order);
        $this->assign("order_ids", join(',', $orderIds));
        $this->assign('payments', $payments);

        return $this->fetch();

    }

    public function cancel()
    {
        $id        = $this->request->param('id', 0, 'intval');
        $userId    = cmf_get_current_user_id();
        $findGoods = Db::name('OrderCart')->field('goods_id')->where(['user_id' => $userId, 'id' => $id])->find();
        if (empty($findGoods)) {
            $this->error('购物车不存在此商品！');
            if ($findGoods['deletable'] == 0) {
                $this->error('此商品不能删除！');
            }
        }

        Db::name('OrderCart')->where(['id' => $id])->delete();

        $this->success('删除成功！');

    }

    public function selectStatus()
    {
        if ($this->request->isPost()) {
            $id       = $this->request->param('id', 0, 'intval');
            $selected = $this->request->param('selected', 0, 'intval');
            $selected = empty($selected) ? 0 : 1;
            $userId   = cmf_get_current_user_id();

            Db::name('OrderCart')->where(['user_id' => $userId, 'id' => $id])->update(['selected' => $selected]);

            $totalAmount = Db::name('OrderCart')->where(['user_id' => $userId, 'selected' => 1])
                ->where('expire_time', 'gt', time())->sum('goods_price*goods_quantity');
            $totalAmount = empty($totalAmount) ? '0.00' : $totalAmount;
            $totalAmount = number_format($totalAmount, 2);
            $this->success('success', '', ['total_amount' => $totalAmount]);
        }
    }

    public function changeQuantity()
    {
        if ($this->request->isPost()) {
            $id       = $this->request->param('id', 0, 'intval');
            $quantity = $this->request->param('quantity', 0, 'intval');
            $userId   = cmf_get_current_user_id();

            Db::name('OrderCart')->where(['user_id' => $userId, 'id' => $id])->update(['goods_quantity' => $quantity]);

            $goodsAmount = Db::name('OrderCart')->where(['user_id' => $userId, 'id' => $id])->sum('goods_price*goods_quantity');
            $goodsAmount = empty($goodsAmount) ? '0.00' : $goodsAmount;
            $goodsAmount = number_format($goodsAmount, 2);

            $totalAmount = Db::name('OrderCart')->where(['user_id' => $userId, 'selected' => 1])
                ->where('expire_time', 'gt', time())->sum('goods_price*goods_quantity');
            $totalAmount = empty($totalAmount) ? '0.00' : $totalAmount;
            $totalAmount = number_format($totalAmount, 2);
            $this->success('success', '', ['total_amount' => $totalAmount, 'goods_amount' => $goodsAmount, 'id' => $id]);
        }
    }

    public function getCount()
    {
        $userId     = cmf_get_current_user_id();
        $goodsCount = Db::name('OrderCart')->where(['user_id' => $userId])
            ->where('expire_time', 'gt', time())->sum('goods_quantity');

        if (empty($goodsCount)) {
            $goodsCount = 0;
        }

        $this->success('success', '', ['count' => $goodsCount]);
    }
}