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

    public function index()
    {
        $userId      = cmf_get_current_user_id();
        $currentTime = time();
        $goods       = Db::name('OrderCart')
            ->where(['user_id' => $userId])
            ->where('expire_time', 'gt', $currentTime)->select();
        $totalAmount = Db::name('OrderCart')->where(['user_id' => $userId, 'selected' => 1])
            ->where('expire_time', 'gt', $currentTime)->sum('goods_price*goods_quantity');

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
                $shopsGoods[$shopId] = [];
            }

            array_push($shopsGoods[$item['shop_id']], $item);
        }

        $shops = [];

        try {
            $shops = Db::name('shop')->where('id', 'in', $shopIds)->column('*', 'id');
        } catch (\Exception $e) {

        }


        $this->assign('goods', $goods);
        $this->assign('shops_goods', $shopsGoods);
        $this->assign('shops', $shops);
        $this->assign('total_amount', $totalAmount);
        return $this->fetch();
    }

    public function confirm()
    {
        $userId      = cmf_get_current_user_id();
        $currentTime = time();
        $goods       = Db::name('OrderCart')->where(['user_id' => $userId, 'selected' => 1])
            ->where('expire_time', 'gt', time())->select();

        if (empty($goods)) {
            $this->error('您还没有选中任何商品！');
        }

        $goodsCount        = 0;
        $virtualGoodsCount = 0;

        foreach ($goods as $item) {
            if ($item['is_virtual']) {
                $virtualGoodsCount++;
            } else {
                $goodsCount++;
            }
        }

        if ($goodsCount > 0 && $virtualGoodsCount > 0) {
            $this->error('虚拟商品和实体商品不能同时提交！');
        }

        if ($goodsCount > 0) {
            $this->assign('is_virtual', false);
        }


        if ($virtualGoodsCount > 0) {
            $this->assign('is_virtual', true);
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
                $shopsGoods[$shopId] = [];
            }

            array_push($shopsGoods[$item['shop_id']], $item);
        }

        $shops = [];

        try {
            $shops = Db::name('shop')->where('id', 'in', $shopIds)->column('*', 'id');
        } catch (\Exception $e) {

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
            $totalAmount = Db::name('OrderCart')->where(['user_id' => $userId, 'selected' => 1])
                ->where('expire_time', 'gt', $currentTime)
                ->sum('goods_price*goods_quantity');

            $this->assign('user_addresses', $userAddresses);
            $this->assign('areas', $areas);
        }

        $invoiceInfo = Db::name('order_user_invoice')->where('user_id', $userId)->find();

        if (!empty($invoiceInfo)) {
            $invoiceInfo['consignee_info'] = json_decode($invoiceInfo['consignee_info'], true);
            $this->assign('invoice_info', $invoiceInfo);
        }

        $this->assign('total_amount', $totalAmount);
        $this->assign('goods', $goods);
        $this->assign('shops_goods', $shopsGoods);
        $this->assign('shops', $shops);
        return $this->fetch();
    }

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