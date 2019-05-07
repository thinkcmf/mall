-- ----------------------------
--  Table structure for `cmf_order`
-- ----------------------------
CREATE TABLE `cmf_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级 id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `order_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '订单状态(0:已完成;1:未完成;2:已取消;)',
  `shipping_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态(0:未发货;1:已发货;2:已收货;10:待发货)',
  `is_inventory_ready` tinyint(4) NOT NULL DEFAULT '0' COMMENT '库存准备完成',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态(0:未支付;1:已支付;10:等待支付)',
  `invoice_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发票状态(0:未开票;1:已开票)',
  `user_confirmed` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户是否确认;1:已确认;0:未确认;2:确认邮件未发',
  `print_invoice_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '开票方式;0:按客户设置;1:手动;2:自动',
  `country` smallint(6) unsigned NOT NULL DEFAULT '86' COMMENT '国家代码，默认中国',
  `province` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '省份',
  `city` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '城市',
  `district` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '县区',
  `town` smallint(6) NOT NULL DEFAULT '0' COMMENT '乡镇',
  `goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品总价',
  `user_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用余额',
  `score` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用积分',
  `score_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用积分抵多少钱',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付款金额',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单总价',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '已支付金额',
  `express_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下单时间',
  `confirm_time` int(11) NOT NULL DEFAULT '0' COMMENT '订单确认时间',
  `deliver_time` int(11) NOT NULL DEFAULT '0' COMMENT '发货时间',
  `delivery_confirm_time` int(11) NOT NULL DEFAULT '0' COMMENT '收货确认时间',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `expire_time` int(11) NOT NULL DEFAULT '0' COMMENT '到期时间(配合pay_status表示订单到期时间)',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格调整',
  `order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '订单编号',
  `consignee` varchar(60) NOT NULL DEFAULT '' COMMENT '收货人',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '地址',
  `zip_code` varchar(10) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮件',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `mobile2` varchar(20) NOT NULL DEFAULT '' COMMENT '备用手机号',
  `shipment_code` varchar(20) NOT NULL DEFAULT '' COMMENT '物流code',
  `shipment_name` varchar(30) NOT NULL DEFAULT '' COMMENT '物流名称',
  `is_supplier_deliver` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否由供应商直接发货给用户',
  `tracking_number` varchar(100) NOT NULL DEFAULT '' COMMENT '快递单号',
  `payment_code` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式code',
  `payment_name` varchar(30) NOT NULL DEFAULT '' COMMENT '支付方式名称',
  `invoice_title` varchar(50) NOT NULL DEFAULT '' COMMENT '发票抬头',
  `invoice_taxpayer_id` varchar(30) NOT NULL DEFAULT '' COMMENT '发票纳税人识别码',
  `user_note` varchar(255) NOT NULL DEFAULT '' COMMENT '用户备注',
  `admin_note` varchar(255) NOT NULL DEFAULT '' COMMENT '管理员备注',
  `source` varchar(30) NOT NULL DEFAULT '' COMMENT '订单来源',
  `out_transaction_id` varchar(64) NOT NULL DEFAULT '' COMMENT '第三方支付订单号',
  `more` text COMMENT '扩展信息,JSON格式',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn` (`order_sn`),
  KEY `order_status` (`order_status`),
  KEY `shipping_status` (`shipping_status`),
  KEY `pay_status` (`pay_status`),
  KEY `uid` (`user_id`) USING BTREE,
  KEY `shipping_code` (`shipment_code`) USING BTREE,
  KEY `payment_code` (`payment_code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- ----------------------------
--  Table structure for `cmf_order_cart`
-- ----------------------------
CREATE TABLE `cmf_order_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '购物车表',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订购物品实体所在表的主键id',
  `goods_sku_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品规格表 id',
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品原价',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品实际支付价格',
  `goods_quantity` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `selected` tinyint(1) NOT NULL DEFAULT '1' COMMENT '购物车选中状态(1:选中;0:未选中)',
  `is_virtual` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否为虚拟商品，0:否;1:是',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '加入购物车的时间',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '到期时间',
  `deletable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可删除；1:可删除;0:不可删除',
  `table_name` varchar(64) NOT NULL DEFAULT '' COMMENT '订购物品实体以前所在表，不带前缀',
  `goods_sku_table` varchar(64) NOT NULL DEFAULT '' COMMENT '商品规格表名',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `goods_spec` varchar(500) NOT NULL DEFAULT '' COMMENT '商品规格(文字描述)',
  `goods_sn` varchar(64) NOT NULL DEFAULT '' COMMENT '商品货号',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='购物车';

-- ----------------------------
--  Table structure for `cmf_order_invoice`
-- ----------------------------
CREATE TABLE `cmf_order_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户 id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '发票类型;1: 个人;2:增值税普通发票;3:增值税专用发票;',
  `shipping_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发货状态(0:未发货;1:已发货;2:已收货;10:待发货)',
  `invoice_no` varchar(500) NOT NULL DEFAULT '' COMMENT '发票号',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '发票抬头，（个人姓名，或公司名称）',
  `taxpayer_id` varchar(30) NOT NULL DEFAULT '' COMMENT '纳税人识别码',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '公司电话',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '公司地址',
  `bank_name` varchar(60) NOT NULL DEFAULT '' COMMENT '开户行',
  `bank_account` varchar(30) NOT NULL DEFAULT '' COMMENT '银行账号',
  `shipment_code` varchar(20) NOT NULL DEFAULT '' COMMENT '物流code',
  `shipment_name` varchar(30) NOT NULL DEFAULT '' COMMENT '物流名称',
  `tracking_number` varchar(32) NOT NULL DEFAULT '' COMMENT '物流单号',
  `deliver_time` int(11) NOT NULL DEFAULT '0' COMMENT '发货时间',
  `consignee_info` text COMMENT '收件人信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='发票表';

-- ----------------------------
--  Table structure for `cmf_order_invoice_order`
-- ----------------------------
CREATE TABLE `cmf_order_invoice_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发票 id',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单 id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='发票订单表';

