<?php

namespace app\product;

class ProductApp
{

    // 应用安装
    public function install()
    {
        return true; //安装成功返回true，失败false
    }

    // 应用卸载
    public function uninstall()
    {
        return true; //卸载成功返回true，失败false
    }
}
