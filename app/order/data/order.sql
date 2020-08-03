# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.28)
# Database: devel_cmfmall_db_design
# Generation Time: 2020-01-06 17:38:23 +0000
# ************************************************************



# Dump of table cmf_order
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cmf_order` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `sn` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '订单流水号',
  `channel` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '订单渠道',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间(配合pay_status表示订单到期时间)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1=激活,0=冻结',
  `country` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '中国' COMMENT '国家，默认中国',
  `province` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '城市',
  `district` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '县区',
  `town` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '乡镇街道',
  `area_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '地区代码',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '地址',
  `consignee` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '收货人',
  `zip_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮政编码',
  `email` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮件',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机',
  `phone2` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备用手机号',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户备注',
  `total_weight` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '包裹重量(KG)',
  `total_item` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '商品件数',
  `amount_goods` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品总价',
  `amount_shipfee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费总额',
  `amount_offset` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠总额',
  `amount_payable` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付款金额',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态:0=未支付,1=已支付',
  `pay_up_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付发起时间',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间(支付与否标准)',
  `pay_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付金额',
  `pay_method` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '支付方式',
  `pay_sn` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '支付流水号',
  `pay_info` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '支付信息',
  `ship_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态(0:未发货;1:已发货)',
  `ship_time` int(10) NOT NULL DEFAULT '0' COMMENT '发货时间',
  `ship_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '物流公司代码',
  `ship_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '物流名称',
  `ship_sn` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '物流面单号',
  `delivery_time` int(10) NOT NULL DEFAULT '0' COMMENT '物流送达时间',
  `received_time` int(10) NOT NULL DEFAULT '0' COMMENT '收货确认时间',
  `finished_time` int(10) NOT NULL DEFAULT '0' COMMENT '订单完成时间',
  `flag` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '后台标记',
  `notice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '后台备注',
  `more` longtext COLLATE utf8mb4_unicode_ci COMMENT '扩展信息(JSON)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';



# Dump of table cmf_order_item
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `cmf_order_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `item_sn` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品货号(因为不确定来自哪个表，但是对应表唯一)',
  `goods_table` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '物件表(不带前缀)',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `goods_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '物品名称',
  `thumbnail` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `sku_table` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品规格表名',
  `sku_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '规格id',
  `sku_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '物品名称',
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `quantity` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '购买数量',
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品原价',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成交价格',
  `more` longtext COLLATE utf8mb4_unicode_ci COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单物品表';
