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

use app\order\model\OrderModel;
use cmf\controller\AdminBaseController;
use Dompdf\FontMetrics;
use think\Db;
use Dompdf\Dompdf;
use think\db\Query;
use think\Hook;

/**
 * Class AdminOrderController
 * @package app\order\controller
 * @adminMenuRoot(
 *     'name'   =>'订单系统',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'list-ul',
 *     'remark' =>'订单系统'
 * )
 * @adminMenuRoot(
 *     'name'   =>'财务管理',
 *     'action' =>'defaultFinance',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'money',
 *     'remark' =>'财务管理'
 * )
 * @adminMenuRoot(
 *     'name'   =>'仓储物流',
 *     'action' =>'defaultStock',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'truck',
 *     'remark' =>'仓储物流'
 * )
 */
class AdminOrderController extends AdminBaseController
{

    /**
     * 订单管理
     * @adminMenu(
     *     'name'   => '订单管理',
     *     'parent' => 'order/AdminOrder/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '订单管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        return $this->_list('index');
    }

    /**
     * 未支付订单
     * @adminMenu(
     *     'name'   => '未支付订单',
     *     'parent' => 'order/AdminOrder/defaultFinance',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '未支付订单',
     *     'param'  => ''
     * )
     */
    public function notPaid()
    {
        $where = function (Query $query) {
            $query->where('a.pay_status', 0)
->where('a.order_status', 'neq',2)
                ->where('expire_time', ['gt', time()], ['eq', 0], 'OR');
        };
        return $this->_list('notPaid', $where, 'not_paid');
    }

    /**
     * 待发货订单
     * @adminMenu(
     *     'name'   => '待发货订单',
     *     'parent' => 'order/AdminOrder/defaultStock',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '待发货订单',
     *     'param'  => ''
     * )
     */
    public function notShipped()
    {
        return $this->_list('notShipped', [
            'a.shipping_status' => 10,
        ], 'not_shipped');
    }

    /**
     * 已发货订单
     * @adminMenu(
     *     'name'   => '已发货订单',
     *     'parent' => 'order/AdminOrder/defaultStock',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '已发货订单',
     *     'param'  => ''
     * )
     */
    public function hasShipped()
    {
        return $this->_list('hasShipped', [
            'a.shipping_status' => 1,
        ], 'has_shipped');
    }

    /**
     * 已支付订单
     * @adminMenu(
     *     'name'   => '已支付订单',
     *     'parent' => 'order/AdminOrder/defaultFinance',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '已支付订单',
     *     'param'  => ''
     * )
     */
    public function hasPaid()
    {
        return $this->_list('hasPaid', [
            'a.pay_status' => 1,
        ], 'has_paid');
    }

    private function _list($tab, $where = [], $tpl = 'index')
    {

        $data = $this->request->param();

        $orderModel = new OrderModel();
        $orders     = $orderModel
            ->alias('a')
            ->field('a.*,b.user_login,b.user_nickname,b.avatar,b.user_email,b.mobile AS user_mobile')
            ->join('__USER__ b', 'a.user_id=b.id')
            ->where($where)
            ->where(function (Query $query) use ($data) {
                if (!empty($data['id'])) {
                    $query->where('a.id', intval($data['id']));
                }

                if (!empty($data['order_sn'])) {
                    $query->where('a.order_sn', 'like', $data['order_sn'] . '%');
                }

                if (!empty($data['email'])) {
                    $query->where('b.user_email', 'like', $data['email'] . '%');
                }

                if (!empty($data['mobile'])) {
                    $query->where('b.mobile', 'like', $data['mobile'] . '%');
                }
            })
            ->order('create_time DESC')
            ->paginate();

        $userIds = [];

        foreach ($orders as $order) {
            array_push($userIds, $order['user_id']);
        }

        try {
            if (!empty($userIds)) {
                $userIds   = array_unique($userIds);
                $customers = Db::name('crm_customer')->where('user_id', 'in', $userIds)->column('*', 'user_id');
                $this->assign('customers', $customers);
            }
        } catch (\Exception $e) {

        }

        $orders->appends($data);

        $this->assign("page", $orders->render());
        $this->assign('orders', $orders);
        $this->assign('tab', $tab);
        return $this->fetch($tpl);
    }

