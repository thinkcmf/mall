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
namespace app\order\Controller;

use cmf\controller\UserBaseController;
use think\Db;

class PaymentController extends UserBaseController
{

    public function getCode()
    {
        $paymentId   = $this->request->param('payment_id', 0, 'intval');
        $paymentCode = $this->request->param('payment_code', '');
        $orderIds    = $this->request->param('order_id');

        $orderIds = explode(',', $orderIds);

        if (empty($orderIds)) {
            $this->error('订单不能为空');
        }

        $userId = cmf_get_current_user_id();

        if (empty($paymentId)) {
            $payment = Db::name("OrderPayment")->where('code', $paymentCode)->find();
        } else {
            $payment = Db::name("OrderPayment")->where('id', $paymentId)->find();
        }

        $orders = [];

        $shopIds = [];

        foreach ($orderIds as $orderId) {
            $findOrder = Db::name('Order')->where(['user_id' => $userId, 'id' => $orderId])->find();

            if (empty($findOrder)) {
                $this->error('订单不存在！');
            } else if ($findOrder['pay_status'] == 1) {
                $this->error('订单已支付！');
            }

            $userPayments = session('user_payments');

            $userPayments = empty($userPayments) ? [] : $userPayments;

            if (!in_array($payment['code'], $userPayments) && $findOrder['payment_code'] != $payment['code']) {
                $this->error('非法支付方式！');
            }

            $data = ['payment_code' => $payment['code'], 'payment_name' => $payment['name']];

            if (empty($payment['is_prepay']) && $findOrder['pay_status'] != 10) {
                $param = ['order_id' => $orderId];
                hook('order_paid', $param);
                $data['pay_status'] = 10;//10:等待支付
            }

            $shopIds[$findOrder['shop_id']] = $findOrder['shop_id'];

            //修改订单支付方式
            Db::name('Order')->where(['user_id' => $userId, 'id' => $orderId, 'pay_status' => 0])->update($data);

            $order = Db::name('Order')->where(['user_id' => $userId, 'id' => $orderId])->find();

            array_push($orders, $order);
        }

        if (empty($payment)) {
            $this->error('支付方式不存在！');
        }

        $shopPayments = [];

        try {
            $shopPayments = Db::name('shop_payment')->where('shop_id', 'in', $shopIds)->column('*', 'shop_id');
        } catch (\Exception $e) {

        }

        $paymentType = 'order';
        $orderId     = $orderIds[0];
        if (count($orderIds) > 1) {
            $paymentType = 'merge';

            $orderId = Db::name('order_merge')->insertGetId(['order_ids' => implode(',', $orderIds)]);
        }

        if (!empty($shopPayments)) {
            foreach ($shopPayments as $shopId => $shopPayment) {
                $shopPayment['config'] = json_decode($shopPayment['config'], true);
                $shopPayments[$shopId] = $shopPayment;
            }
        }

        $this->assign('orders', $orders);
        $this->assign('shop_payments', $shopPayments);
        $this->assign('payment', $payment);

        switch ($payment['code']) {
            case 'WechatPay':
                return $this->redirect(cmf_plugin_url('WechatPay://Pay/pc', ['order_id' => $orderId, 'type' => $paymentType]));
                break;
            case 'BankTransfer':
                break;
        }

        return $this->fetch('payment');
    }

}