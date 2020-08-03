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
namespace app\common\base;

use cmf\controller\AdminBaseController as CmfAdminBaseController;

class AdminBaseController extends CmfAdminBaseController
{
    public $navTabs = [];
    public $navTabsActive;

    protected function initialize()
    {
        parent::initialize();

        // 顶部菜单tabs内容
        $this->assign('nav_tabs', $this->navTabs);
        // 当前操作赋值到模板，用于页面顶部菜单自动激活
        $active = $this->navTabsActive ?? $this->request->action();
        $this->assign('active', $active);
    }

    /**
     * 加载生成器的模板输出
     * @access protected
     * @param  string $template 模板文件名
     * @param  array  $vars     模板输出变量
     * @param  array  $config   模板参数
     * @return mixed
     */
    protected function fetchPage($template = '', $folder = '', $vars = [], $config = [])
    {
        if (empty($folder) && $folder != 'custom') {
            $template = $template ?: 'custom';
            $template = 'common@/page/' . $template;
        } elseif (!empty($template)) {
            $template = $folder . $template;
        }
        return $this->fetch($template, $vars, $config);
    }

    protected function htmlPage($template = '', $folder = '', $vars = [], $config = [])
    {
        return $this->fetchPage($template, $folder, $vars, $config)->getContent();
    }

    /**
     * 获取模块html
     */
    protected function fetchBlock($template = '', $folder = '', $vars = [], $config = [])
    {
        if (empty($folder) || $folder == 'custom') {
            $template = 'common@/block/' . $template;
        } elseif (!empty($template)) {
            $template = $folder . $template;
        }
        return $this->fetch($template, $vars, $config);
    }

    protected function htmlBlock($template = '', $folder = '', $vars = [], $config = [])
    {
        return $this->fetchBlock($template, $folder, $vars, $config)->getContent();
    }
}
