<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <catman@thinkcmf.com>
// +----------------------------------------------------------------------
namespace app\mall\controller;

use app\mall\model\MallItemModel;
use cmf\controller\HomeBaseController;

class CategoryController extends HomeBaseController
{
    public function index()
    {
        return $this->fetch('/categories');
    }

}
