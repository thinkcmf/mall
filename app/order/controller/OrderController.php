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

use app\order\model\OrderModel;
use cmf\controller\UserBaseController;
use Dompdf\FontMetrics;
use think\Db;
use Dompdf\Dompdf;

class OrderController extends UserBaseController
{

    public function index()
    {
        $userId     = cmf_get_current_user_id();
        $orderModel = new OrderModel();
        $orders     = $orderModel->where('user_id', $userId)
            ->order('create_time DESC')
            ->paginate();

        if (!empty($orders)) {
            $orderIds = [];
            foreach ($orders as $order) {
                array_push($orderIds, $order['id']);
            }

            $orderItems    = Db::name('OrderItem')->where('order_id', 'in', $orderIds)->select();
            $newOrderItems = [];

            foreach ($orderItems as $item) {
                $newOrderItems[$item['order_id']][] = $item;
            }

            $this->assign('orders', $orders);
            $this->assign('order_items', $newOrderItems);
        }

        $this->assign("page", $orders->render());
        return $this->fetch();
    }

    public function cancel()
    {
        $id     = $this->request->param('id', 0, 'intval');
        $userId = cmf_get_current_user_id();

        $findOrder = Db::name('order')->field('pay_status,expire_time,order_status')->where(['id' => $id, 'user_id' => $userId])->find();

        if (empty($findOrder)) {
            $this->error('订单不存在!');
        }

        if ($findOrder['pay_status'] == 1) {
            $this->error('订单已经支付成功，无法取消!');
        }

        if ($findOrder['order_status'] == 2) {
            $this->success('订单已取消!');
        }

        //更新订单
        Db::name('order')->where(['id' => $id])->update([
            'order_status' => 2
        ]);

        $this->success("订单取消成功！");
    }

    public function detail()
    {
        $id         = $this->request->param('id', 0, 'intval');
        $order      = Db::name('order')->where('id', $id)->find();
        $orderItems = Db::name('OrderItem')->where('order_id', $id)->select();

        $order['more'] = json_decode($order['more'], true);

        $areaIds = [];
        array_push($areaIds, $order['province']);
        if (!empty($order['city'])) {
            array_push($areaIds, $order['city']);
        }

        if (!empty($order['district'])) {
            array_push($areaIds, $order['district']);
        }

        if (!empty($order['town'])) {
            array_push($areaIds, $order['town']);
        }

        if (!empty($order['more']['invoice']['consignee_info']['province'])) {
            array_push($areaIds, $order['more']['invoice']['consignee_info']['province']);
        } else {
            $order['more']['invoice']['consignee_info']['province'] = 0;
        }

        if (!empty($order['more']['invoice']['consignee_info']['city'])) {
            array_push($areaIds, $order['more']['invoice']['consignee_info']['city']);
        } else {
            $order['more']['invoice']['consignee_info']['city'] = 0;
        }

        if (!empty($order['more']['invoice']['consignee_info']['district'])) {
            array_push($areaIds, $order['more']['invoice']['consignee_info']['district']);
        } else {
            $order['more']['invoice']['consignee_info']['district'] = 0;
        }

        if (!empty($order['more']['invoice']['consignee_info']['town'])) {
            array_push($areaIds, $order['more']['invoice']['consignee_info']['town']);
        } else {
            $order['more']['invoice']['consignee_info']['town'] = 0;
        }


        $areas = Db::name('area')->where('id', 'in', $areaIds)->column('id,name', 'id');

        $this->assign('areas', $areas);
        $this->assign('order_items', $orderItems);

        $totalAmount = Db::name('OrderItem')->where('order_id', $id)->sum('goods_price*goods_quantity');

        $this->assign('total_amount', $totalAmount);
        $this->assign('order', $order);
        return $this->fetch();
    }

    public function downloadInvoice()
    {

        $orderId = $this->request->param('order_id', 0, 'intval');

        $order = Db::name('order')->where('id', $orderId)->where('user_id', cmf_get_current_user_id())->find();

        if (empty($order)) {
            $this->error('订单不存在！');
        }

        $order['more'] = json_decode($order['more'], true);
        $orderSn       = $order['order_sn'];
        $userId        = $order['user_id'];

        $findUser = Db::name('user')->where('id', $userId)->find();

//        if (empty($findUser['user_email'])) {
//            $this->error('用户未绑定邮件,无法发送确认邮件！');
//        }

        $userConfirmCode = md5($orderSn . uniqid() . time()) . '_' . $userId . '_' . $orderSn;

        $orderItems = Db::name('order_item')->where('order_id', $orderId)->select();

        $this->assign('order', $order);
        $this->assign('order_items', $orderItems);
        $this->assign('order_confirm_url', url('order/Public/confirm', '', false, true) . '?code=' . $userConfirmCode);

        $areaIds = [];
        array_push($areaIds, $order['province']);
        if (!empty($order['city'])) {
            array_push($areaIds, $order['city']);
        }

        if (!empty($order['district'])) {
            array_push($areaIds, $order['district']);
        }

        if (!empty($order['town'])) {
            array_push($areaIds, $order['town']);
        }

        $areas = Db::name('area')->where('id', 'in', $areaIds)->column('id,name', 'id');

        $this->assign('areas', $areas);

        $totalAmount = Db::name('OrderItem')->where('order_id', $orderId)->sum('goods_price*goods_quantity');

        $this->assign('total_amount', $totalAmount);

        $subject = $this->fetch('tpl_order_invoice_2');

        $dompdf = new Dompdf(['isPhpEnabled' => true]);
        $dompdf->loadHtml($subject);


        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream('invoice-' . $orderSn . '.pdf', ['Attachment' => 1, 'compress' => 1]);
        exit;
    }

}