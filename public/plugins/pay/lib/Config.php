<?php

namespace plugins\pay\lib;

use think\facade\Env;

class Config
{
    public static function get($key = false)
    {
        $data   = [];
        $config = cmf_get_plugin_config('Pay');
        $dev    = isset($config['dev']) ? $config['dev'] : false;

        $config = [
            // 微信支付参数
            'wechat' => [
                'debug'      => $dev, // 沙箱模式
                'app_id'     => isset($config['wx_app_id']) ? $config['wx_app_id'] : '', // 应用ID
                'mch_id'     => isset($config['wx_mch_id']) ? $config['wx_mch_id'] : '', // 微信支付商户号
                'mch_key'    => isset($config['wx_mch_key']) ? $config['wx_mch_key'] : '', // 微信支付密钥
                'ssl_cer'    => '', // 微信证书 cert 文件
                'ssl_key'    => '', // 微信证书 key 文件
                'notify_url' => cmf_plugin_url('Pay://Alipay/notify', [], true), // 支付通知URL
                'cache_path' => Env::get('runtime_path'),// 缓存目录配置（沙箱模式需要用到）
            ],
            // 支付宝支付参数
            'alipay' => [
                'debug'       => $dev, // 沙箱模式
                'app_id'      => isset($config['ali_app_id']) ? $config['ali_app_id'] : '', // 应用ID
                'public_key'  => isset($config['ali_public_key']) ? $config['ali_public_key'] : '',
                'private_key' => isset($config['ali_private_key']) ? $config['ali_private_key'] : '',
                'notify_url'  => cmf_plugin_url('Pay://Alipay/notify', [], true), // 支付通知URL
                'return_url'  => cmf_url('order/order/index',[],true,true),
            ]
        ];
        if($key && isset($config[$key])){
            return $config[$key];
        }
        return $config;
    }
}