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
namespace api\mall\service;

use app\mall\model\MallGoodsModel;
use app\mall\model\MallGoodsSkuModel;

/**
 * 服务提供： 商品
 *
 */
class GoodsService
{

    /**
     * 商品列表
     */
    public static function goodsList($type = '', $category_id = 0, $brand_id = 0, $limit = 'paginate')
    {
        $model = new MallGoodsModel();

        $map   = [];
        $map[] = ['status', '=', 1];

        switch ($type) {
            case 'top':
                $map[] = ['is_top', '=', '1'];
                break;
            case 'recommended':
                $map[] = ['recommended', '=', '1'];
                break;
            case 'new':
                $map[] = ['is_new', '=', '1'];
                break;
            case 'hot':
                $map[] = ['is_hot', '=', '1'];
                break;
            default:
                break;
        }

        if (!empty($category_id)) {
            $map[] = ['category_id', '=', $category_id];
        }

        if (!empty($brand_id)) {
            $map[] = ['brand_id', 'in', $brand_id];
        }

        $builder = self::getBuilder($model, $map);
        if ($limit === 'paginate') {
            $data = $builder->paginate();

            $items = array_map([__CLASS__, 'listItemFilter'], $data->items());

            return [
                'total'        => $data->total(),
                'list_rows'    => $data->listRows(),
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'has_more'     => $data->currentPage() < $data->lastPage(),
                'items'        => $items
            ];
        }
        $data = $builder->limit($limit)->select();

        $items = array_map([__CLASS__, 'listItemFilter'], $data);

        return $items;
    }

    public static function getBuilder(
        MallGoodsModel $model,
        array $map,
        $keyField = 'title|subtitle|keywords|description',
        $orderFields = ['is_top', 'recommended', 'is_new', 'is_hot'],
        $orderDefault = 'list_order'
    )
    {
        $builder = QueryService::getBuilder($model, $map, $keyField, $orderFields, $orderDefault);
        // order by
        $request = request();
        $order   = $request->param('order');
        if ($order) {
            $desc  = substr($order, 0, 1) == '+' ? 'asc' : 'desc';
            $order = substr($order, 1);
            if ($order == 'price') {
                $orderby = ['price_min' => $desc];
                $builder = $model->order($orderby);
            }
            if ($order == 'sold') {
                $orderby = ['sold_count' => $desc];
                $builder = $model->order($orderby);
            }
        }

        return $builder;
    }

    public static function listItemFilter($item)
    {
        return [
            'id'               => $item->id,
            // 'create_time'    => $item->create_time,
            'update_time'      => $item->update_time,
            // 'delete_time'    => $item->delete_time,
            // 'status'         => $item->status,
            // 'list_order'     => $item->list_order,
            'title'            => $item->title,
            'subtitle'         => $item->subtitle,
            'thumbnail'        => $item->thumbnail,
            'thumbnail_url'    => $item->thumbnail_url,
            'keywords'         => $item->keywords,
            // 'description'    => $item->description,
            'video'            => $item->video,
            // 'thumbnails'     => $item->thumbnails,
            // 'thumbnails_url'     => $item->thumbnails_url,
            'category_id'      => $item->category_id,
            'brand_id'         => $item->brand_id,
            'price_min'        => $item->price_min,
            'price_max'        => $item->price_max,
            'price_market_min' => $item->price_market_min,
            'price_market_max' => $item->price_market_max,
            // 'quantity'       => $item->quantity,
            'is_top'           => $item->is_top,
            'recommended'      => $item->recommended,
            'is_new'           => $item->is_new,
            'is_hot'           => $item->is_hot,
            'view_count'       => $item->view_count,
            'favorite_count'   => $item->favorite_count,
            'like_count'       => $item->like_count,
            'sold_count'       => $item->sold_count,
            // 'on_list'        => $item->on_list,
            'type'             => $item->type,
            // 'content'        => $item->content,
            'more'             => $item->more,
            // 'skus'           => $item->skus->map(function ($sku) {
            //     return self::skuFilter($sku);
            // }),
        ];
    }

    public static function goods($id)
    {
        $res   = [
            'error' => null,
            'data'  => []
        ];
        $goods = MallGoodsModel::with('skus')->get($id);

        if ($goods['delete_time'] != 0) {
            $res['error'] = '商品不存在';
            return $res;
        }

        if ($goods['status'] != 1) {
            $res['error'] = '商品已下架';
            return $res;
        }

        // $skuMap = [
        //     ['goods_id', '=', $id],
        //     // ['status', '=', 1],
        //     ['delete_time', '=', 0],
        // ];
        // $sku = MallGoodsSkuModel::where($skuMap)->select();

        // $goods['skus'] = $sku;

        $data = self::goodsFilter($goods);

        $res['data'] = $data;
        return $res;
    }

    public static function goodsFilter(MallGoodsModel $item)
    {
        $skus = $item->skus->map(function ($sku) {
            return self::skuFilter($sku);
        });

        return [
            'id'               => $item->id,
            // 'create_time'    => $item->create_time,
            'update_time'      => $item->update_time,
            // 'delete_time'    => $item->delete_time,
            'status'           => $item->status,
            // 'list_order'     => $item->list_order,
            'title'            => $item->title,
            'subtitle'         => $item->subtitle,
            'thumbnail'        => $item->thumbnail,
            'thumbnail_url'    => $item->thumbnail_url,
            'keywords'         => $item->keywords,
            // 'description'    => $item->description,
            'video'            => $item->video,
            'thumbnails'       => $item->thumbnails,
            'thumbnails_url'   => $item->thumbnails_url,
            'category_id'      => $item->category_id,
            'brand_id'         => $item->brand_id,
            'price_min'        => $item->price_min,
            'price_max'        => $item->price_max,
            'price_market_min' => $item->price_market_min,
            'price_market_max' => $item->price_market_max,
            // 'quantity'       => $item->quantity,
            'is_top'           => $item->is_top,
            'recommended'      => $item->recommended,
            'is_new'           => $item->is_new,
            'is_hot'           => $item->is_hot,
            'view_count'       => $item->view_count,
            'favorite_count'   => $item->favorite_count,
            'like_count'       => $item->like_count,
            'sold_count'       => $item->sold_count,
            // 'on_list'        => $item->on_list,
            'type'             => $item->type,
            'content'          => $item->content,
            'more'             => $item->more,
            'skus'             => $skus,
        ];
    }

    public static function skuFilter(MallGoodsSkuModel $sku)
    {
        return [
            'id'            => $sku->id,
            // 'create_time'   => $sku->create_time,
            // 'update_time'   => $sku->update_time,
            // 'delete_time'   => $sku->delete_time,
            'status'        => $sku->status,
            'list_order'    => $sku->list_order,
            'goods_id'      => $sku->goods_id,
            'title'         => $sku->title,
            'subtitle'      => $sku->subtitle,
            'thumbnail'     => $sku->thumbnail,
            'thumbnail_url' => $sku->thumbnail_url,
            'keywords'      => $sku->keywords,
            'description'   => $sku->description,
            'price_market'  => $sku->price_market,
            'price'         => $sku->price,
            // 'sn'            => $sku->sn,
            // 'barcode'       => $sku->barcode,
            'stock'         => $sku->stock,
            // 'stock_freezed' => $sku->stock_freezed,
            // 'sold_count'    => $sku->sold_count,
            'shipfee'       => $sku->shipfee,
            // 'weight'        => $sku->weight,
            // 'more'          => $sku->more,
        ];
    }
}
