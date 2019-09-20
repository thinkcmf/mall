<?php

namespace plugins\express\lib\Trackers;

use plugins\express\lib\Waybill;

interface TrackerInterface
{
    /**
     * 追踪运单（即：查快递）
     *
     * @param Waybill $waybill
     *
     * @return void
     * @throws \plugins\express\lib\Exceptions\TrackingException
     *
     */
    public function track(Waybill $waybill);

    /**
     * 获取完整的快递公司支持列表
     *
     * @return array
     */
    public static function getSupportedExpresses();
}
