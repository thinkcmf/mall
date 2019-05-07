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
namespace app\mall\service;

use app\mall\model\MallBrandModel;
use app\mall\model\MallCategoryModel;
use app\mall\model\MallItemModel;
use think\Db;
use think\db\Query;

class ApiService
{
    /**
     * 功能:商品列表,支持分页;<br>
     * @param array $param 查询参数<pre>
     *                     array(
     *                     'where'=>'',
     *                     'limit'=>'',
     *                     'order'=>'',
     *                     'page'=>'',
     *                     )
     *                     字段说明:
     *                     limit:数据条数,默认值为10,可以指定从第几条开始,如3,8(表示共调用8条,从第3条开始)
     *                     order:排序方式,如按posts表里的published_time字段倒序排列：post.published_time desc
     *                     where:查询条件,字符串形式,和sql语句一样,请在事先做好安全过滤,最好使用第二个参数$where的数组形式进行过滤,此方法查询时关联多个表,所以最好指定一下表名,以防字段冲突,查询条件(只支持数组),格式和thinkPHP的where方法一样,此方法查询时关联多个表,所以最好指定一下表名,以防字段冲突;
     *                     </pre>
     * @return array 包括分页的商品列表<pre>
     *                     格式:
     *                     array(
     *                     "items"=>array(),//商品列表,array
     *                     "page"=>"",//生成的分页html,不分页则没有此项
     *                     "total"=>100, //符合条件的商品总数,不分页则没有此项
     *                     "total_pages"=>5 // 总页数,不分页则没有此项
     *                     )</pre>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function items($param)
    {
        $where       = [
            'status' => 1,
        ];
        $paramWhere  = empty($param['where']) ? '' : $param['where'];
        $limit       = empty($param['limit']) ? 10 : $param['limit'];
        $order       = empty($param['order']) ? 'id ASC' : $param['order'];
        $page        = isset($param['page']) ? $param['page'] : false;
        $categoryIds = empty($param['category_ids']) ? '' : $param['category_ids'];

        $return        = [];
        $MallItemModel = new MallItemModel();

        $items = $MallItemModel
            ->where($where)
            ->where($paramWhere)
            ->where(function (Query $query) use ($categoryIds) {
                if (!empty($categoryIds)) {
                    $query->where('category_id', 'in', $categoryIds);
                }
            })
            ->order($order);

        if (empty($page)) {
            $return['items'] = $items
                ->limit($limit)
                ->select();
        } else {
            if (is_array($page)) {
                if (empty($page['list_rows'])) {
                    $page['list_rows'] = 10;
                }
                $items = $items->paginate($page);
            } else {
                $items = $items->paginate(intval($page));
            }
            $return['items']       = $items;
            $return['page']        = $items->render();
            $return['total']       = $items->total();
            $return['total_pages'] = $items->lastPage();

        }
        return $return;
    }

    /**
     *
     * @param array $param
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function brands($param = [])
    {
        $where      = [
            'status' => 1,
        ];
        $paramWhere = empty($param['where']) ? '' : $param['where'];
        $limit      = empty($param['limit']) ? 10 : $param['limit'];
        $order      = empty($param['order']) ? 'id ASC' : $param['order'];
        $page       = isset($param['page']) ? $param['page'] : false;

        $return         = [];
        $mallBrandModel = new MallBrandModel();

        $brands = $mallBrandModel
            ->where($where)
            ->where($paramWhere)
            ->order($order);
        if (empty($page)) {
            $return['items'] = $brands
                ->limit($limit)
                ->select();
        } else {
            if (is_array($page)) {
                if (empty($page['list_rows'])) {
                    $page['list_rows'] = 10;
                }
                $brands = $brands->paginate($page);
            } else {
                $brands = $brands->paginate(intval($page));
            }
            $return['items']       = $brands;
            $return['page']        = $brands->render();
            $return['total']       = $brands->total();
            $return['total_pages'] = $brands->lastPage();
        }
        return $return;
    }

    /**
     * 商品分类查询
     * @param array $param 查询参数
     *                     array(
     *                     'where'=>'',
     *                     'order'=>'',
     *                     )
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function categories($param)
    {
        $paramWhere        = empty($param['where']) ? '' : $param['where'];
        $order             = empty($param['order']) ? '' : $param['order'];
        $mallCategoryModel = new MallCategoryModel();
        $allCategories     = $mallCategoryModel
            ->field('id,parent_id,name,thumbnail,path')
            ->where('status', 1)
            ->where('delete_time', 0)
            ->where($paramWhere)
            ->order($order)
            ->select()
            ->toArray();
        $result            = [];
        foreach ($allCategories as $key => $item) {
            if ($item['parent_id'] == 0) {
                $result[$item['id']] = [
                    'id'        => $item['id'],
                    'parent_id' => $item['parent_id'],
                    'name'      => $item['name'],
                    'thumbnail' => $item['thumbnail'],
                    'path'      => $item['path'],
                    'children'  => []
                ];
            } else {
                $result[$item['parent_id']]['children'][] = $item;
            }
        }
        return $result;
    }
}