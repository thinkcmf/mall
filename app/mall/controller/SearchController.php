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

use app\mall\model\MallItemModel;
use cmf\controller\HomeBaseController;

class SearchController extends HomeBaseController
{
    public function index()
    {
        $keyword = $this->request->param('keyword');
        if (empty($keyword)) {

            $this->error('请输入关键字');

        } else {
            $mallItemModel = new MallItemModel();

            $items = $mallItemModel->where('title', 'like', "%{$keyword}%")->paginate();
			$this->assign('page',$items->render());
            $this->assign('items', $items);
        }

        return $this->fetch('/search');
    }

}