-- ----------------------------
--  Table structure for `cmf_order_item`
-- ----------------------------
CREATE TABLE `cmf_order_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户 ID',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订购物品实体所在表的主键id',
  `goods_sku_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品规格表 id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `confirm_time` int(11) NOT NULL DEFAULT '0' COMMENT '订单确认时间',
  `expire_time` int(11) NOT NULL DEFAULT '0' COMMENT '支付过期时间',
  `goods_quantity` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '购买数量',
  `goods_quantity_locked` int(11) NOT NULL DEFAULT '0' COMMENT '已锁定商品数量',
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品原价',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付价格',
  `reward_score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '购买商品赠送积分',
  `table_name` varchar(50) NOT NULL DEFAULT '' COMMENT '订购物品实体以前所在表，不带前缀',
  `goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '物品名称',
  `goods_thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `goods_sku_table` varchar(64) NOT NULL DEFAULT '' COMMENT '商品规格表名',
  `goods_spec` varchar(500) NOT NULL DEFAULT '' COMMENT '商品规格',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品货号',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
--  Table structure for `cmf_order_merge`
-- ----------------------------
CREATE TABLE `cmf_order_merge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_ids` text NOT NULL,
  `pay_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '支付状态(0:未支付;1:已支付;10:等待支付)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
--  Table structure for `cmf_order_payment`
-- ----------------------------
CREATE TABLE `cmf_order_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_online` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否在线支付',
  `is_cod` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否货到付款(cash on delivery (COD))',
  `is_prepay` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否先付费;0:否;1:是',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:开启;0:关闭)',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `list_order` float(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'pay_coder',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '支付code',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '支付方式名称',
  `description` varchar(255) NOT NULL COMMENT '描述',
  `tips` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `config` text COMMENT '配置',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pay_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付方式表';

-- ----------------------------
--  Table structure for `cmf_order_shipment`
-- ----------------------------
CREATE TABLE `cmf_order_shipment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '表 id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启(1:开启;0:禁用)',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '快递代号',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '快递名称',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `shipping_code` (`code`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
--  Table structure for `cmf_order_user_address`
-- ----------------------------
CREATE TABLE `cmf_order_user_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `country` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '国家',
  `province` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '省份',
  `city` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '城市',
  `district` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地区',
  `town` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乡镇',
  `province_name` varchar(20) NOT NULL DEFAULT '' COMMENT '省份名',
  `city_name` varchar(20) NOT NULL DEFAULT '' COMMENT '市名',
  `district_name` varchar(20) NOT NULL DEFAULT '' COMMENT '地区名',
  `town_name` varchar(20) NOT NULL DEFAULT '' COMMENT '乡镇名',
  `consignee` varchar(20) NOT NULL DEFAULT '' COMMENT '收货人',
  `alias` varchar(20) NOT NULL DEFAULT '' COMMENT '地址别名(方便用户记忆)',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `address` varchar(120) NOT NULL DEFAULT '' COMMENT '地址',
  `zip_code` varchar(10) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `mobile` varchar(30) NOT NULL DEFAULT '' COMMENT '手机',
  `mobile2` varchar(30) NOT NULL DEFAULT '' COMMENT '备用手机号',
  `is_default` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为默认收货地址',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
--  Table structure for `cmf_order_user_invoice`
-- ----------------------------
CREATE TABLE `cmf_order_user_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户 id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '发票类型;1: 个人;2:增值税普通发票;3:增值税专用发票;',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '发票抬头，（个人姓名，或公司名称）',
  `taxpayer_id` varchar(30) NOT NULL DEFAULT '' COMMENT '纳税人识别码',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '公司电话',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '公司地址',
  `bank_name` varchar(60) NOT NULL DEFAULT '' COMMENT '开户行',
  `bank_account` varchar(30) NOT NULL DEFAULT '' COMMENT '银行账号',
  `consignee_info` text COMMENT '收件人信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户发票信息表';