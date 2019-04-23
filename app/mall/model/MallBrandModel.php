<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 <449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\mall\model;

use think\Model;

class MallBrandModel extends Model
{
    /**
     * 开启时间字段自动写入
     */
    protected $autoWriteTimestamp = true;

    /**
     * 新建品牌
     * @param $data
     * @return false|int
     */
    public function addBrand($data)
    {
        $result = $this->allowField(true)->isUpdate(false)->data($data, true)->save();
        return $result;
    }
    /**
     * 编辑品牌
     * @param $data
     * @return false|int
     */
    public function editBrand($data)
    {
        $result = $this->allowField(true)->isUpdate(true)->data($data, true)->save();
        return $result;
    }
}
