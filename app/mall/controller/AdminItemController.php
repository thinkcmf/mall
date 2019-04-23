<?php

namespace app\mall\controller;

use app\mall\model\MallAttrModel;
use app\mall\model\MallAttrValueModel;
use app\mall\model\MallBrandModel;
use app\mall\model\MallCategoryAttrModel;
use app\mall\model\MallCategoryModel;
use app\mall\model\MallItemAttrModel;
use app\mall\model\MallItemModel;
use app\mall\model\MallItemSkuModel;
use app\mall\model\MallModelModel;
use cmf\controller\AdminBaseController;
use think\db\Query;

class AdminItemController extends AdminBaseController
{

    /**
     * 商品管理
     * @adminMenu(
     *     'name'   => '商品管理',
     *     'parent' => 'mall/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $param = $this->request->param();

        $where = function (Query $query) use ($param) {
            if (!empty($param['keyword'])) {
                $query->where('title', 'like', "%{$param['keyword']}%");
            }

            if (!empty($param['category'])) {
                $query->where('category_id', intval($param['category']));
            }
        };

        $mallItemModel = new MallItemModel();
        $items         = $mallItemModel->where($where)->order('create_time DESC')->paginate();

        $items->appends($param);

        $mallCategoryModel = new MallCategoryModel();
        $categoriesTree    = $mallCategoryModel->mallCategoryTree();

        $this->assign('categories_tree', $categoriesTree);

        $this->assign('items', $items);
        $this->assign('page', $items->render());

        return $this->fetch();
    }

    /**
     * 添加商品
     * @adminMenu(
     *     'name'   => '添加商品',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $data = $this->request->param();

        $mallCategoryModel = new MallCategoryModel();
        $categoriesTree    = $mallCategoryModel->mallCategoryTree();

        $this->assign('categories_tree', $categoriesTree);

        $mallBrandModel = new MallBrandModel();

        $brands = $mallBrandModel->select();

        $this->assign('brands', $brands);
        return $this->fetch();
    }

    /**
     * 添加商品提交保存
     * @adminMenu(
     *     'name'   => '添加商品提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品提交保存',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'AdminItem');

        if ($result !== true) {
            $this->error($result);
        }


        $mallItemModel = new MallItemModel();

        $mallItemModel->allowField(true)->save($data);

        $this->success('添加成功！', url('AdminItem/edit', ['id' => $mallItemModel->id]));

    }

    /**
     * 编辑商品
     * @adminMenu(
     *     'name'   => '编辑商品',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        $item = MallItemModel::get($id);

        if (empty($item)) {
            $this->error('宝贝不存在！');
        }

        $category = MallCategoryModel::get($item['category_id']);

        $mallBrandModel = new MallBrandModel();

        $brands = $mallBrandModel->select();


        $this->assign('brands', $brands);

        $this->assign('category', $category);
        $this->assign('item', $item);
        return $this->fetch();
    }

    /**
     * 编辑商品规格
     * @adminMenu(
     *     'name'   => '编辑商品规格',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品规格',
     *     'param'  => ''
     * )
     */
    public function skuList()
    {
        $id   = $this->request->param('id', 0, 'intval');
        $item = MallItemModel::get($id);

        if (empty($item)) {
            $this->error('宝贝不存在！');
        }

        $modelId = $item['model_id'];

        if (empty($modelId)) {
            $attrs   = [];
            $skuList = [];
        } else {
            $attrModel = new MallAttrModel();
            $attrs     = $attrModel->where('model_id', $modelId)->order('list_order ASC')->select();
            if (count($attrs)) {
                $attrs->load('values');
            }
            $itemSkuModel = new MallItemSkuModel();
            $skuList      = $itemSkuModel->where('item_id', $id)->where('delete_time', 0)->select();
        }

        $modelModel = new MallModelModel();
        $models     = $modelModel->select();
        $category   = MallCategoryModel::get($item['category_id']);
        $this->assign('models', $models);
        $this->assign('model_id', $modelId);
        $this->assign('category', $category);
        $this->assign('attrs', $attrs);
        $this->assign('item', $item);
        $this->assign('sku_list', $skuList);
        return $this->fetch();
    }

