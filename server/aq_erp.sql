/*
Navicat MySQL Data Transfer

Source Server         : aq_server
Source Server Version : 50721
Source Host           : 120.77.146.125:3306
Source Database       : aq_erp

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2018-12-24 10:33:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for aq_buy_in
-- ----------------------------
DROP TABLE IF EXISTS `aq_buy_in`;
CREATE TABLE `aq_buy_in` (
  `id` varchar(255) NOT NULL DEFAULT '' COMMENT '采购订单号',
  `check_status` tinyint(2) DEFAULT NULL COMMENT '审核状态:已审核 未审核',
  `in_store_status` tinyint(2) DEFAULT NULL COMMENT '入库状态：全部入库 部分入库 未入库',
  `supplier` varchar(255) DEFAULT NULL COMMENT '供应商',
  `store_id` int(11) DEFAULT NULL COMMENT '仓库',
  `build_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `check_time` int(11) DEFAULT NULL COMMENT '审核时间',
  `build_user` int(11) DEFAULT NULL COMMENT '创建人',
  `check_user` int(11) DEFAULT NULL COMMENT '审核人',
  `info` text COMMENT '备注',
  `close_status` tinyint(2) DEFAULT '0' COMMENT '废弃状态：已废弃 未废弃',
  `item_info` text COMMENT '商品信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idkey` (`id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_buy_instore
-- ----------------------------
DROP TABLE IF EXISTS `aq_buy_instore`;
CREATE TABLE `aq_buy_instore` (
  `id` varchar(255) NOT NULL,
  `check_status` tinyint(1) DEFAULT NULL,
  `buy_order` varchar(255) DEFAULT NULL,
  `build_time` int(11) DEFAULT NULL,
  `check_time` int(11) DEFAULT NULL,
  `info` text COMMENT '备注',
  `build_user` int(11) DEFAULT NULL,
  `check_user` int(11) DEFAULT NULL,
  `close_status` tinyint(4) DEFAULT NULL,
  `item_info` text,
  `store_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idkey` (`id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_buy_out
-- ----------------------------
DROP TABLE IF EXISTS `aq_buy_out`;
CREATE TABLE `aq_buy_out` (
  `id` varchar(255) NOT NULL,
  `check_status` tinyint(1) DEFAULT NULL COMMENT '审核状态',
  `buy_order` varchar(255) DEFAULT NULL,
  `build_time` int(11) DEFAULT NULL,
  `check_time` int(11) DEFAULT NULL,
  `build_user` int(11) DEFAULT NULL,
  `check_user` int(11) DEFAULT NULL,
  `info` text COMMENT '备注',
  `item_info` text COMMENT '商品信息',
  `close_status` tinyint(255) DEFAULT NULL COMMENT '废弃状态',
  `store_id` int(11) DEFAULT NULL COMMENT '仓库Id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idkey` (`id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_config
-- ----------------------------
DROP TABLE IF EXISTS `aq_config`;
CREATE TABLE `aq_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '键名',
  `value` text COMMENT '值',
  `info` text COMMENT '描述',
  `int_value` int(11) DEFAULT '0' COMMENT '整数',
  `type` varchar(255) DEFAULT '' COMMENT '类型',
  PRIMARY KEY (`id`,`name`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_database
-- ----------------------------
DROP TABLE IF EXISTS `aq_database`;
CREATE TABLE `aq_database` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `file_num` int(11) DEFAULT '1' COMMENT '文件数量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=275 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_export
-- ----------------------------
DROP TABLE IF EXISTS `aq_export`;
CREATE TABLE `aq_export` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名字',
  `value` text COMMENT '值',
  `model` varchar(255) DEFAULT '' COMMENT '模块名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_instore_log
-- ----------------------------
DROP TABLE IF EXISTS `aq_instore_log`;
CREATE TABLE `aq_instore_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT NULL COMMENT '操作类型 1：入库 2： 出库 3：配货  4：调货',
  `user_id` int(11) DEFAULT NULL COMMENT '操作人',
  `time` int(11) DEFAULT NULL COMMENT '时间',
  `order` varchar(255) DEFAULT NULL COMMENT '单号',
  `store_id` int(11) DEFAULT NULL COMMENT '仓库',
  `item_id` int(11) DEFAULT NULL COMMENT '商品id',
  `before_num` int(11) DEFAULT NULL COMMENT '修改前数量',
  `after_num` int(11) DEFAULT NULL COMMENT '修改后数量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=603198 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_item
-- ----------------------------
DROP TABLE IF EXISTS `aq_item`;
CREATE TABLE `aq_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(255) NOT NULL DEFAULT '' COMMENT '商品条码',
  `code` varchar(255) NOT NULL DEFAULT '' COMMENT '商品代码',
  `name` varchar(255) NOT NULL COMMENT '商品名称',
  `build_time` int(11) DEFAULT NULL COMMENT '建档日期',
  `short_name` varchar(255) DEFAULT '' COMMENT '商品简称',
  `type` int(10) NOT NULL COMMENT '商品类别',
  `weight` double DEFAULT NULL,
  `sell_base_num` int(11) DEFAULT '1' COMMENT '折单最小约数',
  `check_limit` tinyint(4) DEFAULT '0' COMMENT '是否有审核限制1：有 0没有(是否可以限制，单个用户能购买的上限)',
  `milk_period` tinyint(4) DEFAULT '0' COMMENT '牛奶的段数',
  `sort_id` int(11) DEFAULT '1000' COMMENT '排序',
  `is_del` tinyint(4) DEFAULT '0' COMMENT '是否废弃',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `barcode2` (`barcode`) USING BTREE,
  KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=588 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_item_type
-- ----------------------------
DROP TABLE IF EXISTS `aq_item_type`;
CREATE TABLE `aq_item_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `code` varchar(255) DEFAULT NULL COMMENT '代码',
  `info` text COMMENT '备注',
  `level` int(3) DEFAULT '1' COMMENT '第几层',
  `parent_id` int(11) DEFAULT '0' COMMENT '父节点id',
  `sort_id` int(3) DEFAULT '100' COMMENT '排序id',
  `is_del` tinyint(255) DEFAULT '0' COMMENT '是否作废',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_log
-- ----------------------------
DROP TABLE IF EXISTS `aq_log`;
CREATE TABLE `aq_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `info` text CHARACTER SET utf8mb4 COMMENT '详情',
  `controller` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `link` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=222257 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_module
-- ----------------------------
DROP TABLE IF EXISTS `aq_module`;
CREATE TABLE `aq_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `posid` int(11) DEFAULT '0',
  `need_auth` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=752 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_sell
-- ----------------------------
DROP TABLE IF EXISTS `aq_sell`;
CREATE TABLE `aq_sell` (
  `id` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT ' 销售订单编号',
  `check_user` int(11) DEFAULT NULL COMMENT '审核人',
  `check_time` int(11) DEFAULT NULL COMMENT '审核时间',
  `status` tinyint(3) DEFAULT '0' COMMENT '审核状态',
  `build_user` int(11) DEFAULT NULL COMMENT '创建者',
  `build_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `refund_status` tinyint(4) DEFAULT '0' COMMENT '退款状态',
  `pay_status` tinyint(3) DEFAULT '0' COMMENT '支付状态',
  `shop_id` int(3) DEFAULT NULL COMMENT '商店编号',
  `shop_order` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '平台订单编号',
  `customer_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '收货人',
  `customer_addr` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '收货地址',
  `pay_time` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL COMMENT '分配的仓库id',
  `num` int(11) DEFAULT NULL COMMENT '数量',
  `user_info` text CHARACTER SET utf8 COMMENT '买家备注',
  `customer_account` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '买家账号',
  `discount` double DEFAULT NULL COMMENT '折扣',
  `info` text CHARACTER SET utf8 COMMENT '备注',
  `user_phone` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '手机号',
  `pay_money` double DEFAULT NULL COMMENT '支付金',
  `del_info` text CHARACTER SET utf8 COMMENT '废弃原因',
  `sell_type` tinyint(1) DEFAULT '0' COMMENT '订单标识',
  `logistics` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '物流单号',
  `track_man` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '跟单员',
  `sell_vip_type` tinyint(4) DEFAULT '0' COMMENT '订单vip标识',
  `sell_vip_info` text CHARACTER SET utf8 COMMENT '订单vip信息',
  `logistics_merge` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '合并的物流单号',
  `order_time` int(11) DEFAULT NULL COMMENT '客户下单时间',
  `user_id_number` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '身份证',
  `customer_province` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `customer_city` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `customer_area` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '区',
  `send_user_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '发件人姓名',
  `send_user_phone` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '发件人手机',
  `unit_price` double DEFAULT '0' COMMENT '商品单价',
  `freight_price` double DEFAULT '0' COMMENT '运费',
  `service_price` double DEFAULT '0' COMMENT '服务费',
  `freight_unit_price` double DEFAULT '0' COMMENT '单价平台运费',
  `service_unit_price` double DEFAULT '0' COMMENT '单件平均服务费',
  `merge_order` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '被合并的订单',
  `assign_order` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '分配单单号',
  `customer_username` varchar(190) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '平台账号昵称',
  `pay_id` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '订单支付id',
  `customer_userid` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `pay_type` tinyint(4) DEFAULT '0' COMMENT '支付方式',
  `pay_check_info` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '支付审核描述',
  `idnumpic1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `idnumpic2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `shop_sync_flag` tinyint(4) DEFAULT '0' COMMENT '同步到商城的状态',
  `supply_source` tinyint(4) DEFAULT '1' COMMENT '发货方式',
  PRIMARY KEY (`id`,`shop_order`),
  UNIQUE KEY `idkey` (`id`) USING HASH,
  UNIQUE KEY `logistics` (`logistics`) USING BTREE,
  KEY `check_user` (`check_user`),
  KEY `build_user` (`build_user`),
  KEY `itemid` (`item_id`),
  KEY `status` (`status`),
  KEY `shoporderid` (`shop_order`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for aq_sell_assign
-- ----------------------------
DROP TABLE IF EXISTS `aq_sell_assign`;
CREATE TABLE `aq_sell_assign` (
  `id` varchar(255) DEFAULT NULL,
  `info` text,
  `build_time` int(11) DEFAULT NULL,
  `order_info` text,
  `close_status` tinyint(4) DEFAULT NULL COMMENT '作废',
  `build_user` int(11) DEFAULT NULL,
  `store_id` int(11) NOT NULL COMMENT '仓库',
  `total_num` int(11) DEFAULT NULL COMMENT '总罐数',
  `del_info` text COMMENT '废弃原因',
  UNIQUE KEY `idkey` (`id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_shop
-- ----------------------------
DROP TABLE IF EXISTS `aq_shop`;
CREATE TABLE `aq_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商铺id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '商铺名',
  `shop_type` int(5) NOT NULL DEFAULT '0' COMMENT '商铺类型',
  `valid_expire_time` int(11) DEFAULT NULL COMMENT '授权到期时间',
  `token` varchar(255) DEFAULT NULL COMMENT '配置',
  `shop_edit_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_shop_type
-- ----------------------------
DROP TABLE IF EXISTS `aq_shop_type`;
CREATE TABLE `aq_shop_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_store
-- ----------------------------
DROP TABLE IF EXISTS `aq_store`;
CREATE TABLE `aq_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '仓库名',
  `admin_name` varchar(255) DEFAULT NULL COMMENT '联系人姓名',
  `phone` varchar(255) DEFAULT '' COMMENT '联系人电话',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `sell_num_limit` int(11) DEFAULT '0' COMMENT '发货订单商品数量限制 0：没有限制  3:只能发3个商品的订单',
  `check_rule` text COMMENT '字段审核规则(检查用户下单数量)',
  `period_rule` tinyint(4) DEFAULT '0' COMMENT '是否要区分12和34段  0不区分 1区分 ',
  `same_store` int(11) DEFAULT NULL COMMENT '关联仓库',
  `way_num` int(11) DEFAULT '0' COMMENT '在途数',
  `is_del` tinyint(4) DEFAULT '0' COMMENT '是否废弃',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_store_change
-- ----------------------------
DROP TABLE IF EXISTS `aq_store_change`;
CREATE TABLE `aq_store_change` (
  `id` varchar(255) NOT NULL COMMENT '单号',
  `in_store` int(255) DEFAULT NULL COMMENT '转入仓库',
  `out_store` int(255) DEFAULT NULL COMMENT '转出仓库',
  `check_status` tinyint(3) DEFAULT NULL COMMENT '审核状态',
  `check_time` int(11) DEFAULT NULL COMMENT '审核时间',
  `check_user` int(11) DEFAULT NULL COMMENT '审核人id',
  `build_user` int(11) DEFAULT NULL COMMENT '创建人id',
  `build_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `item_info` text COMMENT '调货数量',
  `info` text COMMENT '备注信息',
  `close_status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idkey` (`id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_store_item
-- ----------------------------
DROP TABLE IF EXISTS `aq_store_item`;
CREATE TABLE `aq_store_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL COMMENT '商品Id',
  `in_store` int(11) NOT NULL DEFAULT '0' COMMENT '在库数量',
  `in_sale` int(11) NOT NULL DEFAULT '0' COMMENT '销售订单在确认数量',
  `store_id` int(11) DEFAULT NULL COMMENT '对应的仓库id',
  `on_way` int(11) DEFAULT '0' COMMENT '在途数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `itemid` (`itemid`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=890 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_sys_user
-- ----------------------------
DROP TABLE IF EXISTS `aq_sys_user`;
CREATE TABLE `aq_sys_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(255) NOT NULL DEFAULT '' COMMENT '账号名',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `mail` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `reg_time` int(11) DEFAULT '0' COMMENT '注册时间',
  `phone` varchar(255) DEFAULT '' COMMENT '手机号',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `is_system` int(1) NOT NULL DEFAULT '0' COMMENT '是否是系统用户',
  `is_valid` int(1) NOT NULL DEFAULT '1' COMMENT '是否在有效',
  `user_group` int(11) NOT NULL DEFAULT '0' COMMENT '用户所属用户组',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for aq_sys_usergroup
-- ----------------------------
DROP TABLE IF EXISTS `aq_sys_usergroup`;
CREATE TABLE `aq_sys_usergroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户组名称',
  `module_ids` text COMMENT '权限组',
  `is_sys` int(1) NOT NULL DEFAULT '0' COMMENT '是否是管理员组',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
