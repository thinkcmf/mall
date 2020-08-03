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

class MallGoodsSkuModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected $type = [
        'more' => 'array'
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

    /**
     * 商品 关联模型
     *
     * @return \think\model\relation\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo(MallGoodsModel::class,'goods_id');
    }
}