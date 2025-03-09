<?php
declare (strict_types = 1);

namespace app\brand\controller;

use cmf\controller\AdminBaseController;
use app\brand\service\BrandService;

/**
 * Class AdminBrandInfoController
 * @adminMenuRoot(
 *     'name'   =>'品牌管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'',
 *     'remark' =>'品牌管理'
 * )
 */
class AdminBrandInfoController extends AdminBaseController
{

    /**
     * 品牌分页列
     * @adminMenu(
     *     'name'   => '品牌列表',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌列',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $validateResult = $this->validate($this->request->param(),'BrandInfo.index');
        if($validateResult !== true){
            $this->error($validateResult);
        }
        $service = new BrandService();
        $serviceResult = $service->page();
        $this->assign('data',$serviceResult);
        return $this->fetch();
    }

    /**
     * 品牌保存
     * @adminMenu(
     *     'name'   => '品牌保存',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌保存',
     *     'param'  => ''
     * )
     */
    public function create(): void
    {
        $validateResult = $this->validate($this->request->param(),'BrandInfo.create');
        if($validateResult !== true){
            $this->error($validateResult);
        }
        $service = new BrandService();
        $serviceResult = $service->create($this->request->param());
        if($serviceResult){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    /**
     * 品牌添加页面
     * @adminMenu(
     *     'name'   => '品牌添加页面',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌添加页面',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 品牌修改
     * @adminMenu(
     *     'name'   => '品牌修改',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌修改',
     *     'param'  => ''
     * )
     */
    public function update(): void
    {
        $validateResult = $this->validate($this->request->param(),'BrandInfo.delete');
        if($validateResult !== true){
            $this->error($validateResult);
        }
        $service = new BrandService();
        $serviceResult = $service->update($this->request->param());
        if($serviceResult){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    /**
     * 品牌编辑页面
     * @adminMenu(
     *     'name'   => '品牌编辑页面',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌编辑页面',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $validateResult = $this->validate($this->request->param(),'BrandInfo.index');
        if($validateResult !== true){
            $this->error($validateResult);
        }
        $service = new BrandService();
        $serviceResult = $service->read($this->request->param());
        $this->assign('data',$serviceResult);
        return $this->fetch();
    }

    /**
     * 品牌详情页面
     * @adminMenu(
     *     'name'   => '品牌详情页面',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌详情页面',
     *     'param'  => ''
     * )
     */
    public function read()
    {
        $validateResult = $this->validate($this->request->param(),'BrandInfo.index');
        if($validateResult !== true){
            $this->error($validateResult);
        }
        $service = new BrandService();
        $serviceResult = $service->read($this->request->param());
        $this->assign('data',$serviceResult);
        return $this->fetch();
    }

    /**
     * 品牌删除
     * @adminMenu(
     *     'name'   => '品牌删除',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '品牌删除',
     *     'param'  => ''
     * )
     */
    public function delete(): void
    {
        $validateResult = $this->validate($this->request->param(),'BrandInfo.delete');
        if($validateResult !== true){
            $this->error($validateResult);
        }
        $service = new BrandService();
        $serviceResult = $service->delete($this->request->param());
        if($serviceResult){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function status(): void
    {
        $param          = $this->request->param();
        $brand = new BrandService();
        $ids = $this->request->param('ids/a');
        $this->success($brand->status($ids,$param));
    }

}
