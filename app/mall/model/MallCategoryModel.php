<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\mall\model;

use think\db\Query;
use think\Model;
use tree\Tree;

class MallCategoryModel extends Model
{
    /**
     * 自动类型转换 more字段
     */
    protected $type = [
        'more' => 'array',
    ];

    /**
     * 开启时间字段自动写入
     */
    protected $autoWriteTimestamp = true;


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
    public function mallCategoryTree($selectId = 0, $currentCid = 0)
    {
        $categories = $this->order("list_order ASC")
            ->where('delete_time', 0)
            ->where(function (Query $query) use ($currentCid) {
                if (!empty($currentCid)) {
                    $query->where('id', 'neq', $currentCid);
                }
            })
            ->select()->toArray();
        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';
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
    public function  getMallCategories()
    {
        $list       = $this->where('delete_time', 0)->select()->toArray();
        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        foreach ($list as $key => $value) {

            $list[$key]['parent_id_node'] = ($value['parent_id']) ? ' class="child-of-node-' . $value['parent_id'] . '"' : '';
            $list[$key]['style']          = empty($value['parent_id']) ? '' : 'display:none;';

            $list[$key]['status'] = $value['status'] ? '<span class="label label-success">启用</span>' : '<span class="label label-danger">禁用</span>';;
            $list[$key]['str_manage'] = $value['status']  ? '<a class="js-ajax-dialog-btn btn btn-xs btn-warning" data-msg="确定要禁用吗？" href="'
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
    public function addCategory($data)
    {
        $result = true;
        self::startTrans();
        try {
            if (!empty($data['thumbnail'])) {
                $data['thumbnail'] = cmf_asset_relative_url($data['thumbnail']);
            }
            $this->allowField(true)->save($data);
            $id = $this->id;
            if (empty($data['parent_id'])) {

                $this->where('id', $id)->update(['path' => '0-' . $id]);
            } else {
                $parentPath = $this->where('id', intval($data['parent_id']))->value('path');
                $this->where('id', $id)->update(['path' => "$parentPath-$id"]);

            }
            self::commit();
        } catch (\Exception $e) {
            self::rollback();
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
    public function editCategory($data)
    {
        $result = true;

        $id          = intval($data['id']);
        $parentId    = intval($data['parent_id']);
        $oldCategory = $this->where('id', $id)->find();

        if (empty($parentId)) {
            $newPath = '0-' . $id;
        } else {
            $parentPath = $this->where('id', intval($data['parent_id']))->value('path');
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
            $this->isUpdate(true)->allowField(true)->save($data, ['id' => $id]);

            $children = $this->field('id,path')->where('path', 'like', $oldCategory['path'] . "-%")->select();
            if (!$children->isEmpty()) {
                foreach ($children as $child) {
                    $childPath = str_replace($oldCategory['path'] . '-', $newPath . '-', $child['path']);
                    $this->where('id', $child['id'])->update(['path' => $childPath], ['id' => $child['id']]);
                }
            }

        }
        return $result;
    }



}
