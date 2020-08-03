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

// 实例支付对象
$pay = new \Pay\Pay($config);

if ($pay->driver('alipay')->gateway()->verify($_POST)) {
    file_put_contents('notify.txt', "收到来自支付宝的异步通知\r\n", FILE_APPEND);
    file_put_contents('notify.txt', '订单号：' . $_POST['out_trade_no'] . "\r\n", FILE_APPEND);
    file_put_contents('notify.txt', '订单金额：' . $_POST['total_amount'] . "\r\n\r\n", FILE_APPEND);
} else {
    file_put_contents('notify.txt', "收到异步通知\r\n", FILE_APPEND);
}


// 下面是项目的真实代码
/*
$pay = new \Pay\Pay(config('pay'));
$notifyInfo = $pay->driver('alipay')->gateway('app')->verify(request()->post('', '', null));
p($notifyInfo, false, RUNTIME_PATH . date('Ymd') . '_notify.txt');
if (in_array($notifyInfo['trade_status'], ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
    // 更新订单状态
    $this->updateOrder($notifyInfo['out_trade_no'], $notifyInfo['trade_no'], $notifyInfo['receipt_amount'], 'alipay');
}
*/