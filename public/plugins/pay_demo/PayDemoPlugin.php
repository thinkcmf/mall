<?php

namespace plugins\pay_demo;

use cmf\lib\Plugin;
use plugins\pay\lib\PayClass;
use think\Db;
use think\exception\HttpResponseException;
use api\mall\service\OrderService;


class PayDemoPlugin extends Plugin
{
    public $info = [
        'name'        => 'PayDemo',
        'title'       => '支付Demo插件',
        'description' => '支付Demo插件',
        'status'      => 1,
        'author'      => '五五',
        'version'     => '1.0',
        'demo_url'    => 'http://www.thinkcmf.com',
        'author_url'  => 'http://www.thinkcmf.com'
    ];

    public $hasAdmin = 0;

    // 插件安装
    public function install()
    {
        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        return true;//卸载成功返回true，失败false
    }

    /**
     * 统一下单
     *
     * @param array $params 订单数据
     * @return bool|mixed|\think\Response
     */
    public function orderPaymentUnifiedorder($params = [])
    {
        try {
            $attach = [
                'user_id'  => $params['user_id'],
                'order_id' => $params['id'],
                'code'     => $params['code']
            ];
            $order     = [
                'attach'       => $attach,
                'out_trade_no' => $params['sn'],
                'body'         => $params['sn'],
                'total_fee'    => (string)bcmul($params['amount'], 100),
            ];
            // 这里传出去支付参数 []  认为支付成功  并模拟异步通知访问插件 异步通知控制器
            
            //回调会执行 订单支付成功 演示用的就 直接写下面了
            $params['pay_amount'] = $order['total_fee']/100;
            $params['pay_sn'] = time();
            $params['pay_time'] = time();
            $params['pay_up_time'] = time();
            $params['pay_method'] = $attach['code'];
            $params['pay_info'] = json_encode($order);
            OrderService::doPaid($attach['user_id'],$attach['order_id'],$params);
            return [];

        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 支付渠道
     *
     * @param array $params
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderPayment($params = [])
    {
        $data = [
            'name'=>'演示用支付插件',
            'code'=>'demopay'
        ];
        return $data;
    }
}