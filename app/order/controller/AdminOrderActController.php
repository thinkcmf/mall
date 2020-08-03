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
namespace app\order\controller;

use app\order\service\OrderService;

class AdminOrderActController extends AdminOrderController
{
    /**
     * 订单支付
     * @adminMenu(
     *     'name'   => '订单后台支付',
     *     'parent' => 'order/AdminOrder/details',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '订单后台支付',
     *     'param'  => ''
     * )
     */
    public function paid()
    {
        $id  = $this->request->param('order_id', 0, 'intval');

        $info = $this->request->param('pay_info');
        $info['pay_time'] = time();

        $res = OrderService::doPaid($id, $info);

        if ($res['error']) {
            $this->error($res['error']);
        }
        $this->success("支付状态更新成功！");
    }

    /**
     * 订单发货
     * @adminMenu(
     *     'name'   => '订单发货',
     *     'parent' => 'order/AdminOrder/details',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '订单发货',
     *     'param'  => ''
     * )
     */
    public function ship()
    {
        $id  = $this->request->param('order_id', 0, 'intval');

        $info = $this->request->param('ship_info');
        $info['ship_time'] = time();

        $res = OrderService::doShip($id, $info);

        if ($res['error']) {
            $this->error($res['error']);
        }
        $this->success("发货状态更新成功！");
    }

    /**
     * 确认送达
     * @adminMenu(
     *     'name'   => '确认送达',
     *     'parent' => 'order/AdminOrder/details',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '确认送达',
     *     'param'  => ''
     * )
     */
    public function delivery()
    {
        $id  = $this->request->param('order_id', 0, 'intval');

        $res = OrderService::doDelivery($id);

        if ($res['error']) {
            $this->error($res['error']);
        }
        $this->success("确认送达成功！");
    }

    /**
     * 确认收货
     * @adminMenu(
     *     'name'   => '确认收货',
     *     'parent' => 'order/AdminOrder/details',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '确认收货',
     *     'param'  => ''
     * )
     */
    public function received()
    {
        $id  = $this->request->param('order_id', 0, 'intval');

        $res = OrderService::doReceived($id);

        if ($res['error']) {
            $this->error($res['error']);
        }
        $this->success("确认收货成功！");
    }

    /**
     * 订单完结
     * @adminMenu(
     *     'name'   => '订单完结',
     *     'parent' => 'order/AdminOrder/details',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '订单完结',
     *     'param'  => ''
     * )
     */
    public function finished()
    {
        $id  = $this->request->param('order_id', 0, 'intval');

        $res = OrderService::doFinished($id);

        if ($res['error']) {
            $this->error($res['error']);
        }
        $this->success("订单完结成功！");
    }

    /**
     * 订单取消
     * @adminMenu(
     *     'name'   => '订单取消',
     *     'parent' => 'order/AdminOrder/details',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '订单取消',
     *     'param'  => ''
     * )
     */
    public function cancel()
    {
        $id  = $this->request->param('order_id', 0, 'intval');
        $force  = $this->request->param('force', 0, 'intval');
        $flag  = $this->request->param('flag', '', 'trim');
        $notice  = $this->request->param('notice', '', 'trim');

        $res = OrderService::doCancel($id, $force, $flag, $notice);

        if ($res['error']) {
            $this->error($res['error']);
        }
        $this->success("取消成功！");
    }

    /**
     * 订单标记
     * @adminMenu(
     *     'name'   => '订单标记',
     *     'parent' => 'order/AdminOrder/details',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '订单标记',
     *     'param'  => ''
     * )
     */
    public function notice()
    {
        $id  = $this->request->param('order_id', 0, 'intval');
        $flag  = $this->request->param('flag', '', 'trim');
        $notice  = $this->request->param('notice', '', 'trim');
        $res = OrderService::doNotice($id, $flag, $notice);

        if ($res['error']) {
            $this->error($res['error']);
        }
        $this->success("标记成功！");
    }
}
