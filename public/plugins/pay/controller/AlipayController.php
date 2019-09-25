<?php

namespace plugins\pay\controller;

use app\order\service\ApiService;
use cmf\controller\PluginRestBaseController;
use think\facade\Log;
use api\mall\service\OrderService;

class AlipayController extends PluginRestBaseController
{
    protected $config = [];

    public function initialize()
    {
        $this->config = \plugins\pay\lib\Config::get();
    }

    public function index()
    {

    }

    public function return()
    {

    }

    public function notify()
    {
        // 实例支付对象
        $pay = new \Pay\Pay($this->config);

        if ($pay->driver('alipay')->gateway()->verify($_POST) && $_POST['app_id'] == $this->config['alipay']['app_id']) {
            Log::info('alipay收到异步通知验签成功');
            if ($_POST['trade_status'] === 'TRADE_SUCCESS' || $_POST['trade_status'] === 'TRADE_FINISHED') {
                Log::info('alipay收到异步通知验签成功 支付成功');
                $params = [];
                $params['amount'] = $_POST['total_amount'];
                $params['out_transaction_id'] = $_POST['trade_no'];
                ApiService::orderPayed($_POST['out_trade_no'],$params);
            }
            return response('success');
        } else {
            Log::debug('alipay收到异步通知',json_encode($_POST));
            echo "fail";
        }
    }
}
