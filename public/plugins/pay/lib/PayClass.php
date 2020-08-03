<?php


namespace plugins\pay\lib;


class PayClass
{
    const CMF_WECHAT_QRCODE = 'cmf-wechat-qrcode';
    const CMF_WECHAT_H5     = 'cmf-wechat-h5';
    const CMF_WECHAT_MINI   = 'cmf-wechat-miniapp';
    const CMF_WECHAT_MP     = 'cmf-wechat-mp';
    const CMF_ALIPAY_WEB    = 'cmf-alipay-web';
    const CMF_ALIPAY_H5     = 'cmf-alipay-h5';
    const CMF_ALIPAY_MINI   = 'cmf-alipay-miniapp';

    public static function all()
    {
        return [

        ];
    }
}