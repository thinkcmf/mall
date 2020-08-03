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
namespace api\mall\controller;

use api\common\base\AuthBaseController;
use api\mall\service\CartService;
use api\mall\service\CheckoutService;
use api\mall\service\OrderService;

class OrderController extends AuthBaseController
{
    /**
     * 提交订单
     */
    public function submit()
    {
        $goods = $this->request->param('goods', []);
        if (empty($goods)) {
            $this->error('请选择要结算的商品');
        }

        $address = $this->request->param('address', []);
        $remark = $this->request->param('remark', '', 'trim');

        $checkoutService = new CheckoutService();
        $result = $checkoutService->submitOrder($this->getUserId(), $goods, $address, $remark);

        if (!empty($result['error'])) {
            $this->error('下单校验失败', $result);
        }
        $this->success('订单提交成功，请及时支付，以免订单过期！', $result);
    }

    /**
     * 列表
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $status = $this->request->param('status', 0, 'intval');
        $data = OrderService::getList($this->getUserId(), $status);
        $response = [];
        $response['list'] = $data;
        $this->success('请求成功!', $response);
    }

    public function count()
    {
        $data = OrderService::getCount($this->getUserId());
        $response = $data;
        $this->success('请求成功!', $response);
    }

    public function read($id)
    {
        if (intval($id) === 0) {
            $this->error('无效的id！');
        } else {
            $res = OrderService::getOrder($this->getUserId(), $id);
            if ($res) {
                $this->success('请求成功!', $res);
            } else {
                $this->error('找不到订单数据');
            }
        }
    }

    public function cancel($id)
    {
        if (intval($id) === 0) {
            $this->error('无效的id！');
        } else {
            $res = OrderService::doCancel($this->getUserId(), $id);
            if ($res['error']) {
                $this->error('取消失败：' . $res['error']);
            }
        }
        $this->success('订单取消成功', $res['data']);
    }

    public function received($id)
    {
        if (intval($id) === 0) {
            $this->error('无效的id！');
        } else {
            $res = OrderService::doReceived($this->getUserId(), $id);
            if ($res['error']) {
                $this->error('收货失败：' . $res['error']);
            }
        }
        $this->success('确认收货成功', $res['data']);
    }
}
