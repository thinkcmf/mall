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

// 原商户订单号
$out_trade_no = '56737188841424';

// 申请退款金额
$refund_fee = '1.00';

try {
    $result = $pay->driver('alipay')->gateway('transfer')->refund($out_trade_no, $refund_fee);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}