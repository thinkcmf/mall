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



# 2020-03-18
ALTER TABLE `cmf_mall_goods_sku` DROP `on_sale`;
ALTER TABLE `cmf_mall_goods_sku` CHANGE `delete_time` `delete_time` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间:0=激活未删除,大于0=删除(回收站)';
ALTER TABLE `cmf_mall_goods_sku` CHANGE `status` `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1=上架,0=下架';


# 2020-05-07 14:13 增加price_market_min,price_market_max字段
ALTER TABLE `cmf_mall_goods` ADD `price_market_min` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '最小市场价' AFTER `price_max`;
ALTER TABLE `cmf_mall_goods` ADD `price_market_max` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT '最大市场价' AFTER `price_market_min`;
