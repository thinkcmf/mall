<?php

// +----------------------------------------------------------------------
// | pay-php-sdk
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/pay-php-sdk
// +----------------------------------------------------------------------

include '../init.php';

// 加载配置参数
$config = require(__DIR__ . '/config.php');

$pay = new Pay\Pay($config);
$verify = $pay->driver('wechat')->gateway('mp')->verify(file_get_contents('php://input'));

if ($verify) {
    file_put_contents('notify.txt', "收到来自微信的异步通知\r\n", FILE_APPEND);
    file_put_contents('notify.txt', '订单号：' . $verify['out_trade_no'] . "\r\n", FILE_APPEND);
    file_put_contents('notify.txt', '订单金额：' . $verify['total_fee'] . "\r\n\r\n", FILE_APPEND);
} else {
    file_put_contents('notify.txt', "收到异步通知\r\n", FILE_APPEND);
}


echo '<xml><return_code>SUCCESS</return_code><return_msg>OK</return_msg></xml>';


// 下面是项目的真实代码
/*
$pay = new Pay\Pay($config);
$notifyInfo = $pay->driver('wechat')->gateway('mp')->verify(file_get_contents('php://input'));
// 支付通知数据获取成功
if ($notifyInfo['result_code'] == 'SUCCESS' && $notifyInfo['return_code'] == 'SUCCESS') {
    $order_no = substr($notifyInfo['out_trade_no'], 0, 10);
    // 更新订单状态
    $this->updateOrder($order_no, $notifyInfo['transaction_id'], $notifyInfo['cash_fee'] / 100, 'wechat');
}
echo '<xml><return_code>SUCCESS</return_code><return_msg>OK</return_msg></xml>';
*/
