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

namespace Pay\Gateways\Wechat;

use Pay\Contracts\HttpService;
use Pay\Exceptions\Exception;
use Pay\Exceptions\GatewayException;
use Pay\Exceptions\InvalidArgumentException;
use Pay\Gateways\Wechat;

/**
 * 转账到银行卡
 * Class BankGateway
 * @package Pay\Gateways\Wechat
 */
class BankGateway extends Wechat
{

    protected $gateway_query = 'https://api.mch.weixin.qq.com/mmpaysptrans/query_bank';

    /**
     * 发起支付
     * @param array $options
     * @return mixed
     * @throws Exception
     * @throws GatewayException
     */
    public function apply(array $options)
    {
        if (!isset($options['partner_trade_no'])) {
            throw new InvalidArgumentException('Missing Options -- [partner_trade_no]');
        }
        if (!isset($options['enc_bank_no'])) {
            throw new InvalidArgumentException('Missing Options -- [enc_bank_no]');
        }
        if (!isset($options['enc_true_name'])) {
            throw new InvalidArgumentException('Missing Options -- [enc_true_name]');
        }
        if (!isset($options['bank_code'])) {
            throw new InvalidArgumentException('Missing Options -- [bank_code]');
        }
        if (!isset($options['amount'])) {
            throw new InvalidArgumentException('Missing Options -- [amount]');
        }
        unset($this->config['appid'], $this->config['notify_url'], $this->config['trade_type'], $this->config['sign_type']);
        if (isset($options['desc'])) {
            $this->config['desc'] = $options['desc'];
        }
        $this->config['amount'] = $options['amount'];
        $this->config['bank_code'] = $options['bank_code'];
        $this->config['partner_trade_no'] = $options['partner_trade_no'];
        $this->config['enc_bank_no'] = $this->rsaEncode($options['enc_bank_no']);
        $this->config['enc_true_name'] = $this->rsaEncode($options['enc_true_name']);
        return $this->getResult($this->gateway_paybank, true);
    }

    /**
     * 查询订单状态
     * @param string $partner_trade_no 商户订单号
     * @return array
     * @throws GatewayException
     */
    public function find($partner_trade_no = '')
    {
        $this->unsetTradeTypeAndNotifyUrl();
        $this->config['partner_trade_no'] = $partner_trade_no;
        unset($this->config['appid'], $this->config['sign_type']);
        return $this->getResult($this->gateway_query, true);
    }

    /**
     * @return string
     */
    protected function getTradeType()
    {
        return '';
    }

    /**
     * @param string $string
     * @param string $encrypted
     * @return string
     * @throws GatewayException
     * @throws Exception
     */
    protected function rsaEncode($string, $encrypted = '')
    {
        $search = ['-----BEGIN RSA PUBLIC KEY-----', '-----END RSA PUBLIC KEY-----', "\n", "\r"];
        $pkc1 = str_replace($search, '', $this->getRsaContent());
        $publicKey = '-----BEGIN PUBLIC KEY-----' . PHP_EOL . wordwrap('MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8A' . $pkc1, 64, PHP_EOL, true) . PHP_EOL . '-----END PUBLIC KEY-----';
        if (!openssl_public_encrypt("{$string}", $encrypted, $publicKey, OPENSSL_PKCS1_OAEP_PADDING)) {
            throw new Exception('Rsa Encrypt Error.');
        }
        return base64_encode($encrypted);
    }

    /**
     * @return string
     * @throws GatewayException
     * @throws Exception
     */
    protected function getRsaContent()
    {
        $cacheKey = "pub_ras_key_" . (empty($this->debug) ? '' : 'debug_') . $this->userConfig->get('mch_id');
        if (($pub_key = HttpService::getCache($cacheKey))) {
            return $pub_key;
        }
        $options = [
            'mch_id'    => $this->userConfig->get('mch_id'),
            'nonce_str' => $this->createNonceStr(64),
            'sign_type' => 'MD5',
        ];
        $options['sign'] = $this->getSign($options);
        $url = 'https://fraud.mch.weixin.qq.com/risk/getpublickey';
        $data = $this->fromXml($this->post($url, $this->toXml($options),
            ['ssl_cer' => $this->userConfig->get('ssl_cer', ''), 'ssl_key' => $this->userConfig->get('ssl_key', '')]
        ));
        if (!isset($data['return_code']) || $data['return_code'] !== 'SUCCESS' || $data['result_code'] !== 'SUCCESS') {
            $error = 'ResultError:' . $data['return_msg'];
            $error .= isset($data['err_code_des']) ? ' - ' . $data['err_code_des'] : '';
            throw new GatewayException($error, 20000, $data);
        }
        HttpService::setCache($cacheKey, $data['pub_key'], 600);
        return $data['pub_key'];
    }
}