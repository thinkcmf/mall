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
    'express_trail' => [
        "type"        => 2,
        "name"        => '物流轨迹', // 钩子名称
        "description" => "物流轨迹", //钩子描述
        "once"        => 1 // 是否只执行一次
    ],
    'express_channel' => [
        "type"        => 2,
        "name"        => '物流公司', // 钩子名称
        "description" => "物流公司", //钩子描述
        "once"        => 1 // 是否只执行一次
    ],
    'area_provinces'=>[
        "type"        => 2,
        "name"        => '获取省份', // 钩子名称
        "description" => "物流轨迹", //钩子描述
        "once"        => 1 // 是否只执行一次
    ],
    'area_city'=>[
        "type"        => 2,
        "name"        => '获取城市', // 钩子名称
        "description" => "物流轨迹", //钩子描述
        "once"        => 1 // 是否只执行一次
    ],
    'area_district'=>[
        "type"        => 2,
        "name"        => '获取行政区', // 钩子名称
        "description" => "物流轨迹", //钩子描述
        "once"        => 1 // 是否只执行一次
    ],
    
];