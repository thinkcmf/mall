<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
return [
    'order_paid' => [
        "type"        => 2,
        "name"        => '订单支付完成', // 钩子名称
        "description" => "订单支付完成", //钩子描述
        "once"        => 0 // 是否只执行一次
    ],
    'order_payment' => [
        "type"        => 2,
        "name"        => '获取支付渠道', // 钩子名称
        "description" => "获取支付渠道", //钩子描述
        "once"        => 1 // 是否只执行一次
    ],
    'order_payment_unifiedorder' => [
        "type"        => 2,
        "name"        => '支付统一下单', // 钩子名称
        "description" => "支付统一下单", //钩子描述
        "once"        => 1 // 是否只执行一次
    ],
];