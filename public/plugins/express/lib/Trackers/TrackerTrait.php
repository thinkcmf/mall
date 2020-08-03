<?php

namespace plugins\express\lib\Trackers;

use plugins\express\lib\Exceptions\TrackingException;
use plugins\express\lib\Waybill;
use api\mall\model\MallExpressModel;

trait TrackerTrait
{
    /**
     * 解析 JSON 响应
     *
     * @param Curl $curl
     *
     * @return mixed
     */
    protected static function getJsonResponse($curl, $assoc = false)
    {
        $responseRaw = $curl->getBody();
        $response    = json_decode($responseRaw, $assoc);
        if (json_last_error() !== 0) {
            throw new TrackingException('Response data cannot be decoded as json', $responseRaw);
        }

        return $response;
    }

    /**
     * 获取是否支持某个快递公司
     *
     * @param string $expressName
     *
     * @return bool
     */
    public static function isSupported($expressName)
    {
        $list = static::getSupportedExpresses();

        return isset($list[$expressName]);
    }

    /**
     * 获取快递公司代码
     *
     * @param Waybill $waybill
     *
     * @return string
     */
    public function getExpressCode(Waybill $waybill)
    {
        $express = $waybill->getExpress();
        if ($express) {

            if (static::isSupported($express)) {
                $list = static::getSupportedExpresses();

                return $list[$express];
            }
        } elseif ($this instanceof DetectorInterface) {
            $list = $this->detect($waybill);
            if (count($list) > 0) {
                return reset($list);
            }
        }

        throw new TrackingException("Unsupported express name: {$express}");
    }
}
