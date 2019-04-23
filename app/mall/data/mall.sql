
-- ----------------------------
--  Table structure for `cmf_mall_attr`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_mall_attr`;
CREATE TABLE `cmf_mall_attr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `model_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品模型ID',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '类型;1:自然属性;2:销售属性',
  `has_thumbnail` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否有缩略图，一个商品类型只能有一个属性允许上传缩略图',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `input_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '输入组件类型;1:下拉列表;2:单行文本框;3:多行文本;',
  `searchable` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否可搜索;0:不可以;1:可以;(只有下拉列表可以搜索)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `list_order` float unsigned NOT NULL DEFAULT '10000' COMMENT '排序',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `remark` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `options` text COMMENT '属性可选值，以英文逗号分隔，如果允许上传缩略图，代表此属性为自定义属性，商品可以自由设置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COMMENT='商品品属性表';

-- ----------------------------
--  Table structure for `cmf_mall_attr_value`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_mall_attr_value`;
CREATE TABLE `cmf_mall_attr_value` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性值ID',
  `attr_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '属性ID',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '属性值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品属性值表';

-- ----------------------------
--  Table structure for `cmf_mall_brand`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_mall_brand`;
CREATE TABLE `cmf_mall_brand` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '品牌id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:可用;0:不可用',
  `list_order` float unsigned NOT NULL DEFAULT '10000' COMMENT '排序',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌名称',
  `alias` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌别名',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌图片标识',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌网址',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌介绍',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品品牌表';

-- ----------------------------
--  Table structure for `cmf_mall_category`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_mall_category`;
CREATE TABLE `cmf_mall_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品类型ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:可用;0:不可用',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类描述',
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '一级分类logo',
  `path` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类层级关系路径',
  `more` longtext COLLATE utf8mb4_unicode_ci COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品分类表';

-- ----------------------------
--  Table structure for `cmf_mall_item`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_mall_item`;
CREATE TABLE `cmf_mall_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺id',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类ID',
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品模型ID',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品标题',
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品副标题',
  `item_sn` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品编号',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价格',
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品原价',
  `weight` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品重量，单位g',
  `volume` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '体积;单位ml',
  `length` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '长,单位cm',
  `width` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '宽,单位cm',
  `height` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '高,单位cm',
  `video` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品视频',
  `thumbnail` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '缩略图',
  `list_order` float NOT NULL DEFAULT '0' COMMENT '排序',
  `view_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '查看数',
  `favorite_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数',
  `like_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `is_top` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶;1:置顶;0:不置顶',
  `recommended` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐;1:推荐;0:不推荐',
  `new_arrival` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否新品上市',
  `is_hot` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否为热卖商品',
  `is_virtual` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是虚拟商品',
  `is_self` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否自营',
  `barcode` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品级别的条形码',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `use_platform` tinyint(1) NOT NULL DEFAULT '0' COMMENT '使用平台;0:全部;1:pc;2:手机',
  `content` mediumtext COLLATE utf8mb4_unicode_ci COMMENT '内容',
  `more` mediumtext COLLATE utf8mb4_unicode_ci COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品表';

-- ----------------------------
--  Table structure for `cmf_mall_item_attr`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_mall_item_attr`;
CREATE TABLE `cmf_mall_item_attr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类ID',
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品模型ID',
  `item_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `sku_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'SKU ID',
  `attr_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销售属性ID',
  `attr_value_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销售属性值ID',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=450 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
--  Table structure for `cmf_mall_item_sku`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_mall_item_sku`;
CREATE TABLE `cmf_mall_item_sku` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺id',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品模型ID',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品类目ID',
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '品牌',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态;0:下架;1:上架',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原价',
  `weight` decimal(20,3) NOT NULL DEFAULT '0.000' COMMENT '商品重量',
  `volume` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '体积;单位ml',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `title` varchar(90) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品标题',
  `sn` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品编号',
  `barcode` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '条形码',
  `thumbnail` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品默认图',
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '销售属性值组成的key(从小到大用排序)',
  `spec_info` longtext COLLATE utf8mb4_unicode_ci COMMENT '规格描述',
  `more` longtext COLLATE utf8mb4_unicode_ci COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品SKU表';

-- ----------------------------
--  Table structure for `cmf_mall_model`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_mall_model`;
CREATE TABLE `cmf_mall_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态;0:禁用;1:正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='商品模型表';

SET FOREIGN_KEY_CHECKS = 1;
