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
return [
    'List/index' => [
        'name'   => '商城应用-商品分类列表',
        'vars'   => [
            'id' => [
                'pattern' => '\d+',
                'require' => true
            ]
        ],
        'simple' => true
    ],
    'Item/index' => [
        'name'   => '商城应用-产品页',
        'vars'   => [
            'id'  => [
                'pattern' => '\d+',
                'require' => true
            ],
        ],
        'simple' => true
    ],
];