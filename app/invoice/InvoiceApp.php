<?php

namespace app\invoice;

class InvoiceApp
{

    // 应用安装
    public function install(): bool
    {
        return true; //安装成功返回true，失败false
    }

    // 应用卸载
    public function uninstall(): bool
    {
        return true; //卸载成功返回true，失败false
    }
}
