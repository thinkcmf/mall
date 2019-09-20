<?php

namespace plugins\express\lib\Trackers;

use plugins\express\lib\Exceptions\TrackingException;
use plugins\express\lib\Traces;
use plugins\express\lib\Waybill;
use plugins\express\lib\Config;
use GuzzleHttp\Client;

class Kuaidiniao implements TrackerInterface
{
    use TrackerTrait;

    public $businessId;

    public $appKey;

    /**
     * 快递鸟
     *
     * @param string $businessId EBusinessID
     * @param string $appKey AppKey
     */
    public function __construct($businessId, $appKey)
    {
        $this->businessId = $businessId;
        $this->appKey     = $appKey;
    }

    public static function getSupportedExpresses()
    {
        return Config::codeMap('Kuaidiniao');
    }

    public function track(Waybill $waybill)
    {
        $data = [
            'LogisticCode' => $waybill->id,
            'ShipperCode'  => $this->getExpressCode($waybill)
        ];
        if($data['ShipperCode'] == 'SF'){
            $data['CustomerName'] = $waybill->customerName;
        }
        $requestData = json_encode($data);

        if ($requestData === false) {
            throw new \RuntimeException('Function json_encode returns false');
        }
        $params   = [
            'RequestData' => urlencode($requestData),
            'EBusinessID' => $this->businessId,
            'RequestType' => '1002',
            'DataSign'    => urlencode(base64_encode(md5($requestData . $this->appKey))),
            'DataType'    => '2',
        ];
        $curl     = new Client();
        $curl     = $curl->request(
            'POST',
            'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx',
            ['form_params' => $params]
        );
        $response = static::getJsonResponse($curl);
        if ($response->Success == false) {
            throw new TrackingException($response->Reason, $response);
        }
        $statusMap = [
            0 => Waybill::STATUS_PICKEDUP,
            2 => Waybill::STATUS_TRANSPORTING,
            3 => Waybill::STATUS_DELIVERED,
            4 => Waybill::STATUS_REJECTED,
        ];
        $waybill->setStatus($response->State, $statusMap);
        $waybill->setTraces(
            Traces::parse($response->Traces, 'AcceptTime', 'AcceptStation', 'Remark')
        );
    }
}
