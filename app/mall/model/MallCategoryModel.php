<?php

namespace app\mall\model;

class MallCategoryModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected $type = [
        'more' => 'array',
    ];

    /**
     * 缩略图 获取器 自动转换
     *
     * @param $value
     * @return string
     */
    public function getThumbnailUrlAttr($value, $data)
    {
        return cmf_get_image_url($data['thumbnail']);
    }
}
