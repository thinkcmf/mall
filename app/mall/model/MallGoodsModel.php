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

class MallGoodsModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected $type = [
        'more' => 'array',
        'thumbnails' => 'array',
    ];

    public function setContentAttr($value)
    {
        if(is_array($value)){
            return json_encode($value);
        }
        return [];
    }

    public function getContentAttr($value)
    {
        $data = json_decode($value, true);
        if(!empty($data) && is_array($data)){
            foreach($data as &$val){
                $src = $val['file_path'] ?? '';
                $val['file_url'] = cmf_get_image_url($src);
            }
        }
        return $data;
    }

    /**
     * 缩略图url 获取器 自动转换
     *
     * @param $value
     * @return string
     */
    public function getThumbnailUrlAttr($value, $data)
    {
        return cmf_get_image_url($data['thumbnail']);
    }

    /**
     * 轮播图url 获取器 自动转换
     *
     * @param $value
     * @return string
     */
    public function getThumbnailsUrlAttr($values, $data)
    {
        $thumbnails = json_decode($data['thumbnails'],true);
        if (is_array($thumbnails)) {
            return array_map(function ($value) {
                return cmf_get_image_url($value);
            }, $thumbnails);
        }
    }

    /**
     * 品牌 关联模型
     *
     * @return \think\model\relation\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(MallBrandModel::class, 'brand_id');
    }

    /**
     * 分类 关联模型
     *
     * @return \think\model\relation\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(MallCategoryModel::class, 'category_id');
    }

    /**
     * 商品SKU 关联模型
     *
     * @return
     */
    public function skus()
    {
        return $this->hasMany(MallGoodsSkuModel::class, 'goods_id');
    }
}
