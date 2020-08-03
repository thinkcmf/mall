<?php


namespace api\mall\controller;


use api\mall\service\OrderService;
use api\common\base\AuthBaseController;

/**
 * 统一支付控制器
 *
 * Class PaymentController
 * @package api\mall\controller
 */
class PaymentController extends AuthBaseController
{

    /**
     * 统一支付
     */
    public function pay()
    {
        $code   = $this->request->param('code', 'cmf-wechat-miniapp');
        $id = $this->request->param('id','');
        if(empty($id)){
            $this->error('缺失订单编号');
        }
        $order = OrderService::getOrder($this->getUserId(),$id);
        if(!$order){
            $this->error('订单不存在');
        }
        $params = [
            'code'    => $code,
            'amount'  => $order['amount_payable'],
            'sn'      => $order['sn'],
            'id'      => $order['id'],
            'user_id' => $this->getUserId()
        ];

        $info = hook_one('order_payment_unifiedorder', $params);
        if($info === false){
            $this->error('未安装支付插件');
        }
        // TODO 根据$code支付渠道 判断插件结果 不同的处理
        $this->success('ok',$info);
    }
}