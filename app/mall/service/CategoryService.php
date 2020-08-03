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
namespace app\mall\service;

use app\mall\model\MallCategoryModel;
use think\db\Query;
use tree\Tree;

/**
 * 服务提供： 品牌
 *
 */
class CategoryService extends BaseService
{

    /**
     * 获取数据列表
     */
    public static function get($map = [], $orderby = 'list_order', $field = '', $deleted = false)
    {
        if ($deleted) {
            $map[] = ['delete_time', '>', 0];
        } else {
            $map[] = ['delete_time', '=', 0];
        }

        $model = MallCategoryModel::where($map);

        $model = $model->order($orderby);

        if (!empty($field)) {
            $model = $model->field($field);
        }

        return $model->select();
    }

    /**
     * 获取KeyVal列表
     */
    public static function getKv($valField = 'name', $keyField = 'id', $decorat = false)
    {
        $data = self::get()->toArray();
        if ($decorat) {
            $tree          = new Tree();
            $tree->icon    = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
            $tree->nbsp    = '&nbsp;&nbsp;';
            $newCategories = [];
            foreach ($data as $item) {
                array_push($newCategories, $item);
            }
            $tree->init($newCategories);
            // $str     = '{$id}={$spacer}{$name}|';
            $str     = '{$' . $keyField . '}={$spacer}{$' . $valField . '}|';
            $treeStr = $tree->getTree(0, $str);
            $treeArr = array_map(function ($item) {
                if (!empty($item)) {
                    return explode('=', $item);
                }
                return '';
            }, explode('|', $treeStr));
            $treeArr = array_filter($treeArr);
            $return = [];
            foreach ($treeArr as $item) {
                $return[$item[0]] = $item[1];
            }

            return $return;
        }
        return array_column($data, $valField, $keyField);
    }

    /**
     *  设置编辑 分类
     * @param       $input 输入的数据
     * @param array $where where条件
     * @return 默认返回
     */
    public function setMallCate($input, $where = [])
    {
        $data['name'] = $input['title'];
        if (empty($input['image'])) {
            $data['thumbnail'] = $input['image'];
        }
        return $this->updateDate($input, $where);
    }

