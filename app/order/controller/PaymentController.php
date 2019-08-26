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
        $orderId    = $this->request->param('order_id');

        if (empty($orderId)) {
            $this->error('订单不能为空');
        }

        $userId = cmf_get_current_user_id();

        if (empty($paymentId)) {
            $payment = Db::name("OrderPayment")->where('code', $paymentCode)->find();
        } else {
            $payment = Db::name("OrderPayment")->where('id', $paymentId)->find();
        }

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
            $data['pay_status'] = 10;//10:等待支付
        }

        //修改订单支付方式
        Db::name('Order')->where(['user_id' => $userId, 'id' => $orderId, 'pay_status' => 0])->update($data);

        $order   = Db::name('Order')->where(['user_id' => $userId, 'id' => $orderId])->find();
        $params  = [
            'code'   => $payment['code'],
            'amount' => $order['order_amount'],
            'sn'     => $order['order_sn']
        ];
        $payment = hook_one('order_payment_unifiedorder', $params);
        return $payment;
    }

    public function unifiedorder()
    {
        $params = $this->request->param();
        hook_one('order_payment_unifiedorder', $params);
    }
}