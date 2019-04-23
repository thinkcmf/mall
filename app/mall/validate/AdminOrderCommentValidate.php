<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\mall\validate;

use FontLib\Table\Type\name;
use think\Validate;

class AdminOrderCommentValidate extends Validate
{
    protected $rule = [
        'parent_id' => 'require|number|checkCommentIdExist',
        'content' => 'require|max:500',
    ];
    protected $message = [
        'parent_id.require'=>'评价ID不能为空',
        'content.require' => '评论内容不能为空!',
    ];

    protected $scene = [
        'edit'=>['content']
    ];

    protected function checkCommentIdExist($parent_id){
        if(isset($parent_id)){
            $data = Db('OrderComment')->find($parent_id);
            if(empty($data)){
                return '评价不存在';
            }
        }
        return true;
    }
}