    /**
     * 生成分类 select树形结构
     * @param int $selectId   需要选中的分类 id
     * @param int $currentCid 需要隐藏的分类 id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function mallCategoryTree($selectId = 0, $currentCid = 0)
    {
        $categoryModel = new MallCategoryModel();
        $categories    = $categoryModel->order("list_order ASC")
            ->where('delete_time', 0)
            ->where(function (Query $query) use ($currentCid) {
                if (!empty($currentCid)) {
                    $query->where('id', 'neq', $currentCid);
                }
            })
            ->select()->toArray();
        $tree          = new Tree();
        $tree->icon    = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp    = '&nbsp;&nbsp;';
        $newCategories = [];
        foreach ($categories as $item) {
            $item['selected'] = $selectId == $item['id'] ? "selected" : "";

            array_push($newCategories, $item);
        }
        $tree->init($newCategories);
        $str     = '<option value=\"{$id}\" {$selected}>{$spacer}{$name}</option>';
        $treeStr = $tree->getTree(0, $str);

        return $treeStr;
    }

    /**
     * 商品分类
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getMallCategories()
    {
        $categoryModel = new MallCategoryModel();
        $list          = $categoryModel->where('delete_time', 0)->select()->toArray();
        $tree          = new Tree();
        $tree->icon    = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp    = '&nbsp;&nbsp;';

        foreach ($list as $key => $value) {

            $list[$key]['parent_id_node'] = ($value['parent_id']) ? ' class="child-of-node-' . $value['parent_id'] . '"' : '';
            $list[$key]['style']          = empty($value['parent_id']) ? '' : 'display:none;';

            $list[$key]['status'] = $value['status'] ? '<span class="label label-success">启用</span>' : '<span class="label label-danger">禁用</span>';;
            $list[$key]['str_manage'] = $value['status'] ? '<a class="js-ajax-dialog-btn btn btn-xs btn-warning" data-msg="确定要禁用吗？" href="'
                . url("AdminCategory/setStatusOff", ["id" => $value['id']]) . '">禁用</a> ' : '<a class="js-ajax-dialog-btn btn btn-xs btn-warning" data-msg="确定要启用吗？" href="'
                . url("AdminCategory/setStatusOn", ["id" => $value['id']]) . '">启用</a> ';
            $list[$key]['str_manage'] .= '<a class="btn btn-xs btn-primary" href="' . url("AdminCategory/add", ["parent" => $value['id']]) . '">添加子分类</a> ';
            $list[$key]['str_manage'] .= '<a class="btn btn-xs btn-primary" href="' . url("AdminCategory/edit", ["id" => $value['id']]) . '">编辑</a> ';
            $list[$key]['str_manage'] .= '<a class="js-ajax-dialog-btn btn btn-xs btn-danger" data-msg="确定要删除？" href="' . url("AdminCategory/delete", ["id" => $value['id']]) . '">删除</a>';
        }

        $tree->init($list);

        $tpl = "<tr id='node-\$id' \$parent_id_node style='\$style'>
            <td style='padding-left:20px;'><input name='list_orders[\$id]' type='text' size='3' value='\$list_order' class='input-order'></td>
            <td>\$id</td>
            <td>\$spacer \$name</td>
            <td>\$status</td>
            <td>\$str_manage</td>
        </tr>";

        $treeStr = $tree->getTree(0, $tpl);

        return $treeStr;
    }

    /**
     * 添加分类
     * @param $data
     * @return bool
     * @throws \think\exception\PDOException
     */
    public static function addCategory($data)
    {
        $categoryModel = new MallCategoryModel();
        $result        = true;
        $categoryModel->startTrans();
        try {
            if (!empty($data['thumbnail'])) {
                $data['thumbnail'] = cmf_asset_relative_url($data['thumbnail']);
            }
            $categoryModel->allowField(true)->save($data);
            $id = $categoryModel->id;
            if (empty($data['parent_id'])) {

                $categoryModel->where('id', $id)->update(['path' => '0-' . $id]);
            } else {
                $parentPath = $categoryModel->where('id', intval($data['parent_id']))->value('path');
                $categoryModel->where('id', $id)->update(['path' => "$parentPath-$id"]);
            }
            $categoryModel->commit();
        } catch (\Exception $e) {
            $categoryModel->rollback();
            $result = false;
        }

        return $result;
    }

    /**
     * 编辑商品分类
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function editCategory($data)
    {
        $categoryModel = new MallCategoryModel();
        $result        = true;

        $id          = intval($data['id']);
        $parentId    = intval($data['parent_id']);
        $oldCategory = $categoryModel->where('id', $id)->find();

        if (empty($parentId)) {
            $newPath = '0-' . $id;
        } else {
            $parentPath = $categoryModel->where('id', intval($data['parent_id']))->value('path');
            if ($parentPath === false) {
                $newPath = false;
            } else {
                $newPath = "$parentPath-$id";
            }
        }

        if (empty($oldCategory) || empty($newPath)) {
            $result = false;
        } else {

            $data['path'] = $newPath;
            if (!empty($data['thumbnail'])) {
                $data['thumbnail'] = cmf_asset_relative_url($data['thumbnail']);
            }
            $categoryModel->isUpdate(true)->allowField(true)->save($data, ['id' => $id]);

            $children = $categoryModel->field('id,path')->where('path', 'like', $oldCategory['path'] . "-%")->select();
            if (!$children->isEmpty()) {
                foreach ($children as $child) {
                    $childPath = str_replace($oldCategory['path'] . '-', $newPath . '-', $child['path']);
                    $categoryModel->where('id', $child['id'])->update(['path' => $childPath], ['id' => $child['id']]);
                }
            }
        }
        return $result;
    }
}
