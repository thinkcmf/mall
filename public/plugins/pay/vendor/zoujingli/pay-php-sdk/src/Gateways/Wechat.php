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
// | 项目设计及部分源码参考于 yansongda/pay，在此特别感谢！
// +----------------------------------------------------------------------

namespace Pay\Gateways;

use Pay\Contracts\Config;
use Pay\Contracts\GatewayInterface;
use Pay\Contracts\HttpService;
use Pay\Exceptions\Exception;
use Pay\Exceptions\GatewayException;
use Pay\Exceptions\InvalidArgumentException;

/**
 * 微信支付基础类
 * Class Wechat
 * @package Pay\Gateways\Wechat
 */
abstract class Wechat extends GatewayInterface
{

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Config
     */
    protected $userConfig;

    /**
     * @var string
     */
    protected $gateway = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    /**
     * @var string
     */
    protected $gateway_query = 'https://api.mch.weixin.qq.com/pay/orderquery';

    /**
     * @var string
     */
    protected $gateway_close = 'https://api.mch.weixin.qq.com/pay/closeorder';

    /**
     * @var string
     */
    protected $gateway_refund = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    /**
     * @var string
     */
    protected $gateway_transfer = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

    /**
     * @var string
     */
    protected $gateway_micropay = 'https://api.mch.weixin.qq.com/pay/micropay';

    /**
     * @var string
     */
    protected $gateway_bill = 'https://api.mch.weixin.qq.com/pay/downloadbill';

    /**
     * @var string
     */
    protected $gateway_ras_public = 'https://fraud.mch.weixin.qq.com/risk/getpublickey';

    /**
     * @var string
     */
    protected $gateway_paybank = 'https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank';

    /**
     * Wechat constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        $this->debug = !empty($config['debug']);
        $this->userConfig = new Config($config);
        if (is_null($this->userConfig->get('app_id'))) {
            throw new InvalidArgumentException('Missing Config -- [app_id]');
        }
        if (is_null($this->userConfig->get('mch_id'))) {
            throw new InvalidArgumentException('Missing Config -- [mch_id]');
        }
        if (is_null($this->userConfig->get('mch_key'))) {
            throw new InvalidArgumentException('Missing Config -- [mch_key]');
        }
        if (!empty($config['cache_path'])) {
            HttpService::$cachePath = $config['cache_path'];
        }
        // 沙箱模式
        if (!empty($config['debug'])) {
            $this->gateway = 'https://api.mch.weixin.qq.com/sandboxnew/pay/unifiedorder';
            $this->gateway_bill = 'https://api.mch.weixin.qq.com/sandboxnew/pay/downloadbill';
            $this->gateway_query = 'https://api.mch.weixin.qq.com/sandboxnew/pay/orderquery';
            $this->gateway_close = 'https://api.mch.weixin.qq.com/sandboxnew/pay/closeorder';
            $this->gateway_refund = 'https://api.mch.weixin.qq.com/sandboxnew/secapi/pay/refund';
            $this->gateway_transfer = 'https://api.mch.weixin.qq.com/sandboxnew/mmpaymkttransfers/promotion/transfers';
            $this->gateway_micropay = 'https://api.mch.weixin.qq.com/sandboxnew/pay/micropay';
            $this->gateway_paybank = 'https://api.mch.weixin.qq.com/sandboxnew/mmpaysptrans/pay_bank';
//            $this->gateway_ras_public = 'https://fraud.mch.weixin.qq.com/sandboxnew/risk/getpublickey';
            // 沙箱验证签名及沙箱密钥更新
            $sandbox_signkey = HttpService::getCache('sandbox_signkey');
            if (empty($sandbox_signkey)) {
                $data = ['mch_id' => $this->userConfig->get('mch_id', ''), 'nonce_str' => $this->createNonceStr('32')];
                $data['sign'] = $this->getSign($data);
                $result = $this->fromXml($this->post('https://api.mch.weixin.qq.com/sandboxnew/pay/getsignkey', $this->toXml($data)));
                if (isset($result['return_code']) && $result['return_code'] === 'SUCCESS') {
                    $sandbox_signkey = $result['sandbox_signkey'];
                    HttpService::setCache('sandbox_signkey', $sandbox_signkey);
                } else {
                    throw new Exception(isset($result['return_msg']) ? $result['return_msg'] : '沙箱验证签名及获取沙箱密钥失败！');
                }
            }
            $this->userConfig->set('mch_key', $sandbox_signkey);
        }
        $this->config = [
            'appid'      => $this->userConfig->get('app_id', ''),
            'mch_id'     => $this->userConfig->get('mch_id', ''),
            'nonce_str'  => $this->createNonceStr(),
            'sign_type'  => 'MD5',
            'notify_url' => $this->userConfig->get('notify_url', ''),
            'trade_type' => $this->getTradeType(),
        ];
        if ($this->userConfig->offsetExists('sub_appid')) {
            $this->config['sub_appid'] = $this->userConfig->get('sub_appid', '');
        }
        if ($this->userConfig->offsetExists('sub_mch_id')) {
            $this->config['sub_mch_id'] = $this->userConfig->get('sub_mch_id', '');
        }

    }

    /**
     * 订单退款操作
     * @param array $options
     * @return array
     * @throws GatewayException
     */
    public function refund($options = [])
    {
        $this->config = array_merge($this->config, $options);
        $this->config['op_user_id'] = isset($this->config['op_user_id']) ?: $this->userConfig->get('mch_id', '');
        $this->unsetTradeTypeAndNotifyUrl();
        return $this->getResult($this->gateway_refund, true);
    }

