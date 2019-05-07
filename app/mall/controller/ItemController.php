<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <catmant@thinkcmf.com>
// +----------------------------------------------------------------------
namespace app\mall\controller;

use app\mall\model\MallAttrModel;
use app\mall\model\MallAttrValueModel;
use app\mall\model\MallItemAttrModel;
use app\mall\model\MallItemModel;
use app\mall\model\MallItemSkuModel;
use app\mall\model\OrderCommentModel;
use cmf\controller\HomeBaseController;
use think\Db;
use tree\Tree;

class ItemController extends HomeBaseController
{
    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {

        $id   = $this->request->param('id', 0, 'intval');
        $item = MallItemModel::get($id);
        $this->assign('item', $item);

        $mallItemSkuModel = new MallItemSkuModel();
        $skuList          = $mallItemSkuModel->where('status',1)->where('item_id', $id)->where('delete_time', 0)->select();

        $skuIds = [];

        if (!$skuList->isEmpty()) {
            $skuAttrIds      = [];
            $skuAttrValueIds = [];
            $itemSkuAttrs    = [];


            foreach ($skuList as $sku) {
                $skuIds[$sku['key']] = [
                    'id'             => $sku['id'],
                    'price'          => $sku['price'],
                    'original_price' => $sku['original_price'],
                    'quantity'       => $sku['quantity']
                ];
                $skuAttrValues       = explode(';', $sku['key']);

                foreach ($skuAttrValues as $skuAttrValue) {
                    $skuAttrValue = explode(':', $skuAttrValue);
                    $attrId       = $skuAttrValue[0];
                    $attrValueId  = $skuAttrValue[1];

                    array_push($skuAttrIds, $skuAttrValue[0]);
                    array_push($skuAttrValueIds, $skuAttrValue[1]);
                    $skuIds[$sku['key']]['attr_values'][$attrId] = $attrValueId;
                }
            }

            $attrModel = new MallAttrModel();
            $attrNames = $attrModel->where('id', 'in', $skuAttrIds)->order('list_order ASC')->column('name', 'id');

            $attrValueModel = new MallAttrValueModel();
            $attrValues     = $attrValueModel->where('id', 'in', $skuAttrValueIds)->field('id,attr_id,value')->select();


            foreach ($attrValues as $attrValue) {
                $attrId                                                            = $attrValue['attr_id'];
                $itemSkuAttrs[$attrId]['name']                                     = $attrNames[$attrId];
                $itemSkuAttrs[$attrId]['values'][$attrId . ':' . $attrValue['id']] = $attrValue;
            }

            $newItemSkuAttrs = [];

            foreach ($attrNames as $attrId => $attrName) {
                $newItemSkuAttrs[$attrId] = $itemSkuAttrs[$attrId];
            }

            $newSkuIds = [];

            foreach ($skuIds as $skuId) {
                $attrValues = $skuId['attr_values'];

                $newKeyArr = [];


                foreach ($attrNames as $attrId => $attrName) {
                    if (isset($attrValues[$attrId])) {
                        array_push($newKeyArr, "{$attrId}:{$attrValues[$attrId]}");
                    }
                }

                $newKey = implode(';', $newKeyArr);

                $newSkuIds[$newKey] = $skuId;
            }

            $skuIds = $newSkuIds;


            $this->assign('item_sku_attrs', $newItemSkuAttrs);

        }

        $this->assign('sku_ids', $skuIds);


        return $this->fetch('item');
    }

    // 添加产品包装到购物车
    public function addToCart()
    {
        $this->checkUserLogin();

        $itemId    = $this->request->param('id', 0, 'intval');
        $itemSkuId = $this->request->param('sku_id', 0, 'intval');
        $buyNow    = $this->request->param('buy_now', 0, 'intval');
        $quantity  = $this->request->param('quantity', 1, 'intval');

        $mallItemModel    = new MallItemModel();
        $mallItemSkuModel = new MallItemSkuModel();

        $findMallItem = $mallItemModel->where('id', $itemId)->find();

        if (empty($findMallItem)) {
            $this->error('商品不存在！');
        }

        $skuWhere = ['item_id' => $itemId];

        if (!empty($itemSkuId)) {
            $skuWhere['id'] = $itemSkuId;
        }

        $findMallItemSku = $mallItemSkuModel->where($skuWhere)->find();
        $price           = 0;

        if (empty($findMallItemSku)) {
            $itemSkuId       = 0;
            $price           = $findMallItem['price'];
            $findMallItemSku = $findMallItem;
            $goodsSpec       = "";
        } else {
            $itemSkuId = $findMallItemSku['id'];
            $goodsSpec = $findMallItemSku['spec_info'];
            $price     = $findMallItemSku['price'];
        }


        $userId      = cmf_get_current_user_id();
        $currentTime = time();
        $expireTime  = $currentTime + 7 * 24 * 3600;

        $goodName = $findMallItem['title'];

        $findProductCountInCart = Db::name('OrderCart')->where([
            'user_id'         => $userId,
            'table_name'      => 'mall_item',
            'goods_sku_table' => 'mall_item_sku',
            'goods_id'        => $itemId,
            'goods_sku_id'    => $itemSkuId,
        ])->count();

        if ($findProductCountInCart == 0) {


            Db::name('OrderCart')->insert([
                'user_id'         => $userId,
                'table_name'      => 'mall_item',
                'goods_sku_table' => 'mall_item_sku',
                'goods_id'        => $itemId,
                'goods_sku_id'    => $itemSkuId,
                'goods_name'      => $goodName,
                'goods_spec'      => $goodsSpec,
                'goods_price'     => $price,
                'goods_quantity'  => $quantity,
                'goods_thumbnail' => $findMallItem['thumbnail'],
                'selected'        => 1,
                'create_time'     => $currentTime,
                'expire_time'     => $expireTime,
                'more'            => json_encode(['goods_spec' => $findMallItemSku])
            ]);
        } else {
            Db::name('OrderCart')->where([
                'user_id'         => $userId,
                'table_name'      => 'mall_item',
                'goods_sku_table' => 'mall_item_sku',
                'goods_id'        => $itemId,
                'goods_sku_id'    => $itemSkuId,
            ])->update([
                'expire_time'    => $expireTime,
                'goods_quantity' => Db::raw('goods_quantity+' . $quantity)
            ]);
        }

        $this->success("产品已成功添加到购物车!", $buyNow ? cmf_url('order/Cart/index') : "");

    }

}
