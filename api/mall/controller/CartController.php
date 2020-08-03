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

class CartController extends AuthBaseController
{
    /**
     * 列表
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $data = CartService::getCart($this->getUserId());
        $response = [];
        $response['list'] = $data;
        $this->success('请求成功!', $response);
    }

    /**
     * 购物车商品 添加、编辑数量
     */
    public function edit()
    {
        $goodsId  = $this->request->param('id', 0, 'intval');
        $goodsId  = $this->request->param('goods_id', $goodsId, 'intval');
        $skuId    = $this->request->param('sku_id', 0, 'intval');
        $number   = $this->request->param('number', 1, 'intval');
        $selected = $this->request->param('selected', 1, 'intval');

        $res = CartService::modifyCart($this->getUserId(), $goodsId, $skuId, $number, $selected);

        if ($res['error']) {
            $this->error($res['error']);
        }

        $this->success('操作成功!', []);
    }

    /**
     * 购物车商品 删除、选择、取消选择
     */
    public function set()
    {
        $action = $this->request->param('action', '', 'trim');

        $goodsId = $this->request->param('id', 0, 'intval');
        $goodsId = $this->request->param('goods_id', $goodsId, 'intval');
        $skuId   = $this->request->param('sku_id', 0, 'intval');

        $res = CartService::setCart($action, $this->getUserId(), $goodsId, $skuId);

        if ($res['error']) {
            $this->error($res['error']);
        }

        $this->success($res['action'] . '成功!', []);
    }

    /**
     * 购物车下单
     */
    public function checkout()
    {
        $goods = $this->request->param('goods', []);
        if (empty($goods)) {
            $this->error('请选择要结算的商品');
        }

        $address = $this->request->param('address', []);
        $remark = $this->request->param('remark', '', 'trim');

        $checkoutService = new CheckoutService();
        $checkoutInfo = $checkoutService->checkout($this->getUserId(), $goods, $address, $remark);

        if (!empty($checkoutInfo['error']) && !empty($checkoutInfo['error']['bill'])) {
            $this->error('下单校验失败', $checkoutInfo);
        }
        $this->success('请求成功！', $checkoutInfo);
    }

    /**
     * 直接购买下单
     */
    public function buynow()
    {
        $skuId = $this->request->param('id', 0, 'intval');
        $skuId = $this->request->param('sku_id', $skuId, 'intval');
        $number = $this->request->param('number', 1, 'intval');
        if ($skuId < 1 || $number < 1) {
            $this->error('请提交要结算的商品');
        }

        $checkoutService = new CheckoutService();
        $checkoutInfo = $checkoutService->checkout($this->getUserId(), [$skuId => $number]);

        if (!empty($checkoutInfo['error']) && !empty($checkoutInfo['error']['bill'])) {
            $this->error('下单校验失败', $checkoutInfo);
        }
        $this->success('请求成功！', $checkoutInfo);
    }
}
