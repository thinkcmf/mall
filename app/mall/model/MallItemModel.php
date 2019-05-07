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
namespace app\mall\model;

use think\db\Query;
use think\Model;

class MallItemModel extends Model
{
    // 是否需要自动写入时间戳 如果设置为字符串 则表示时间字段的类型
    protected $autoWriteTimestamp = true;

    protected $type = [
        'more' => 'array',
    ];

    /**
     * 关联分类表
     * @return \think\model\relation\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('MallCategoryModel', 'category_id');
    }

    /**
     * 关联模型表
     * @return \think\model\relation\BelongsTo
     */
    public function  model()
    {
        return $this->belongsTo('MallModelModel', 'model_id');
    }

    /**
     * content 自动转化
     * @param $value
     * @return string
     */
    public function getContentAttr($value)
    {
        return cmf_replace_content_file_url(htmlspecialchars_decode($value));
    }

    /**
     * content 自动转化
     * @param $value
     * @return string
     */
    public function setContentAttr($value)
    {
        return htmlspecialchars(cmf_replace_content_file_url(htmlspecialchars_decode($value), true));
    }

    /**
     * 商品列表
     * @param int $goodsId 商品id
     * @param int $where 查询条件
     * @return string
     */
    public function adminMallItemList($goodsId = 0, $where = '')
    {
        $where = function (Query $query) use ($goodsId) {
            if (!empty($goodsId)) {
                $query->where('id', $goodsId);
            }
        };

        $goodsList = $this->order("list_order Asc")->where('status', 1)->where($where)->select()->toArray();

        $goodsList = array_map(function ($value) use ($goodsId) {

            $str                 = '<a href="' . url("bindAttr", ["id" => $value['id']]) . '">绑定属性</a> | <a href="' . url("bindBrand", ["id" => $value['id']]) . '">绑定品牌</a> | ';
            $str                 .= $value['status'] == '启用' ? '<a class="js-ajax-dialog-btn" data-msg="确定要禁用吗？" href="' . url("setStatusOff", ["id" => $value['id']]) . '">禁用</a>' : '<a class="js-ajax-dialog-btn" data-msg="确定要启用吗？" href="' . url("setStatusOn", ["id" => $value['id']]) . '">启用</a>';
            $str                 .= ' | <a href="' . url("add", ["parent" => $value['id']]) . '">添加子分类</a> | <a href="' . url("edit", ["id" => $value['id']]) . '">编辑</a>';
            $value['str_action'] = $str . ' | <a class="js-ajax-dialog-btn" data-msg="确定要删除？" href="' . url("del", ["id" => $value['id']]) . '">删除</a>';
            return $value;
        }, $goodsList);

        return $goodsList;
    }

}