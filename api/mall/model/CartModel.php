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
namespace api\mall\model;

use app\mall\model\MallGoodsModel;
use app\mall\model\MallGoodsSkuModel;
use think\Model;

class CartModel extends Model
{
    protected $autoWriteTimestamp = true;

    // protected $type = [];

    protected $goodsTable = 'mall_goods';
    protected $skuTable = 'mall_goods_sku';

    /**
     * 商品 关联模型 
     * 【注意：默认表的模型关联！】
     *
     * @return \think\model\relation\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo(MallGoodsModel::class, 'goods_id');
    }

    public function sku()
    {
        return $this->belongsTo(MallGoodsSkuModel::class, 'sku_id');
    }
}
