<?php

namespace plugins\pay;

use cmf\lib\Plugin;
use plugins\pay\lib\PayClass;
use think\Db;
use think\exception\HttpResponseException;


class PayPlugin extends Plugin
{
    public $info = [
        'name'        => 'Pay',
        'title'       => '支付插件',
        'description' => '支付插件',
        'status'      => 1,
        'author'      => '五五',
        'version'     => '1.0',
        'demo_url'    => 'http://www.thinkcmf.com',
        'author_url'  => 'http://www.thinkcmf.com'
    ];

    public $hasAdmin = 0;

    protected $payment = [
            [
                'code'        => PayClass::CMF_WECHAT_MINI,
                'name'        => '微信小程序支付',
                'description' => '微信小程序支付',
                'tips'        => '微信小程序',
            ],
//            [
//                'code'        => PayClass::CMF_WECHAT_QRCODE,
//                'name'        => '微信扫码支付',
//                'description' => '微信扫码支付',
//                'tips'        => '微信扫码',
//            ],
//            [
//                'code'        => PayClass::CMF_WECHAT_H5,
//                'name'        => '微信H5支付',
//                'description' => '微信H5支付',
//                'tips'        => '微信H5',
//            ],
//            [
//                'code'        => PayClass::CMF_WECHAT_MP,
//                'name'        => '微信扫码支付',
//                'description' => '微信扫码支付',
//                'tips'        => '微信扫码',
//            ],
//            [
//                'code'        => PayClass::CMF_ALIPAY_WEB,
//                'name'        => '支付宝web支付',
//                'description' => '支付宝web支付',
//                'tips'        => '支付宝跳转',
//            ],
//            [
//                'code'        => PayClass::CMF_ALIPAY_H5,
//                'name'        => '支付宝H5支付',
//                'description' => '支付宝H5支付',
//                'tips'        => '支付宝跳转',
//            ]
        ];

    // 插件安装
    public function install()
    {

        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        return true;//卸载成功返回true，失败false
    }

    /**
     * 统一下单
     *
     * @param array $params 订单数据
     * @return bool|mixed|\think\Response
     */
    public function orderPaymentUnifiedorder($params = [])
    {
        try {
            $attach = [
                'user_id'  => $params['user_id'],
                'order_id' => $params['id'],
                'code'     => $params['code']
            ];
            $attach = urlencode(http_build_query($attach));
            switch ($params['code']) {
                case PayClass::CMF_WECHAT_MINI:
                    $payType   = 'wechat';
                    $payMethod = 'miniapp';
                    $order     = [
                        'attach'       => $attach,
                        'out_trade_no' => $params['sn'],
                        'body'         => $params['sn'],
                        'total_fee'    => (string)bcmul($params['amount'], 100),
                    ];
                    break;
                case PayClass::CMF_WECHAT_MP:
                    $payType   = 'wechat';
                    $payMethod = 'mp';
                    $order     = [
                        'attach'       => $attach,
                        'out_trade_no' => $params['sn'],
                        'body'         => $params['sn'],
                        'total_fee'    => (string)bcmul($params['amount'], 100),
                    ];
                    break;
                case PayClass::CMF_WECHAT_H5:
                    $payType   = 'wechat';
                    $payMethod = 'wap';
                    $order     = [
                        'attach'       => $attach,
                        'out_trade_no' => $params['sn'],
                        'body'         => $params['sn'],
                        'total_fee'    => (string)bcmul($params['amount'], 100),
                    ];
                    break;
                case PayClass::CMF_ALIPAY_WEB:
                    $payType   = 'alipay';
                    $payMethod = 'web';
                    $order     = [
                        'attach'       => $attach,
                        'out_trade_no' => $params['sn'],
                        'subject'      => $params['sn'],
                        'total_amount' => $params['amount'],
                    ];
                    break;
                case PayClass::CMF_ALIPAY_H5:
                    $payType   = 'alipay';
                    $payMethod = 'wap';
                    $order     = [
                        'attach'       => $attach,
                        'out_trade_no' => $params['sn'],
                        'subject'      => $params['sn'],
                        'total_amount' => $params['amount'],
                    ];
                    break;
                default:
                    return false;
            }
            $config  = \plugins\pay\lib\Config::get();
            $pay     = new \Pay\Pay($config);
            $options = $pay->driver($payType)->gateway($payMethod)->apply($order);
            switch ($params['code']) {
                case PayClass::CMF_WECHAT_MINI:
                    return $options;
                    break;
                case PayClass::CMF_WECHAT_QRCODE:
                    return $options;
                    break;
                case PayClass::CMF_WECHAT_MP:
                    return $options;
                    break;
                case PayClass::CMF_WECHAT_H5:
                    throw new HttpResponseException(\redirect($options));
                    break;
                case PayClass::CMF_ALIPAY_WEB:
                    throw new HttpResponseException(\response($options));
                    break;
                case PayClass::CMF_ALIPAY_H5:
                    throw new HttpResponseException(\response($options));
                    break;
                default:
                    return $options;
            }

        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 支付渠道
     *
     * @param array $params
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderPayment($params = [])
    {
        $data = $this->payment;
        return $data;
    }
}