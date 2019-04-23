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
namespace app\mall\taglib;

use think\template\TagLib;

class Mall extends TagLib
{
	/**
	 * 定义标签列表
	 */
	protected $tags = [
		// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
		'items'      => ['attr' => 'field,where,limit,order,page,relation,returnVarName,pageVarName,categoryIds', 'close' => 1],//非必须属性item
		'brands'     => ['attr' => 'field,where,limit,order,page,relation,returnVarName,pageVarName,categoryIds', 'close' => 1],//非必须属性item
		'categories' => ['attr' => 'where,order', 'close' => 1],//非必须属性item
	];

	/**
	 * 商品列表标签
	 * @param $tag
	 * @param $content
	 * @return string
	 */
	public function tagItems($tag, $content)
	{
		$item          = empty($tag['item']) ? 'vo' : $tag['item'];//循环变量名
		$field         = empty($tag['field']) ? '' : $tag['field'];
		$order         = empty($tag['order']) ? '' : $tag['order'];
		$relation      = empty($tag['relation']) ? '' : $tag['relation'];
		$pageVarName   = empty($tag['pageVarName']) ? '__PAGE_VAR_NAME__' : $tag['pageVarName'];
		$returnVarName = empty($tag['returnVarName']) ? 'items_data' : $tag['returnVarName'];

		$where = '""';
		if (!empty($tag['where']) && strpos($tag['where'], '$') === 0) {
			$where = $tag['where'];
		}

		$limit = "''";
		if (!empty($tag['limit'])) {
			if (strpos($tag['limit'], '$') === 0) {
				$limit = $tag['limit'];
				$this->autoBuildVar($limit);
			} else {
				$limit = "'{$tag['limit']}'";
			}
		}

		$page = "''";
		if (!empty($tag['page'])) {
			if (strpos($tag['page'], '$') === 0) {
				$page = $tag['page'];
			} else {
				$page = intval($tag['page']);
				$page = "'{$page}'";
			}
		}

		$categoryIds = "''";
		if (!empty($tag['categoryIds'])) {
			if (strpos($tag['categoryIds'], '$') === 0) {
				$categoryIds = $tag['categoryIds'];
				$this->autoBuildVar($categoryIds);
			} else {
				$categoryIds = "'{$tag['categoryIds']}'";
			}
		}

		$parse = <<<parse
<?php
\${$returnVarName} = \app\mall\service\ApiService::items([
    'field'   => '{$field}',
    'where'   => {$where},
    'limit'   => {$limit},
    'order'   => '{$order}',
    'page'    => $page,
    'relation'=> '{$relation}',
    'category_ids'=>{$categoryIds}
]);

\${$pageVarName} = isset(\${$returnVarName}['page'])?\${$returnVarName}['page']:'';

 ?>
<volist name="{$returnVarName}.items" id="{$item}">
{$content}
</volist>
parse;
		return $parse;
	}

	/**
	 * 品牌列表标签
	 * @param $tag
	 * @param $content
	 * @return string
	 */
	public function tagBrands($tag, $content)
	{
		$item          = empty($tag['item']) ? 'vo' : $tag['item'];//循环变量名
		$field         = empty($tag['field']) ? '' : $tag['field'];
		$order         = empty($tag['order']) ? '' : $tag['order'];
		$relation      = empty($tag['relation']) ? '' : $tag['relation'];
		$pageVarName   = empty($tag['pageVarName']) ? '__PAGE_VAR_NAME__' : $tag['pageVarName'];
		$returnVarName = empty($tag['returnVarName']) ? 'brands_data' : $tag['returnVarName'];

		$where = '""';
		if (!empty($tag['where']) && strpos($tag['where'], '$') === 0) {
			$where = $tag['where'];
		}

		$limit = "''";
		if (!empty($tag['limit'])) {
			if (strpos($tag['limit'], '$') === 0) {
				$limit = $tag['limit'];
				$this->autoBuildVar($limit);
			} else {
				$limit = "'{$tag['limit']}'";
			}
		}

		$page = "''";
		if (!empty($tag['page'])) {
			if (strpos($tag['page'], '$') === 0) {
				$page = $tag['page'];
			} else {
				$page = intval($tag['page']);
				$page = "'{$page}'";
			}
		}
		$parse = <<<parse
<?php
\${$returnVarName} = \app\mall\service\ApiService::brands([
    'field'   => '{$field}',
    'where'   => {$where},
    'limit'   => {$limit},
    'order'   => '{$order}',
    'page'    => $page,
    'relation'=> '{$relation}'
]);

\${$pageVarName} = isset(\${$returnVarName}['page'])?\${$returnVarName}['page']:'';

 ?>
<volist name="{$returnVarName}.items" id="{$item}">
{$content}
</volist>
parse;
		return $parse;
	}
	/**
	 * 商品分类标签
	 * @param $tag
	 * @param $content
	 * @return string
	 */
	public function tagCategories($tag, $content)
	{
		$item          = empty($tag['item']) ? 'vo' : $tag['item'];//循环变量名
		$order         = empty($tag['order']) ? '' : $tag['order'];
		$returnVarName = 'mall_categories_data';
		$where         = '""';
		if (!empty($tag['where']) && strpos($tag['where'], '$') === 0) {
			$where = $tag['where'];
		}

		$parse = <<<parse
<?php
\${$returnVarName} = \app\mall\service\ApiService::categories([
    'where'   => {$where},
    'order'   => '{$order}',
]);

 ?>
<volist name="{$returnVarName}" id="{$item}">
{$content}
</volist>
parse;
		return $parse;
	}
}