    /**
     * 保存商品规格
     * @adminMenu(
     *     'name'   => '保存商品规格',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '保存商品规格',
     *     'param'  => ''
     * )
     */
    public function saveSku()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'AdminSku');

        if ($result !== true) {
            $this->error($result);
        }

        $id = $this->request->param('id', 0, 'intval');

        $attrs = $this->request->param('attrs/a');

        ksort($attrs);

        $attrValueModel = new MallAttrValueModel();
        $attrModel      = new MallAttrModel();

        $attrNamesArr      = $attrModel->where('id', 'in', array_keys($attrs))->column('name', 'id');
        $attrValuesTextArr = $attrValueModel->where('id', 'in', array_values($attrs))->column('value', 'id');

        $keyArr    = [];
        $specInfos = [];

        $newAttrs = [];

        foreach ($attrs as $attrId => $attrValueId) {
            if (!empty($attrValueId)) {
                $newAttrs[$attrId] = $attrValueId;
                array_push($keyArr, "$attrId:$attrValueId");
                array_push($specInfos, "{$attrNamesArr[$attrId]}:{$attrValuesTextArr[$attrValueId]}");
            }
        }

        $attrs = $newAttrs;

        $itemModel = new MallItemModel();

        $itemId = intval($data['item_id']);

        $item = $itemModel->where('id', $itemId)->find();

        if (empty($item)) {
            $this->error('商品不存在！');
        }

        $data['model_id']    = $item['model_id'];
        $data['category_id'] = $item['category_id'];
        $data['brand_id']    = $item['brand_id'];
        $data['status']      = 0;
        $data['title']       = $item['title'];
        $data['key']         = implode(';', $keyArr);
        $data['spec_info']   = implode(';', $specInfos);


        $itemSkuModel  = new MallItemSkuModel();
        $itemAttrModel = new MallItemAttrModel();
        if (empty($id)) {
            $itemSkuModel->allowField(true)->save($data);

            $itemAttrData = [];

            foreach ($attrs as $attrId => $attrValueId) {
                array_push($itemAttrData, [
                    'category_id'   => $item['category_id'],
                    'brand_id'      => $item['brand_id'],
                    'model_id'      => $item['model_id'],
                    'item_id'       => $itemId,
                    'sku_id'        => $itemSkuModel->id,
                    'attr_id'       => $attrId,
                    'attr_value_id' => $attrValueId
                ]);
            }

            $itemAttrModel->saveAll($itemAttrData);

        } else {
            $itemSkuModel->allowField(true)->isUpdate(true)->save($data, ['id' => $id]);

            $itemAttrData = [];
            foreach ($attrs as $attrId => $attrValueId) {
                array_push($itemAttrData, [
                    'category_id'   => $item['category_id'],
                    'brand_id'      => $item['brand_id'],
                    'model_id'      => $item['model_id'],
                    'item_id'       => $itemId,
                    'sku_id'        => $itemSkuModel->id,
                    'attr_id'       => $attrId,
                    'attr_value_id' => $attrValueId
                ]);
            }

            $itemAttrs = $itemAttrModel->where('sku_id', $itemSkuModel->id)->select();

            $needDeleteItemAttrIds = [];
            $needUpdateItemAttrIds = [];

            foreach ($itemAttrs as $itemAttr) {
                if (isset($attrs[$itemAttr['attr_id']])) {
                    $itemAttrModel->where('id', $itemAttr['id'])->update(['attr_value_id' => $attrs[$itemAttr['attr_id']]]);
                    array_push($needUpdateItemAttrIds, $itemAttr['id']);
                } else {
                    array_push($needDeleteItemAttrIds, $itemAttr['id']);
                }
            }

            $itemAttrData = [];

            foreach ($attrs as $attrId => $attrValueId) {
                if (!in_array($attrId, $needUpdateItemAttrIds)) {
                    array_push($itemAttrData, [
                        'category_id'   => $item['category_id'],
                        'brand_id'      => $item['brand_id'],
                        'model_id'      => $item['model_id'],
                        'item_id'       => $itemId,
                        'sku_id'        => $itemSkuModel->id,
                        'attr_id'       => $attrId,
                        'attr_value_id' => $attrValueId
                    ]);
                }
            }

            if (!empty($itemAttrData)) {
                $itemAttrModel->saveAll($itemAttrData);
            }

        }


        $this->success('保存成功', null, ['id' => $itemSkuModel->id]);
    }

    /**
     * 删除商品规格
     * @adminMenu(
     *     'name'   => '删除商品规格',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除商品规格',
     *     'param'  => ''
     * )
     */
    public function deleteSku()
    {
        $id           = $this->request->param('id', 0, 'intval');
        $itemSkuModel = new MallItemSkuModel();

        $itemSkuModel->where('id', $id)->update(['delete_time' => time()]);

        $this->success('删除成功！');
    }

    /**
     * 商品规格上下架
     * @adminMenu(
     *     'name'   => '商品规格上下架',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品规格上下架',
     *     'param'  => ''
     * )
     */
    public function skuStatus()
    {
        $id           = $this->request->param('id', 0, 'intval');
        $status       = $this->request->param('status', 0, 'intval');
        $itemSkuModel = new MallItemSkuModel();

        $status = empty($status) ? 0 : 1;

        $itemSkuModel->where('id', $id)->update(['status' => $status]);

        $this->success('操作成功！');
    }

    /**
     * 更改商品模型
     * @adminMenu(
     *     'name'   => '更改商品模型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '更改商品模型',
     *     'param'  => ''
     * )
     */
    public function changeModel()
    {
        $itemSkuModel = new MallItemSkuModel();
        $id           = $this->request->param('id', 0, 'intval');
        $modelId      = $this->request->param('model_id', 0, 'intval');
        $itemModel    = new MallItemModel();
        $item         = $itemModel->where('id', $id)->find();

        if ($item['model_id'] != $modelId) {
            $itemSkuModel->where('item_id', $id)->where('delete_time', 0)->update(['delete_time' => time()]);
            $itemModel->where('id', $id)->update(['model_id' => $modelId]);
        }


        $this->success('更换成功！');
    }

    /**
     * 编辑商品提交保存
     * @adminMenu(
     *     'name'   => '编辑商品提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品提交保存',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();
        $id   = $this->request->param('id', 0, 'intval');

        $result = $this->validate($data, 'AdminItem');

        if ($result !== true) {
            $this->error($result);
        }

        $mallItemModel = new MallItemModel();

        $item = $mallItemModel->where('id', $id)->find();

        if (empty($item)) {
            $this->error('商品不存在！');
        }

        $data['more']['photos'] = [];
        if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {

            foreach ($data['photo_urls'] as $key => $url) {
                $photoUrl = cmf_asset_relative_url($url);
                array_push($data['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
            }
        }

        $mallItemModel->allowField(true)->save($data, ['id' => $id]);


        $this->success('保存成功！');

    }

    /**
     * 商品排序
     * @adminMenu(
     *     'name'   => '商品排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $mallItemModel = new MallItemModel();
        parent::listOrders($mallItemModel);
        $this->success("排序更新成功！");
    }

    /**
     * 商品上架下架
     * @adminMenu(
     *     'name'   => '商品上架下架',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品上架下架',
     *     'param'  => ''
     * )
     */
    public function status()
    {
        $data          = $this->request->param();
        $mallItemModel = new MallItemModel();

        if (isset($data['ids']) && !empty($data["sell"])) {
            $ids = $this->request->param('ids/a');
            $mallItemModel->where('id', 'in', $ids)->update(['status' => 1]);
            $this->success("更新成功！");
        }

        if (isset($data['ids']) && empty($data["sell"])) {
            $ids = $this->request->param('ids/a');
            $mallItemModel->where('id', 'in', $ids)->update(['status' => 0]);
            $this->success("更新成功！");
        }
    }

}