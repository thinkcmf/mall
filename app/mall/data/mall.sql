# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.28)
# Database: devel_cmfmall_db_design
# Generation Time: 2020-01-06 18:15:32 +0000
# ************************************************************



# Dump of table cmf_cart
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cmf_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1=激活,0=冻结',
  `list_order` float unsigned NOT NULL DEFAULT '1000' COMMENT '排序',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `goods_table` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '物件表(不带前缀)',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `sku_table` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品规格表名',
  `sku_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'SKUid',
  `quantity` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '数量',
  `selected` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '选中:1=选中,0=未选中',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购物车表';



# Dump of table cmf_express
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cmf_express` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1=激活,0=冻结',
  `list_order` float unsigned NOT NULL DEFAULT '1000' COMMENT '排序',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '别名',
  `thumbnail` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '缩略图',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '介绍描述(前台展示)',
  `code` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '物流公司代码',
  `remark` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注说明(后台)',
  `type` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '物流类型',
  `base_weight` decimal(10,3) unsigned NOT NULL DEFAULT '1.000' COMMENT '默认首重(KG)',
  `base_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '默认首重运费',
  `next_weight` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '默认续重(KG)',
  `next_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '默认续重运费',
  `more` longtext COLLATE utf8mb4_unicode_ci COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='物流模板表';



# Dump of table cmf_express_fee
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cmf_express_fee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `list_order` float unsigned NOT NULL DEFAULT '1000' COMMENT '排序',
  `express_id` int(10) unsigned NOT NULL COMMENT '物流模板ID',
  `province` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '省份地区名称',
  `province_id` int(10) unsigned NOT NULL COMMENT '省份地区ID',
  `base_weight` decimal(10,3) unsigned NOT NULL DEFAULT '1.000' COMMENT '首重(KG)',
  `base_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '首重运费',
  `next_weight` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '续重(KG)',
  `next_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '续重运费',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='物流资费表';



# Dump of table cmf_mall_brand
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cmf_mall_brand` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1=激活,0=冻结',
  `list_order` float unsigned NOT NULL DEFAULT '1000' COMMENT '排序',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '别名',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'logo',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '介绍描述',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌网址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商城品牌表';



# Dump of table cmf_mall_category
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cmf_mall_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1=激活,0=冻结',
  `list_order` float unsigned NOT NULL DEFAULT '1000' COMMENT '排序',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '别名',
  `thumbnail` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '缩略图',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '介绍描述',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `path` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类层级关系路径',
  `more` longtext COLLATE utf8mb4_unicode_ci COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商城分类表';



# Dump of table cmf_mall_goods
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cmf_mall_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间:0=激活未删除,大于0=删除(回收站)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1=上架,0=下架',
  `list_order` float unsigned NOT NULL DEFAULT '1000' COMMENT '排序',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品标题',
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品副标题',
  `thumbnail` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '缩略图',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '介绍描述',
  `video` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '视频',
  `thumbnails` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '主图(JSON)',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `price_min` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品最低价格',
  `price_max` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品最高价格',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `is_top` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶:1=是,0=否',
  `recommended` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐:1=是,0=否',
  `is_new` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否新品:1=是,0=否',
  `is_hot` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否热卖:1=是,0=否',
  `view_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '查看数',
  `favorite_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数',
  `like_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `sold_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '销售数',
  `on_list` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '列表状态:1=列表可见,0=不可见(不可搜索、列表展示，但可以通过链接访问)',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型:0=常规商品,1=虚拟产品',
  `content` longtext COLLATE utf8mb4_unicode_ci COMMENT '内容',
  `more` longtext COLLATE utf8mb4_unicode_ci COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商城商品表';



# Dump of table cmf_mall_goods_sku
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cmf_mall_goods_sku` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间:0=激活未删除,大于0=删除(回收站)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1=上架,0=下架',
  `list_order` float unsigned NOT NULL DEFAULT '1000' COMMENT '排序',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'SKU标题',
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'SKU副标题',
  `thumbnail` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'SKU缩略图',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '介绍描述',
  `price_market` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场参考价',
  `price` decimal(10,2) unsigned NOT NULL COMMENT '售价',
  `sn` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '货号',
  `barcode` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '条形码',
  `stock` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存',
  `stock_freezed` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '冻结库存',
  `sold_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '售出(累计)',
  `shipfee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单件运费(元)',
  `weight` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '商品重量(KG)',
  -- `on_sale` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '上架状态:1=上架,0=下架',
  -- `promotion` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '促销:none/空=无促销',
  `more` longtext COLLATE utf8mb4_unicode_ci COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商城商品SKU表';



# Dump of table cmf_payment
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cmf_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1=激活,0=冻结',
  `list_order` float unsigned NOT NULL DEFAULT '1000' COMMENT '排序',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '别名',
  `thumbnail` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '缩略图',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '介绍描述(前台展示)',
  `remark` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注说明(后台)',
  `type` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '支付类型',
  `api_channel` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'API渠道',
  `api_params` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'API参数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='支付渠道表';

# 2020-05-07 14:13 增加price_market_min,price_market_max字段
ALTER TABLE `cmf_mall_goods` ADD `price_market_min` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '最小市场价' AFTER `price_max`;
ALTER TABLE `cmf_mall_goods` ADD `price_market_max` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '最大市场价' AFTER `price_market_min`;
