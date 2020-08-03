<?php
// +----------------------------------------------------------------------
// | CMFMall_2020
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 达达 <ccbox.net@163.com>
// +----------------------------------------------------------------------
namespace app\mall\model;

class MallBrandModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    /**
     * logo地址获取 自动转换  获取器
     *
     * @param $value
     * @return string
     */
    public function getLogoUrlAttr($value, $data){
        return cmf_get_image_url($data['logo']);
    }

}