    public function deliver()
    {
        $id         = $this->request->param('id', 0, 'intval');
        $order      = Db::name('order')->where('id', $id)->find();
        $orderItems = Db::name('OrderItem')->where('order_id', $id)->select();

        $areaIds = [];
        array_push($areaIds, $order['province']);
        if (!empty($order['city'])) {
            array_push($areaIds, $order['city']);
        }

        if (!empty($order['district'])) {
            array_push($areaIds, $order['district']);
        }

        $areas     = Db::name('area')->where('id', 'in', $areaIds)->column('id,name', 'id');
        $shipments = Db::name('order_shipment')->where('status', 1)->select();

        $this->assign('areas', $areas);
        $this->assign('order_items', $orderItems);
        $this->assign('shipments', $shipments);
        $this->assign('order', $order);
        return $this->fetch('deliver');
    }

    public function batchDeliver()
    {
        $ids = $this->request->param('ids');

        $shipments = Db::name('order_shipment')->where('status', 1)->select();

        $orders = Db::name('order')->where('id', 'in', $ids)->column('*', 'id');

        $areaIds = [];

        foreach ($orders as $key => $order) {
            $orders[$key]['items'] = Db::name('order_item')->where('order_id', $order['id'])->select();
            array_push($areaIds, $order['province']);
            if (!empty($order['city'])) {
                array_push($areaIds, $order['city']);
            }

            if (!empty($order['district'])) {
                array_push($areaIds, $order['district']);
            }
        }

        $areas = Db::name('area')->where('id', 'in', $areaIds)->column('id,name', 'id');

        $this->assign('orders', $orders);
        $this->assign('shipments', $shipments);
        $this->assign('ids', $ids);
        $this->assign('areas', $areas);

        return $this->fetch('batch_deliver');
    }

    public function batchDeliveryOrder()
    {
        $ids = $this->request->param('ids');

        $shipments = Db::name('order_shipment')->where('status', 1)->select();

        $orders = Db::name('order')->where('id', 'in', $ids)->column('*', 'id');

        $areaIds = [];

        foreach ($orders as $key => $order) {
            $orders[$key]['items'] = Db::name('order_item')->where('order_id', $order['id'])->select();
            array_push($areaIds, $order['province']);
            if (!empty($order['city'])) {
                array_push($areaIds, $order['city']);
            }

            if (!empty($order['district'])) {
                array_push($areaIds, $order['district']);
            }
        }

        $areas = Db::name('area')->where('id', 'in', $areaIds)->column('id,name', 'id');

        $this->assign('orders', $orders);
        $this->assign('shipments', $shipments);
        $this->assign('ids', $ids);
        $this->assign('areas', $areas);

        return $this->fetch('batch_delivery_order');
    }

    public function expressWaybill()
    {
        $id         = $this->request->param('id', 0, 'intval');
        $order      = Db::name('order')->where('id', $id)->find();
        $orderItems = Db::name('OrderItem')->where('order_id', $id)->select();

        $areaIds = [];
        array_push($areaIds, $order['province']);
        if (!empty($order['city'])) {
            array_push($areaIds, $order['city']);
        }

        if (!empty($order['district'])) {
            array_push($areaIds, $order['district']);
        }

        $areas = Db::name('area')->where('id', 'in', $areaIds)->column('id,name', 'id');

        $this->assign('areas', $areas);
        $this->assign('order_items', $orderItems);

        $this->assign('order', $order);
        return $this->fetch('express_waybill');
    }

    public function batchExpressWaybill()
    {
        $ids = $this->request->param('ids');

        $ids = explode(',', $ids);

        $shipments = Db::name('order_shipment')->where('status', 1)->select();

        $orders = Db::name('order')->where('id', 'in', $ids)->column('*', 'id');

        $areaIds = [];

        foreach ($orders as $key => $order) {
            $orders[$key]['items'] = Db::name('order_item')->where('order_id', $order['id'])->select();
            array_push($areaIds, $order['province']);
            if (!empty($order['city'])) {
                array_push($areaIds, $order['city']);
            }

            if (!empty($order['district'])) {
                array_push($areaIds, $order['district']);
            }
        }

        $areas = Db::name('area')->where('id', 'in', $areaIds)->column('id,name', 'id');

        $this->assign('orders', $orders);
        $this->assign('shipments', $shipments);
        $this->assign('areas', $areas);

        return $this->fetch('batch_express_waybill');
    }

