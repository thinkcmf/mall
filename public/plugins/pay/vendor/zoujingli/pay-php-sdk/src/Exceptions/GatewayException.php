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

namespace Pay\Exceptions;

/**
 * 支付网关异常类
 * Class GatewayException
 * @package Pay\Exceptions
 */
class GatewayException extends Exception
{
    /**
     * error raw data.
     * @var array
     */
    public $raw = [];

    /**
     * GatewayException constructor.
     * @param string $message
     * @param int $code
     * @param array $raw
     */
    public function __construct($message, $code, $raw = [])
    {
        parent::__construct($message, intval($code));
        $this->raw = $raw;
    }
}
