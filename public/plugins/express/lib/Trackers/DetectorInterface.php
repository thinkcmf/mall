<?php

namespace plugins\express\lib\Trackers;

use plugins\express\lib\Waybill;

interface DetectorInterface
{
    /**
     * 识别快递公司
     *
     * @param Waybill $waybill
     *
     * @return array
     */
    public function detect(Waybill $waybill);
}
