<?php

namespace plugins\pay\controller;

use api\mall\service\OrderService;
use cmf\controller\PluginRestBaseController;

use think\facade\Log;

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
        $verify = $pay->driver('wechat')->gateway()->verify(file_get_contents('php://input'));

        if ($verify) {
            Log::info('微信收到异步通知验签成功');
            if ($verify['result_code'] === 'SUCCESS') {
                Log::info('微信收到异步通知验签成功  SUCCESS 支付成功');
                $attach = parse_str(urldecode($_POST['attach']));
                $params = [];
                $params['pay_amount'] = $verify['total_fee']/100;
                $params['pay_sn'] = $_POST['transaction_id'];
                $params['pay_time'] = strtotime($_POST['time_end']);
                $params['pay_up_time'] = time();
                $params['pay_method'] = $attach['code'];
                $params['pay_info'] = json_encode($_POST);
                OrderService::doPaid($attach['user_id'],$attach['order_id'],$params);
                $xml = <<<EOF
<xml>
    <return_code><![CDATA[SUCCESS]]></return_code>
    <return_msg><![CDATA[OK]]></return_msg>
</xml>
EOF;
                return xml($xml);
            }

        } else {
            Log::debug('微信收到异步通知验签失败',json_encode($_POST));
        }
    }
}
