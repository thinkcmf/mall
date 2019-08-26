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
        $order      = Db::name('Order')->where('id', $id)->find();
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
}