-- 增加商品属性值排序 2019-04-15 8:40
ALTER TABLE `cmf_mall_attr_value` ADD `list_order` FLOAT NOT NULL DEFAULT '10000' COMMENT '排序' AFTER `attr_id`;

ALTER TABLE `cmf_mall_attr_value` DROP `type_id`;


-- 增加商品规格删除时间 2019-04-15 18:26
ALTER TABLE `cmf_mall_item_sku` ADD `delete_time` INT NOT NULL DEFAULT '0' COMMENT '删除时间' AFTER `update_time`;
ALTER TABLE `cmf_mall_item_sku` ADD `list_order` FLOAT NOT NULL DEFAULT '10000' COMMENT '排序' AFTER `delete_time`;
