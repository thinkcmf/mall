<?php

namespace app\order\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class AdminOrderDraftController extends AdminBaseController
{

    public function deleteItem()
    {
        $id  = $this->request->param('id', 0, 'intval');
        $key = $this->request->param('key');

        $orderDraft = Db::name('order_draft')->where(['admin_id' => cmf_get_current_admin_id(), 'id' => $id])->find();

        if (empty($orderDraft)) {
            $this->error('记录不存在！');
        }

        $items = json_decode($orderDraft['items'], true);

        if (isset($items[$key])) {
            unset($items[$key]);
        }

        $totalAmount = 0;

        foreach ($items as $item) {
            $totalAmount += $item['goods_price'] * $item['goods_quantity'];
        }

        Db::name('order_draft')->where(['id' => $id])->update([
            'items'        => json_encode($items),
            'total_amount' => round($totalAmount, 2)
        ]);

        $this->success('删除成功！');
    }

    public function changeItemQuantity()
    {
        $id       = $this->request->param('id', 0, 'intval');
        $key      = $this->request->param('key');
        $quantity = $this->request->param('quantity', 0, 'intval');

        $orderDraft = Db::name('order_draft')->where(['admin_id' => cmf_get_current_admin_id(), 'id' => $id])->find();

        if (empty($orderDraft)) {
            $this->error('记录不存在！');
        }

        $items = json_decode($orderDraft['items'], true);
        if (!isset($items[$key])) {
            $this->error('记录不存在！');
        }

        $items[$key]['goods_quantity'] = $quantity;
        $goodsAmount                   = number_format(round($items[$key]['goods_price'] * $quantity, 2), 2);

        $totalAmount = 0;

        foreach ($items as $item) {
            $totalAmount += $item['goods_price'] * $item['goods_quantity'];
        }

        Db::name('order_draft')->where(['id' => $id])->update([
            'items'        => json_encode($items),
            'total_amount' => round($totalAmount, 2)
        ]);

        $this->success('保存成功！', '', ['goods_amount' => $goodsAmount]);
    }


}