<?php

namespace plugins\pay\controller;

use app\order\service\ApiService;
use cmf\controller\PluginRestBaseController;

use think\facade\Log;
use api\mall\service\OrderService;

class WechatController extends PluginRestBaseController
{
    protected $config = [];

    public function initialize()
    {
        $this->config = \plugins\pay\lib\Config::wechat();
    }

    public function index()
    {

    }

    public function return()
    {

    }

    public function notify()
    {
        $pay    = new \Pay\Pay($this->config);
        $verify = $pay->driver('wechat')->gateway('wap')->verify(file_get_contents('php://input'));

        if ($verify) {
            Log::info('微信收到异步通知验签成功');
            if ($verify['result_code'] === 'SUCCESS') {
                $params = [];
                $params['amount'] = $verify['total_fee']/100;
                $params['out_transaction_id'] = $_POST['transaction_id'];
                ApiService::orderPayed($verify['out_trade_no'],$params);
                $xml = <<<EOF
<xml>
    <return_code><![CDATA[SUCCESS]]></return_code>
    <return_msg><![CDATA[OK]]></return_msg>
</xml>
EOF;
                return xml($xml);
            }

        } else {
            Log::debug('微信收到异步通知验签失败');
        }
    }
}
