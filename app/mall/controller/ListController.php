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

use app\mall\model\MallCategoryModel;
use cmf\controller\HomeBaseController;

class ListController extends HomeBaseController
{
    /**
     * 商品列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $id            = $this->request->param('id', 0, 'intval');
        $categoryModel = new MallCategoryModel();

        $category = $categoryModel->where('id', $id)->where('status', 1)->find();

        $this->assign('category', $category);

        return $this->fetch('/list');
    }

    public function recommended()
    {
        return $this->fetch('/recommended');
    }

}