    public function detail()
    {
        $id         = $this->request->param('id', 0, 'intval');
        $order      = Db::name('order')->where('id', $id)->find();
        $orderItems = Db::name('OrderItem')->where('order_id', $id)->select();

        $order['more'] = json_decode($order['more'], true);
        $areaIds       = [];
        array_push($areaIds, $order['province']);
        if (!empty($order['city'])) {
            array_push($areaIds, $order['city']);
        }

        if (!empty($order['district'])) {
            array_push($areaIds, $order['district']);
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

    //更新订单状态为已支付
    public function paid()
    {
        $id        = $this->request->param('id', 0, 'intval');
        $findOrder = Db::name('order')->where('id', $id)->find();
        if (empty($findOrder)) {
            $this->error('订单不存在！');
        }
        //$orderItems = Db::name('OrderItem')->field('goods_id')->where(['order_id' => $id])->select();
        $payTime = time();

        $data = [
            'pay_status' => 1,
            'pay_time'   => $payTime,
        ];

        if ($findOrder['shipping_status'] == 0) {
            $data['shipping_status'] = 10;
        }

        //更新订单
        Db::name('order')->where('id', $id)->update($data);

        $this->success("订单状态更新成功！");
    }

    public function readyToShip()
    {
        $id        = $this->request->param('id', 0, 'intval');
        $findOrder = Db::name('order')->where('id', $id)->find();
        if (empty($findOrder)) {
            $this->error('订单不存在！');
        }
        //$orderItems = Db::name('OrderItem')->field('goods_id')->where(['order_id' => $id])->select();
        $payTime = time();

        if ($findOrder['shipping_status'] == 0) {
            $data['shipping_status'] = 10;
        }

        //更新订单
        Db::name('order')->where(['id' => $id])->update($data);

        $this->success("订单状态更新成功！");
    }

    public function needManualInvoice()
    {
        $id        = $this->request->param('id', 0, 'intval');
        $findOrder = Db::name('order')->where('id', $id)->find();
        if (empty($findOrder)) {
            $this->error('订单不存在！');
        }

        //更新订单
        Db::name('order')->where('id', $id)->update(['print_invoice_type' => 1]);

        $this->success("设置成功！");
    }

    public function shipped()
    {
        $id         = $this->request->param('id', 0, 'intval');
        $find_order = Db::name('order')->field('pay_status')->where('id', $id)->find();
        if (!empty($find_order)) {
            if (empty($find_order['pay_status'])) {
                $this->error('此订单还没有支付，无法设置发货完成状态！');
            }

            Db::name('order')->where(['id' => $id, 'pay_status' => 1])->update(['shipping_status' => 1]);
            $this->success('状态更新成功!');
        } else {
            $this->error('此订单不存在！');
        }
    }

    public function complete()
    {
        $id                 = $this->request->param('id', 0, 'intval');
        $findOrderPaidCount = Db::name('order')->where(['id' => $id, 'pay_status' => 1])->count();
        if ($findOrderPaidCount > 0) {
            $findOrderShippedCount = Db::name('order')->where(['id' => $id, 'shipping_status' => 2])->count();
            if ($findOrderShippedCount > 0) {
                Db::name('order')->where(['order_id' => $id, 'pay_status' => 1, 'shipping_status' => 2])->save(['order_status' => 0]);
                $this->success('状态更新成功!');
            } else {
                $this->error('此订单还没签收，无法设置已完成状态！');
            }

        } else {
            $this->error('此订单还没有支付，无法设置已完成状态！');
        }
    }

    public function invoicePrinted()
    {
        $id                 = $this->request->param('id', 0, 'intval');
        $findOrderPaidCount = Db::name('order')->where(['id' => $id, 'pay_status' => 1])->count();
        if ($findOrderPaidCount > 0) {
            $result = Db::name('order')->where(['id' => $id, 'pay_status' => 1])->update(['invoice_status' => 1]);
            if ($result !== false) {
                $this->success('状态更新成功!');
            } else {
                $this->error('状态更新失败!');
            }
        } else {
            $this->error('此订单还没有支付，无法设置此状态！');
        }
    }

    public function setTrackingNumber()
    {
        $trackingNumber = $this->request->param('tracking_number');
        $shipmentCode   = $this->request->param('shipment_code');
        $id             = $this->request->param('id', 0, 'intval');

        if (empty($trackingNumber)) {
            $this->error('运单号不能为空！');
        }

        $shipmentName = Db::name('order_shipment')->where('code', $shipmentCode)->value('name');

        if (empty($shipmentName)) {
            $this->error('物流不存在！');
        }

        $order = Db::name('order')->where('id', $id)->find();

        if (empty($order)) {
            $this->error('订单不存在！');
        }

        if ($order['is_supplier_deliver'] || $order['shipping_status'] == 1) {
            Db::name('order')->where('id', $id)->update([
                'tracking_number' => $trackingNumber,
                'shipping_status' => 1, //已发货
                'shipment_code'   => $shipmentCode,
                'shipment_name'   => $shipmentName,
                'deliver_time'    => time()
            ]);
        } else {
            $items = Db::name('order_item')->where('order_id', $id)->select();

            if (!$items->isEmpty()) {
                $allPass = true;
                Db::startTrans();

                foreach ($items as $item) {
                    $params = ['order_item' => $item, 'order_sn' => $order['order_sn'], 'user_id' => $order['user_id']];

                    $tableNameArr = explode('_', $item['table_name']);

                    $app = $tableNameArr[0];

                    $class = 'app\\' . $app . '\\behavior\\OrderDeliverCallback' . cmf_parse_name($item['table_name'], 1) . "Behavior";

                    if (class_exists($class)) {
                        try {
                            Hook::exec($class, 'run', $params);
                        } catch (\Exception $e) {
                            $allPass = false;
                            Db::rollback();
                            file_put_contents('OrderDeliverCallback.log', $e->getMessage() . "\n\n\n", 8);
                        }
                    } else {
                        $this->error('no');
                        file_put_contents('OrderDeliverCallback.log', $class . '不存在' . "\n\n\n", 8);
                    }
                }


                if ($allPass) {
                    try {
                        Db::name('order')->where('id', $id)->update([
                            'tracking_number' => $trackingNumber,
                            'shipping_status' => 1, //已发货
                            'shipment_code'   => $shipmentCode,
                            'shipment_name'   => $shipmentName,
                            'deliver_time'    => time()
                        ]);
                        Db::commit();
                    } catch (\Exception $E) {
                        Db::rollback();
                        $this->error('发货失败！' . $E->getMessage());
                    }

                } else {
                    $this->error('发货失败！');
                }

            } else {

            }
        }


        $this->success('保存成功！');

    }

    public function adminNote()
    {
        if ($this->request->isPost()) {
            $orderId   = $this->request->param('id', 0, 'intval');
            $adminNote = $this->request->param('admin_note');

            Db::name('order')->where('id', $orderId)->update(['admin_note' => $adminNote]);

            $this->success('保存成功！');
        }
    }

    public function applyPrintInvoice()
    {
        $orderId = $this->request->param('order_id', 0, 'intval');

        $order = Db::name('order')->where('id', $orderId)->find();

        if (empty($order)) {
            $this->error('订单不存在！');
        }

        $findInvoiceOrder = Db::name('order_invoice_order')->where('order_id', $orderId)->count();

        if ($findInvoiceOrder > 0) {
            $this->error('订单已申请开发票！');
        }

        $orderMore = json_decode($order['more'], true);

        $invoiceInfo = $orderMore['invoice'];
        unset($invoiceInfo['id']);
        $invoiceInfo['consignee_info'] = json_encode($invoiceInfo['consignee_info']);
        $invoiceInfo['create_time']    = time();

        Db::startTrans();

        try {
            $orderInvoiceId = Db::name('order_invoice')->insertGetId($invoiceInfo);

            Db::name('order_invoice_order')->insert([
                'order_id'   => $orderId,
                'invoice_id' => $orderInvoiceId
            ]);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error('操作失败，请重试！');
        }

        $this->success('申请成功！', '');
    }


}