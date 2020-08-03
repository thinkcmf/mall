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

use Pay\Gateways\Wechat;

/**
 * 微信小程序支付网关
 * Class MiniappGateway
 * @package Pay\Gateways\Wechat
 */
class MiniappGateway extends Wechat
{

    /**
     * 当前操作类型
     * @return string
     */
    protected function getTradeType()
    {
        return 'JSAPI';
    }

    /**
     * 应用并返回参数
     * @param array $options
     * @return array
     * @throws \Pay\Exceptions\GatewayException
     */
    public function apply(array $options = [])
    {
        $this->config['appid'] = $this->userConfig->get('app_id');
        $payRequest = [
            'appId'     => $this->config['appid'],
            'timeStamp' => time() . '',
            'nonceStr'  => $this->createNonceStr(),
            'package'   => 'prepay_id=' . $this->preOrder($options)['prepay_id'],
            'signType'  => 'MD5',
        ];
        $payRequest['paySign'] = $this->getSign($payRequest);
        return $payRequest;
    }
}
