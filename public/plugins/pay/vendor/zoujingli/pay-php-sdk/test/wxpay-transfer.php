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

// 支付参数
$options = [
    'partner_trade_no' => '42134122', //商户订单号
    'openid'           => 'ol0Q_uJUcrb1DOjmQRycmSpLjRmo', //收款人的openid
    'check_name'       => 'NO_CHECK', //NO_CHECK：不校验真实姓名\FORCE_CHECK：强校验真实姓名
    // 're_user_name'     => '张三', //check_name为 FORCE_CHECK 校验实名的时候必须提交
    'amount'           => '101', //企业付款金额，单位为分
    'desc'             => '帐户提现', //付款说明
    'spbill_create_ip' => '192.168.0.1', //发起交易的IP地址
];

// 实例支付对象
$pay = new \Pay\Pay($config);

try {
    $result = $pay->driver('wechat')->gateway('transfer')->apply($options);
    echo '<pre>';
    var_export($result);
} catch (Exception $e) {
    echo $e->getMessage();
}


