<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
return [
    'dev'             => [
        'title'   => '第三方支付沙箱',
        'type'    => 'radio',
        'options' => [
            0 => '关闭',
            1 => '启用'
        ],
        'value'   => '0',
        'tip'     => '沙箱模式'
    ],
    'ali_app_id'      => [
        'title' => '支付宝appid',
        'type'  => 'text',
        'value' => '',
        'tip'   => '支付宝appid'
    ],
    'ali_public_key'  => [
        'title' => '支付宝公钥（注意不是应用公钥）',
        'type'  => 'text',
        'value' => '',
        'tip'   => '支付宝公钥（注意不是应用公钥）'
    ],
    'ali_private_key' => [
        'title' => '支付宝应用私钥',
        'type'  => 'text',
        'value' => '',
        'tip'   => '支付宝应用私钥'
    ],
    'wx_app_id'       => [
        'title' => '微信appid',
        'type'  => 'text',
        'value' => '',
        'tip'   => '微信appid'
    ],
    'wx_mch_id'       => [
        'title' => '微信支付商户号',
        'type'  => 'text',
        'value' => '',
        'tip'   => '微信支付商户号'
    ],
    'wx_mch_key'      => [
        'title' => '微信支付密钥',
        'type'  => 'text',
        'value' => '',
        'tip'   => '微信支付密钥'
    ]
];
					