    /**
     * 关闭正在进行的订单
     * @param string $out_trade_no
     * @return array
     * @throws GatewayException
     */
    public function close($out_trade_no = '')
    {
        $this->config['out_trade_no'] = $out_trade_no;
        $this->unsetTradeTypeAndNotifyUrl();
        return $this->getResult($this->gateway_close);
    }

    /**
     * 查询订单状态
     * @param string $out_trade_no
     * @return array
     * @throws GatewayException
     */
    public function find($out_trade_no = '')
    {
        $this->config['out_trade_no'] = $out_trade_no;
        $this->unsetTradeTypeAndNotifyUrl();
        return $this->getResult($this->gateway_query);
    }

    /**
     * XML内容验证
     * @param string $data
     * @param null $sign
     * @param bool $sync
     * @return array|bool
     */
    public function verify($data, $sign = null, $sync = false)
    {
        $data = $this->fromXml($data);
        $sign = is_null($sign) ? $data['sign'] : $sign;
        return $this->getSign($data) === $sign ? $data : false;
    }

    /**
     * @return mixed
     */
    abstract protected function getTradeType();

    /**
     * @param array $options
     * @return array
     * @throws GatewayException
     */
    protected function preOrder($options = [])
    {
        $this->config = array_merge($this->config, $options);
        return $this->getResult($this->gateway);
    }

    /**
     * 获取验证访问数据
     * @param string $url
     * @param bool $cert
     * @return array
     * @throws GatewayException
     */
    protected function getResult($url, $cert = false)
    {
        $this->config['sign'] = $this->getSign($this->config);
        if ($cert) {
            $data = $this->fromXml($this->post($url, $this->toXml($this->config), ['ssl_cer' => $this->userConfig->get('ssl_cer', ''), 'ssl_key' => $this->userConfig->get('ssl_key', '')]));
        } else {
            $data = $this->fromXml($this->post($url, $this->toXml($this->config)));
        }
        if (!isset($data['return_code']) || $data['return_code'] !== 'SUCCESS' || $data['result_code'] !== 'SUCCESS') {
            $error = 'ResultError:' . $data['return_msg'];
            $error .= isset($data['err_code_des']) ? ' - ' . $data['err_code_des'] : '';
        }
        if (isset($data['sign'])) {
            if (!isset($error) && $this->getSign($data) !== $data['sign']) {
                $error = 'GetResultError: return data sign error';
            }
        }
        if (isset($error)) {
            throw new GatewayException($error, 20000, $data);
        }
        return $data;
    }


    /**
     * 生成内容签名
     * @param $data
     * @return string
     */
    protected function getSign($data)
    {
        if (is_null($this->userConfig->get('mch_key'))) {
            throw new InvalidArgumentException('Missing Config -- [mch_key]');
        }
        ksort($data);
        $string = md5($this->getSignContent($data) . '&key=' . $this->userConfig->get('mch_key'));
        return strtoupper($string);
    }

    /**
     * 生成签名内容
     * @param $data
     * @return string
     */
    private function getSignContent($data)
    {
        $buff = '';
        foreach ($data as $k => $v) {
            $buff .= ($k != 'sign' && $v != '' && !is_array($v)) ? $k . '=' . $v . '&' : '';
        }
        return trim($buff, '&');
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     */
    protected function createNonceStr($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 转为XML数据
     * @param array $data 源数据
     * @return string
     */
    protected function toXml($data)
    {
        if (!is_array($data) || count($data) <= 0) {
            throw new InvalidArgumentException('convert to xml error !invalid array!');
        }
        $xml = '<xml>';
        foreach ($data as $key => $val) {
            $xml .= (is_numeric($val) ? "<{$key}>{$val}</{$key}>" : "<{$key}><![CDATA[{$val}]]></{$key}>");
        }
        return $xml . '</xml>';
    }

    /**
     * 解析XML数据
     * @param string $xml 源数据
     * @return mixed
     */
    protected function fromXml($xml)
    {
        if (!$xml) {
            throw new InvalidArgumentException('convert to array error !invalid xml');
        }
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
    }

    /**
     * 清理签名验证不必要的参数
     * @return bool
     */
    protected function unsetTradeTypeAndNotifyUrl()
    {
        unset($this->config['notify_url']);
        unset($this->config['trade_type']);
        return true;
    }
}
