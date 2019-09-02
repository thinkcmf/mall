<?php

namespace plugins\pay;

use cmf\lib\Plugin;
use think\Db;
use think\exception\HttpResponseException;


class PayPlugin extends Plugin
{
    const CMF_WECHAT_QRCODE = 'cmf-wechat-qrcode';
    const CMF_WECHAT_H5     = 'cmf-wechat-h5';
    const CMF_WECHAT_MP     = 'cmf-wechat-mp';
    const CMF_ALIPAY_WEB    = 'cmf-alipay-web';
    const CMF_ALIPAY_H5     = 'cmf-alipay-h5';

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

    // 插件安装
    public function install()
    {
        Db::name('OrderPayment')->delete(true);
        Db::name('OrderPayment')->insertAll([
            [
                'code'        => self::CMF_WECHAT_QRCODE,
                'name'        => '微信扫码支付',
                'description' => '微信扫码支付',
                'tips'        => '微信扫码',
            ],
            [
                'code'        => self::CMF_WECHAT_H5,
                'name'        => '微信H5支付',
                'description' => '微信H5支付',
                'tips'        => '微信H5',
            ],
            [
                'code'        => self::CMF_WECHAT_MP,
                'name'        => '微信扫码支付',
                'description' => '微信扫码支付',
                'tips'        => '微信扫码',
            ],
            [
                'code'        => self::CMF_ALIPAY_WEB,
                'name'        => '支付宝web支付',
                'description' => '支付宝web支付',
                'tips'        => '支付宝跳转',
            ],
            [
                'code'        => self::CMF_ALIPAY_H5,
                'name'        => '支付宝H5支付',
                'description' => '支付宝H5支付',
                'tips'        => '支付宝跳转',
            ]
        ]);
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
     * @param array $params
     * @return bool|mixed|\think\Response
     */
    public function orderPaymentUnifiedorder($params = [])
    {
        try {
            switch ($params['code']) {
                case self::CMF_WECHAT_QRCODE:
                    $payType   = 'wechat';
                    $payMethod = 'scan';
                    $order     = [
                        'out_trade_no' => $params['sn'],
                        'body'         => $params['sn'],
                        'total_fee'    => (string)bcmul($params['amount'], 100),
                    ];
                    break;
                case self::CMF_WECHAT_MP:
                    $payType   = 'wechat';
                    $payMethod = 'mp';
                    $order     = [
                        'out_trade_no' => $params['sn'],
                        'body'         => $params['sn'],
                        'total_fee'    => (string)bcmul($params['amount'], 100),
                    ];
                    break;
                case self::CMF_WECHAT_H5:
                    $payType   = 'wechat';
                    $payMethod = 'wap';
                    $order     = [
                        'out_trade_no' => $params['sn'],
                        'body'         => $params['sn'],
                        'total_fee'    => (string)bcmul($params['amount'], 100),
                    ];
                    break;
                case self::CMF_ALIPAY_WEB:
                    $payType   = 'alipay';
                    $payMethod = 'web';
                    $order     = [
                        'out_trade_no' => $params['sn'],
                        'subject'      => $params['sn'],
                        'total_amount' => $params['amount'],
                    ];
                    break;
                case self::CMF_ALIPAY_H5:
                    $payType   = 'alipay';
                    $payMethod = 'wap';
                    $order     = [
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
                case self::CMF_WECHAT_QRCODE:
                    return $options;
                    break;
                case self::CMF_WECHAT_MP:
                    return $options;
                    break;
                case self::CMF_WECHAT_H5:
                    throw new HttpResponseException(\redirect($options));
                    break;
                case self::CMF_ALIPAY_WEB:
                    throw new HttpResponseException(\response($options));
                    break;
                case self::CMF_ALIPAY_H5:
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
        $data = [];
        if (cmf_is_mobile()) {
            $data = Db::name('OrderPayment')
                ->where('status', 1)
                ->where('code',self::CMF_ALIPAY_H5)
                ->whereOr(
                    'code',self::CMF_WECHAT_H5
                )
                ->field(true)
                ->select();
        } else {
            $data = Db::name('OrderPayment')
                ->where('status', 1)
                ->where('code',self::CMF_ALIPAY_WEB)
                ->whereOr(
                    'code',self::CMF_WECHAT_QRCODE
                )
                ->field(true)
                ->select();
        }
        if (cmf_is_wechat()) {
            $data = Db::name('OrderPayment')
                ->where('status', 1)
                ->where('code',self::CMF_WECHAT_MP)
                ->field(true)
                ->select();
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $data = Db::name('OrderPayment')
                ->where('status', 1)
                ->where('code',self::CMF_ALIPAY_H5)
                ->field(true)
                ->select();
        }
        return $data;
    }
}