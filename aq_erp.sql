/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50617
Source Host           : 127.0.0.1:3306
Source Database       : aq_erp

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2018-07-20 17:06:53
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
-- Records of aq_buy_in
-- ----------------------------

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
-- Records of aq_buy_instore
-- ----------------------------

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
-- Records of aq_buy_out
-- ----------------------------

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
  PRIMARY KEY (`id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_config
-- ----------------------------
INSERT INTO `aq_config` VALUES ('1', 'sell_check_default_user', '2', '销售订单默认审核人', null, 'string');
INSERT INTO `aq_config` VALUES ('2', 'logistics_base', 'AB62000000AU', '物流单号基数', '0', 'string');
INSERT INTO `aq_config` VALUES ('3', 'logistics_count', null, '物流单号数量', '76049', 'int');
INSERT INTO `aq_config` VALUES ('4', 'bc_value', '', 'BC对应的值', '6', 'int');
INSERT INTO `aq_config` VALUES ('5', 'cc_value', null, 'CC对应的值', '3', 'int');
INSERT INTO `aq_config` VALUES ('6', 'ns_value', null, 'NS对应的值', '1', 'int');
INSERT INTO `aq_config` VALUES ('7', 'no_value', null, '不拆单对应数量', '100000', 'int');

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
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_database
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_export
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=221554 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_instore_log
-- ----------------------------

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
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=519 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_item
-- ----------------------------
INSERT INTO `aq_item` VALUES ('252', '9418783002837', 'A002900', 'Aptamil-金装婴儿奶粉2段900g', '0', 'K2', '25', '1080', '3', '1', '2', '2', '0');
INSERT INTO `aq_item` VALUES ('253', '9418783002844', 'A003900', 'Aptamil-金装婴儿奶粉3段900g', '0', 'K3', '25', '1080', '6', '1', '3', '3', '0');
INSERT INTO `aq_item` VALUES ('254', '9418783002851', 'A004900', 'Aptamil-金装婴儿奶粉4段900g', '0', 'K4', '25', '1080', '6', '1', '4', '4', '0');
INSERT INTO `aq_item` VALUES ('259', '9418783000413', 'KG001900', 'Karicare-婴儿羊奶粉1段900g', '0', 'G1', '23', '900', '3', '1', '1', '9', '0');
INSERT INTO `aq_item` VALUES ('260', '9418783000420', 'KG002900', 'Karicare-婴儿羊奶粉2段900g', '0', 'G2', '23', '900', '3', '1', '2', '10', '0');
INSERT INTO `aq_item` VALUES ('261', '9418783003025', 'KG003900', 'Karicare-婴儿羊奶粉3段900g', '0', 'G3', '23', '900', '3', '1', '3', '11', '0');
INSERT INTO `aq_item` VALUES ('262', '9332045000174', 'B001900', 'Bellamy-婴儿奶粉1段900g', '0', 'B1', '22', '900', '3', '1', '1', '12', '0');
INSERT INTO `aq_item` VALUES ('263', '9332045000181', 'B002900', 'Bellamy-婴儿奶粉2段900g', '0', 'B2', '22', '900', '3', '1', '2', '13', '0');
INSERT INTO `aq_item` VALUES ('264', '9332045000198', 'B003900', 'Bellamy-婴儿奶粉3段900g', '0', 'B3', '22', '900', '3', '1', '3', '14', '0');
INSERT INTO `aq_item` VALUES ('265', '9421902960031', 'A201900', 'A2-婴儿奶粉1段900g', '0', 'A1', '21', '900', '3', '1', '1', '15', '0');
INSERT INTO `aq_item` VALUES ('266', '9421902960048', 'A202900', 'A2-婴儿奶粉2段900g', '0', 'A2', '21', '1080', '3', '1', '2', '16', '0');
INSERT INTO `aq_item` VALUES ('267', '9421902960055', 'A203900', 'A2-婴儿奶粉3段900g', '0', 'A3', '21', '1080', '3', '1', '3', '17', '0');
INSERT INTO `aq_item` VALUES ('268', '', 'DAPRE800', '德国爱他美 PRE段 800g', '0', 'DA pre', '20', '900', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('269', '', 'DA001800', '德国爱他美 1段 800g', '0', 'DA1', '20', '900', '1', '0', '1', '1000', '0');
INSERT INTO `aq_item` VALUES ('270', '', 'DA002800', '德国爱他美 2段 800g', '0', 'DA2', '20', '900', '1', '0', '2', '1000', '0');
INSERT INTO `aq_item` VALUES ('271', '', 'DA003800', '德国爱他美 3段 800g', '0', 'DA3', '20', '900', '1', '0', '3', '1000', '0');
INSERT INTO `aq_item` VALUES ('272', '', 'DA004600', '德国爱他美 1+ 600g', '0', 'DA1+', '20', '700', '1', '0', '1', '1000', '0');
INSERT INTO `aq_item` VALUES ('273', '', 'DA005600', '德国爱他美 2+ 600g', '0', 'DA2+', '20', '700', '1', '0', '2', '1000', '0');
INSERT INTO `aq_item` VALUES ('274', '', 'N001850', '荷兰牛栏 1段 850g', '0', 'N1', '19', '950', '1', '0', '1', '1000', '0');
INSERT INTO `aq_item` VALUES ('275', '', 'N002850', '荷兰牛栏 2段 850g', '0', 'N2', '19', '950', '1', '0', '2', '1000', '0');
INSERT INTO `aq_item` VALUES ('276', '', 'N003800', '荷兰牛栏 3段 800g', '0', 'N3', '19', '900', '1', '0', '3', '1000', '0');
INSERT INTO `aq_item` VALUES ('277', '', 'N004800', '荷兰牛栏 4段 800g', '0', 'N4', '19', '900', '1', '0', '4', '1000', '0');
INSERT INTO `aq_item` VALUES ('278', '', 'N005800', '荷兰牛栏 5段 800g', '0', 'N5', '19', '900', '1', '0', '5', '1000', '0');
INSERT INTO `aq_item` VALUES ('279', '', 'HLMS1800', '荷兰美素 1段 800g', '0', '荷兰美素1段', '18', '900', '1', '0', '1', '1000', '0');
INSERT INTO `aq_item` VALUES ('280', '', 'HLMS2800', '荷兰美素 2段 800g', '0', '荷兰美素2段', '18', '900', '1', '0', '2', '1000', '0');
INSERT INTO `aq_item` VALUES ('281', '', 'HLMS3800', '荷兰美素 3段 800g', '0', '荷兰美素3段', '18', '900', '1', '0', '3', '1000', '0');
INSERT INTO `aq_item` VALUES ('282', '', 'HLMS4700', '荷兰美素 4段 700g', '0', '荷兰美素4段', '18', '800', '1', '0', '4', '1000', '0');
INSERT INTO `aq_item` VALUES ('283', '', 'HLMS5700', '荷兰美素 5段 700g', '0', '荷兰美素5段', '18', '800', '1', '0', '5', '1000', '0');
INSERT INTO `aq_item` VALUES ('284', '', 'AMYF900', '安满满悦孕妇奶粉 900g', '0', '安满', '17', '1080', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('285', '', 'DYQZ1000', 'Devondale-成人奶粉全脂', '0', '德运全脂', '17', '1000', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('286', '', 'DYTZ1000', 'Devondale-成人奶粉脱脂', '0', '德运脱脂', '17', '1000', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('287', '', 'AJQZ900', '安佳全脂罐装 900g', '0', '安佳全脂', '17', '1100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('288', '', 'CRYNF1000', 'Caprilac成人羊奶粉 1KG', '0', '成人羊奶粉', '17', '1000', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('289', '', 'GS10100', '羊奶皂原味 100g', '0', '羊皂原味', '16', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('290', '', 'GS20100', '羊奶皂柠檬味 100g', '0', '羊皂柠檬味', '16', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('291', '', 'GSFM100', '羊奶皂蜂蜜味 100g', '0', '羊皂蜂蜜味', '16', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('292', '', 'GSYM100', '羊奶皂燕麦味 100g', '0', '羊皂燕麦味', '16', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('293', '', 'YNRF500', '羊奶润肤露 500ml', '0', '羊奶润肤露', '16', '600', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('294', '', 'SWJYY500', 'Swisse 胶原蛋白液 500ml', '0', 'SW胶原液', '11', '600', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('295', '', 'SWJYP100', 'Swisse 胶原蛋白片 100片', '0', 'SW胶原片', '15', '130', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('296', '', 'SWYLSY501', 'Swisse 叶绿素液梅子味 500ml', '0', 'SW叶绿素梅子', '15', '560', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('297', '', 'SWYLSY502', 'Swisse 叶绿素液薄荷味 500ml', '0', 'SW叶绿素薄荷', '15', '600', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('298', '', 'SWYLSP100', 'Swisse 叶绿素片 100片', '0', 'SW叶绿素片', '15', '90', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('299', '', 'SWG150', 'Swisse 钙片+维生素D 150片', '0', 'SW钙片', '15', '330', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('300', '', 'SWMYM30', 'Swisse 蔓越莓 30片', '0', 'SW蔓越莓30片', '15', '80', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('301', '', 'SWMYM180', 'Swisse 蔓越莓 180片', '0', 'SW蔓越莓180片', '15', '360', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('302', '', 'SWPTZ60', 'Swisse 葡萄籽 60片', '0', 'SW葡萄籽60片', '15', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('303', '', 'SWPTZ180', 'Swisse 葡萄籽 180片', '0', 'SW葡萄籽180片', '15', '200', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('304', '', 'SWHG120', 'Swisse 护肝片 120片', '0', 'SW护肝', '15', '165', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('305', '', 'SWSMP120', 'Swisse 睡眠片 100片', '0', 'SW睡眠', '15', '170', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('306', '', 'SWWGL90', 'Swisse 维骨力 90片', '0', 'SW维骨力', '15', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('307', '', 'SWQLK50', 'Swisse 前列康 50片', '0', 'SW前列康', '15', '105', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('308', '', 'SWLXZ100', 'Swisse 螺旋藻 100片', '0', 'SW螺旋藻', '15', '90', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('309', '', 'SWWSS120', 'Swisse 女士复合维生素 120片', '0', 'SW女维', '15', '230', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('310', '', 'SWWSS121', 'Swisse 男士复合维生素 120片', '0', 'SW男维', '15', '235', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('311', '', 'SWWSS122', 'Swisse 儿童复合维生素 120片', '0', 'SW儿童复合', '15', '155', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('312', '', 'SWCMYY90', 'Swisse 儿童聪明鱼油 90片', '0', 'SW儿童聪明鱼油', '15', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('313', '', 'BMYJC190', 'BLACKMORES 月见草 190片', '0', 'BM月见草', '14', '320', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('314', '', 'BMHJS180', 'BLACKMORES 孕妇黄金素 180片', '0', 'BM黄金素', '14', '315', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('315', '', 'BMXQZ50', 'BLACKMORES 西芹籽 50片', '0', 'BM西芹籽', '14', '170', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('316', '', 'BMYY401', 'BLACKMORES 原味鱼油 400片', '0', 'BM鱼油原味', '14', '650', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('317', '', 'BMYY402', 'BLACKMORES 无腥味鱼油 400片', '0', 'BM鱼油无腥味', '14', '635', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('318', '', 'BMWGL180', 'BLACKMORES 维骨力 180片', '0', 'BM维骨力', '14', '600', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('319', '', 'BMFM30', 'BLACKMORES 辅酶Q10 150mg 30片', '0', 'BM辅酶', '14', '110', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('320', '', 'BMMS50', 'BLACKMORES 维e面霜 50g', '0', 'BM维E面霜', '14', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('321', '', 'BMXT90', 'BLACKMORES 血糖平衡片 90片', '0', 'BM血糖平衡', '14', '170', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('322', '', 'BMSJM40', 'BLACKMORES 圣洁莓 40片', '0', 'BM圣洁莓', '14', '120', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('323', '', 'HCFJ200', 'HC 蜂胶 1000mg 200片', '0', 'HC蜂胶', '13', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('324', '', 'HCMYY100', 'HC 绵羊油 100g', '0', 'HC绵羊油', '13', '200', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('325', '', 'HCPTZ300', 'HC 葡萄籽 300片', '0', 'HC葡萄籽', '13', '225', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('326', '', 'GMMY100', 'GM 绵羊油 100g', '0', 'GM绵羊油 100g', '12', '150', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('327', '', 'GMEM100', 'GM 鸸鹋油 100g', '0', 'GM鸸鹋油 100g', '12', '150', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('328', '', 'GMWS25', 'GM 维生素E 晚霜 100g', '0', 'GM维E晚霜 100g', '12', '150', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('329', '', 'RSHT500', '红印黑糖 500g', '0', '黑糖', '11', '600', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('330', '', 'RSYG101', '红印牙膏柠檬味 100g', '0', '红印牙膏柠檬味', '10', '120', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('331', '', 'RSYG102', '红印牙膏苏打味 100g', '0', '红印牙膏苏打味', '10', '120', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('332', '', 'RSYG103', '红印牙膏去烟渍 100g', '0', '红印牙膏去烟渍', '10', '120', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('333', '', 'ZHLK250', '乐康膏（水果膏） 250g', '0', '乐康膏250g', '9', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('334', '', 'ZHLK500', '乐康膏（水果膏） 500g', '0', '乐康膏500g', '9', '600', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('335', '', 'ZHFSBY', '澳洲香蕉船防晒婴儿（粉色） 75ml', '0', '香蕉船防晒baby', '16', '150', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('336', '', 'ZHFSKID', '澳洲香蕉船防晒儿童（蓝黄色） 75ml', '0', '香蕉船防晒kid', '16', '150', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('337', '', 'ZHKWT5', '康维他蜂蜜 5+ 1kg', '0', '康维他5+1kg', '9', '1080', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('338', '', 'ZHKWT10', '康维他蜂蜜 10+ 500g', '0', '康维他10+ 500g', '9', '545', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('339', '', 'BIOYY90', 'BIO island 鱼油瓶装 90片', '0', 'bio鱼油', '8', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('340', '', 'BIORG90', 'BIO island 乳钙瓶装 90片', '0', 'bio乳钙', '8', '110', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('341', '', 'BIOX120', 'BIO island 锌片 120片', '0', 'bio锌片', '8', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('342', '', 'BIODHA30', 'BIO island DHA盒装 30片', '0', 'bioDHA盒装', '8', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('343', '', 'BIODHA60', 'BIO island DHA瓶装 60片', '0', 'bioDHA瓶装', '8', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('344', '', 'ZHFW135', 'Aerogard 防蚊喷雾 135ml', '0', '蚊水135ml', '9', '150', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('345', '', 'ZHFW175', 'Aerogard 防蚊喷雾 175ml', '0', '蚊水175ml', '9', '200', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('346', '', 'ZHFW50', 'Aerogard 防蚊喷雾 50ml', '0', '蚊水50ml', '9', '50', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('347', '', 'ZHYSJKID', 'Life Space Children 儿童益生菌 60g', '0', '益生菌儿童children', '9', '200', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('348', '', 'ZHYSJBABY', 'Life Space baby 婴儿益生菌 60g', '0', '益生菌婴儿baby', '9', '200', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('349', '', 'ZHDJ20', 'Ostelin 维生素D滴剂 20ml', '0', '维D滴剂', '9', '75', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('350', '', 'NWYY60', '佳思敏儿童软糖（Omega3+鱼油） 60片', '0', '佳思敏（Omega3+鱼油）', '7', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('351', '', 'NWFF60', '佳思敏儿童软糖（Omega3+复合维生素） 60片', '0', '佳思敏（Omega3+复合维生素）', '7', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('352', '', 'NWKLG60', '佳思敏儿童软糖（抗流感） 60片', '0', '佳思敏（抗流感）', '7', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('353', '', 'NWCX60', '佳思敏儿童软糖（维生素C+锌） 60片', '0', '佳思敏（维生素C+锌）', '7', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('354', '', 'NWFTS60', '佳思敏儿童软糖（复合维生素+防挑食） 60片', '0', '佳思敏（复合维生素+防挑食）', '7', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('355', '', 'NWSC60', '佳思敏儿童软糖（复合维生素+蔬菜） 60片', '0', '佳思敏（复合维生素+蔬菜）', '7', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('356', '', 'NWGD60', '佳思敏儿童软糖（钙+维生素D） 60片', '0', '佳思敏（钙+维生素D）', '7', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('357', '', 'ZHHSLXJ', '贺寿利奶片香蕉味 210g', '0', '贺寿利奶片香蕉味', '5', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('358', '', 'ZHHSLQKL', '贺寿利奶片巧克力味 210g', '0', '贺寿利奶片巧克力味', '5', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('359', '', 'ZHHSLXC', '贺寿利奶片香草味 210g', '0', '贺寿利奶片香草味', '5', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('360', '', 'ZHXFCS250', 'Redwin洗发水（茶树油） 250ml', '0', '洗发水（茶树油）', '4', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('361', '', 'ZHXFJM250', 'Redwin洗发水（焦炭油） 250ml', '0', '洗发水（焦炭油）', '4', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('362', '', 'RNMM50', 'Royal Nectar 蜂毒面膜 50ml', '0', '蜂毒面膜', '3', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('363', '', 'BIODSJ90', 'BIO island  袋鼠精胶囊 90片', '0', '袋鼠精', '8', '200', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('364', '', 'ZHFC500', 'Queen Bee 蜂巢 500g', '0', '蜂巢', '9', '600', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('365', '', 'ZHQFY200', 'Biobalance 清肺液 200ml', '0', '清肺液', '9', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('366', '', 'ZHWNY200', 'BIO OIL 万能油 200ml', '0', 'BIO-OIL万能油', '16', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('367', '', 'ZHMGG25', 'LUCAS 万用木瓜膏 25g', '0', '木瓜膏', '16', '50', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('368', '', 'FFFX100', 'Freezeframe丰胸膏 100ml', '0', 'FF丰胸膏', '2', '200', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('369', '', 'FFYS15', 'Freezeframe眼霜 15g', '0', 'FF眼霜', '2', '50', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('370', '', 'ZHHFR250', 'Discreet黑发还原乳 250ml', '0', '黑发乳', '9', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('371', '', 'ZHYZS750', '瘦身椰子水 750ml', '0', '椰子水', '9', '900', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('372', '', 'ZHHT500', '德国铁元 补血 500ml', '0', '铁元', '9', '1000', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('373', '', 'ZHSM150', '手膜护手霜 150g', '0', '手膜', '16', '200', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('374', '', 'FFXY250', 'Femfresh女性洗液250ml', '0', '女性洗液', '4', '400', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('375', '', 'ZHQDG25', '星期四茶树油祛痘凝胶（特效去暗疮）25g', '0', '星期四祛痘膏', '16', '50', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('376', '', 'BIOET500', 'BIO-E 儿童蜂蜜  500g', '0', 'BIO-E 儿童蜂蜜', '8', '600', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('377', '', 'BIOJT500', 'BIO-E 姜糖蜂蜜  500g', '0', 'BIO-E 姜糖蜂蜜', '8', '600', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('378', '', 'BIONM500', 'BIO-E 柠檬蜂蜜  500g', '0', 'BIO-E 柠檬蜂蜜', '8', '600', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('379', '', 'WWSTZ1000', 'WWS脱脂 1kg', '0', 'WWS脱脂', '17', '1000', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('380', '', 'KWTBBT10', '康维他蜂胶棒棒糖 10支', '0', '康维他棒棒糖', '5', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('381', '', 'MFYY400', '洗洁精原味 400ml', '0', '洗洁精原味', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('382', '', 'MFKJ400', '洗洁精抗菌 400ml', '0', '洗洁精抗菌', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('383', '', 'MFNM400', '洗洁精柠檬味 400ml', '0', '洗洁精柠檬味', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('384', '', 'MFML400', '洗洁精茉莉味 400ml', '0', '洗洁精茉莉味', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('385', '', 'MFQN400', '洗洁精青柠味 400ml', '0', '洗洁精青柠味', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('386', '', 'NWSWYY180', '佳思敏三味鱼油180粒', '0', '佳思敏三味鱼油180粒', '7', '315', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('387', '', 'ZHMLG100', '摩洛哥护发精油 100ml', '0', '摩洛哥发油', '16', '285', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('388', '', 'ZHMYMG250', '蔓越莓干（零食） 250g', '0', '蔓越莓干（零食）', '5', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('389', '', 'HCJSX200', 'HC 角鲨烯软胶囊 200粒', '0', '角鲨烯', '13', '400', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('390', '', 'HCNCR300', 'HC 牛初乳粉 300g', '0', '牛初乳', '13', '375', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('391', '', 'ZHKLG50', 'Ostelin儿童VD+钙咀嚼片 50片', '0', '恐龙钙', '9', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('392', '', 'HCLLZ100', 'HC 大豆卵磷脂胶囊 100粒', '0', '卵磷脂', '13', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('393', '', 'HCYNP300', 'HC 纯天然羊奶片咀嚼片 巧克力味300粒', '0', '巧克力羊奶片', '13', '265', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('394', '', 'ZHMKF100', '玛卡植物精华超级粉100g', '0', '玛卡粉', '9', '115', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('395', '9344949000136', 'ZGMKZ1000', 'Maxigenes-成人奶粉全脂', '0', 'MAX', '17', '1000', '6', '1', '0', '24', '0');
INSERT INTO `aq_item` VALUES ('396', '', 'YSJCR', '成人益生菌', '0', '成人益生菌', '9', '150', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('397', '', 'RNMS50', 'Royal Nectar 蜂毒面霜 50g', '0', '蜂毒面霜', '3', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('398', '', 'RNYS15', 'Royal Nectar 蜂毒眼霜 15g', '0', '蜂毒眼霜', '3', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('399', '', 'GMMY250', 'GM 绵羊油 250g', '0', 'GM 绵羊油 250g', '12', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('400', '', 'SWFJ210', 'Swisse蜂胶 210粒', '0', 'SW蜂胶 210粒', '15', '365', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('401', '', 'MFBC400', '洗洁精白茶味 400ml', '0', '洗洁精白茶味', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('402', '', 'SWPT03', 'Swisse 维C泡腾片', '0', '维C泡腾片', '15', '350', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('403', '', 'RSYG104', '红印牙膏蜂胶味 100g', '0', '红印牙膏蜂胶', '10', '120', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('404', '', 'MFAC400', '洗洁精澳橙味 400ml', '0', '洗洁精澳橙', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('405', '', 'GMEM250', 'GM 鸸鹋油 250g', '0', 'GM鸸鹋油 250g', '12', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('406', '', 'HCFJ201', 'HC 蜂胶2000mg 200片', '0', 'HC蜂胶2000mg', '13', '410', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('407', '', 'FFXY251', 'Femfresh女性洗液（百合）250ml', '0', '女性洗液（百合）', '2', '400', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('408', '', 'ZHMLS360', '澳洲maltesers麦丽素360g', '0', '麦丽素360g', '5', '400', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('409', '', 'ZHSM50', '澳洲DU\'IT 脚膜脚霜足膜50g', '0', '脚膜', '16', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('410', '', 'ZHWHB10', 'knoppers威化饼干 一条十块', '0', '威化饼 10块', '5', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('411', '', 'H005900', 'A2-成人奶粉全脂', '0', 'A2A', '17', '1000', '6', '1', '0', '23', '0');
INSERT INTO `aq_item` VALUES ('412', '', 'ZHXYCZT01', '薰衣草枕头', '0', '枕头', '4', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('413', '', 'XGMS3900', '港版美素佳儿3段 900g', '0', '港版美素3段', '18', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('414', '', 'ZHKLRC01', 'careline 柯蓝绵羊油润唇膏无色 5g', '0', '柯蓝润唇膏', '16', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('415', '', 'ZHMGGY45', 'Trilogy趣乐活玫瑰果油45ml', '0', '玫瑰果油', '16', '130', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('416', '', 'YFBC', '淘宝邮费补差', '0', '邮费补差', '0', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('417', '', 'ZHHWXL38', '日本花王婴儿拉拉裤加大号XL 38片', '0', '花王纸尿裤XL', '4', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('418', '', 'ZHHWL44', '日本花王婴儿纸尿裤L 44片', '0', '花王纸尿裤L', '4', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('419', '', 'ZHHWM64', '日本花王 婴儿纸尿裤M 64片', '0', '花王纸尿裤M', '4', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('420', '', 'ZHHWS82', '日本花王婴儿纸尿裤S 82片', '0', '花王纸尿裤S', '4', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('421', '', 'ZHHWS19', '花王卫生巾S系列25cm*19', '0', null, '0', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('422', '', 'ZHHWS15', '花王卫生巾S系列30cm*15', '0', null, '0', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('423', '', 'ZHHWS13', '花王卫生巾S系列35cm*13', '0', null, '0', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('424', '', 'ZHHWS28', '花王卫生巾S系列20.5cm*28', '0', null, '0', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('425', '', 'ZHHWHD80', '花王护垫14cm*80', '0', null, '0', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('426', '', 'ZHHWF7', '花王卫生巾F系列40cm*7', '0', null, '0', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('427', '', 'ZHHWF20', '花王卫生巾F系列22.5cm*20', '0', null, '0', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('428', '', 'ZHHWF17', '花王卫生巾F系列 25cm*17', '0', null, '0', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('429', '', 'MFYY450', '洗洁精原味 450ml', '0', '洗洁精原味', '1', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('430', '', 'MFKJ450', '洗洁精抗菌 450ml', '0', '洗洁精抗菌', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('431', '', 'MFNM450', '洗洁精柠檬味 450ml', '0', '洗洁精柠檬味', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('432', '', 'MFAC450', '洗洁精澳橙味 450ml', '0', '洗洁精澳橙味', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('433', '', 'MFQN450', '洗洁精青柠味 450ml', '0', '洗洁精青柠味', '1', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('434', '9347832000657', 'HSS2603900', '惠氏S26金装3段900g', '0', 'S3', '29', '1100', '3', '1', '0', '21', '0');
INSERT INTO `aq_item` VALUES ('435', '', 'ZHXKW50', 'breath pearls清新草本香口丸 50粒', '0', '香口丸', '9', '30', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('436', '', 'SWXZ300', 'swisse温泉水萃取小黄瓜 卸妆液 300ml', '0', 'SW卸妆液', '15', '350', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('437', '', 'ZHCN60', 'Herbs of Gold催奶片 60粒', '0', '催奶片', '9', '200', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('438', '', 'GMSYBS250', 'GM 山羊奶保湿霜 250g', '0', '山羊奶保湿霜', '12', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('439', '', 'BIODHA601', 'bioisland 孕妇DHA 60粒', '0', 'bio孕妇DHA', '8', '70', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('440', '', 'ZHSGZ10', 'Eaoron涂抹式水光针蛋白霜10ml 第一代', '0', '水光针第一代', '9', '40', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('441', '', 'RSYG105', '红印儿童牙膏 75g', '0', '儿童牙膏', '10', '90', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('442', '', 'ZHQWG500', 'Gumption清洁去污膏 500g', '0', '去污膏', '4', '555', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('443', '', 'ZHSGZ101', 'Eaoron涂抹式水光针蛋白霜10ml  第二代', '0', '水光针第二代', '16', '45', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('444', '', 'BIOZZS90', 'BIO island 儿童助长素2段 90粒', '0', 'bio助长素', '8', '115', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('445', '', 'ZHARSFS60', '日本资生堂安热沙小金瓶防晒霜 60ml', '0', '安热沙防晒', '16', '90', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('446', '9345850006293', 'ZHOZ900', 'Oz Farm澳滋孕妇奶粉900g', '0', '孕妇奶粉', '17', '1000', '3', '1', '0', '27', '0');
INSERT INTO `aq_item` VALUES ('447', '', 'ZHLMNP300', '美可卓蓝莓护眼牛奶片 300g', '0', '美可卓奶片', '9', '360', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('448', '', 'ZHTMG75', 'NAIR无痛温和身体脱毛膏 75g', '0', '脱毛膏', '16', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('449', '', 'YSJYF60', 'Life Space孕妇益生菌 60粒', '0', '孕妇益生菌', '9', '150', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('450', '', 'ZHGJS100', 'Dencorub 关节霜 100g', '0', '关节霜', '9', '125', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('451', '', 'BIOXS500', 'Bio E柠檬味酵素原液500ml', '0', '酵素柠檬味', '8', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('452', '', 'SWGNQ60', 'Swisse 更年期平衡营养素 60片', '0', 'SW更年期片', '15', '136', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('453', '', 'ZHBTJ200', 'Centrum善存儿童补铁剂樱桃味 200ml', '0', '善存补铁剂', '9', '445', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('454', '', 'ZHCM200', 'Bubba Blue 植物除螨喷雾 200ml', '0', '除螨喷雾', '4', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('455', '', 'ZHSFS250', 'NATIO娜迪奥香薰甘菊玫瑰水爽肤水250ml', '0', 'NATIO爽肤水', '16', '305', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('456', '9347832000633', 'HSS2602900', '惠氏S26金装2段900g', '0', 'S2', '29', '1100', '3', '1', '0', '20', '0');
INSERT INTO `aq_item` VALUES ('457', '', 'ZHZHL80', 'Ego QV naked 滚珠止汗露 80g', '0', '止汗露', '9', '110', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('458', '', 'ZHNX4301', 'Fatblaster代餐摩卡味奶昔 430g', '0', '摩卡奶昔', '9', '530', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('459', '', 'ZHNX4302', 'Fatblaster代餐香草味奶昔 430g', '0', '香草奶昔', '9', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('460', '', 'ZHNX4303', 'Fatblaster代餐巧克力味奶昔 430g', '0', '巧克力奶昔', '9', '500', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('461', '', 'ZHJJP16', 'HYDRODOL  解酒片（黑盒） 16片', '0', 'HYDRODOL 解酒片', '9', '20', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('462', '', 'ZHWCDJ10', '可瑞康VC儿童维他命VC滴剂 10ml', '0', '维C滴剂', '9', '60', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('463', '', 'ZHOZLNR900', 'Oz Farm老年人奶粉', '0', 'Oz Farm中老年奶粉', '17', '1100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('464', '', 'ZHYTS50', 'healthy care 羊胎素 50ml', '0', '羊胎素', '16', '150', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('465', '', 'NWDBF3751', '佳思敏原味蛋白粉 375g', '0', '原味蛋白粉', '7', '460', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('466', '', 'NWDBF3752', '佳思敏巧克力味蛋白粉 375g', '0', '巧克力蛋白粉', '7', '460', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('468', '9418783003414', 'AP002900', 'Aptamil-白金婴儿奶粉2段900g', '0', 'P2', '28', '1100', '3', '1', '2', '6', '0');
INSERT INTO `aq_item` VALUES ('469', '9418783003421', 'AP003900', 'Aptamil-白金婴儿奶粉3段900g', '0', 'P3', '28', '1100', '6', '1', '3', '7', '0');
INSERT INTO `aq_item` VALUES ('470', '9418783003438', 'AP004900', 'Aptamil-白金婴儿奶粉4段900g', '0', 'P4', '28', '1100', '6', '1', '4', '8', '0');
INSERT INTO `aq_item` VALUES ('471', '', 'ZHHZJ30', 'Daktarin 进口灰指甲水 30ml', '0', '灰指甲水', '9', '75', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('472', '', 'ZHWNY60', 'BIO OIL 万能油 60ml', '0', '万能油 60ml', '16', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('473', '9347832000619 ', 'HSS2601900', '惠氏S26金装1段900g', '0', 'S1', '29', '1100', '3', '1', '0', '19', '0');
INSERT INTO `aq_item` VALUES ('474', '9347832000671', 'HSS2604900', '惠氏S26金装4段900g', '0', 'S4', '29', '1100', '3', '1', '0', '22', '0');
INSERT INTO `aq_item` VALUES ('475', '', 'ZHETYY60', 'Herbs of Gold儿童无腥味鱼油香草味 60粒', '0', 'HOG儿童鱼油', '0', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('476', '', 'ZHYXY60', 'Herbs of Gold银杏叶片6000mg 60粒', '0', '银杏叶片', '0', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('477', '', 'ZHDCWSS60', 'Herbs of Gold儿童多重维生素片草莓香草味  60粒', '0', '儿童多重维生素片', '0', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('478', '', 'ZHPFXF60', 'Herbs of Gold祛痘防痘皮肤清洁修复片 60粒', '0', '皮肤修复片', '0', '300', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('479', '', 'HCYNP301', 'HC 纯天然羊奶片咀嚼片 香草味300粒', '0', '香草羊奶片', '13', '250', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('480', '', 'HCFM100', 'Healthy Care辅酶Q10软胶囊150mg 100粒', '0', 'HC辅酶Q10', '13', '200', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('481', '', 'MHSUKIN', 'Sukin洗面奶乳液水三件套', '0', 'sukin三件套', '0', '565', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('482', '', 'BMHY30', 'Blackmores越橘蓝莓素护眼精华 30片', '0', 'BM护眼精华', '0', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('483', '', 'NWDBF3753', '佳思敏香草味蛋白粉 375g', '0', '香草蛋白粉', '7', '460', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('484', '', 'ZHMGJN60', 'unichi澳洲玫瑰果精华胶囊 60粒', '0', '玫瑰果胶囊', '16', '85', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('485', '', 'ZHSHJ60', 'Unichi澳洲生蚝精华胶囊 60粒', '0', '生蚝精华胶囊', '9', '75', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('486', '', 'HCVC500', 'Healthy Care 维生素C咀嚼片 500粒', '0', 'HC维生素C', '13', '605', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('487', '', 'NWNP151', '佳思敏儿童高钙奶片150粒 巧克力味', '0', '佳思敏奶片巧克力', '7', '365', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('488', '', 'NWNP150', '佳思敏儿童高钙奶片150粒 蜂蜜味', '0', '佳思敏奶片蜂蜜', '7', '365', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('489', '', 'NWNP152', '佳思敏儿童高钙奶片150粒 含DHA', '0', '佳思敏奶片DHA', '7', '365', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('490', '9345850009751', 'ZHOZET900', 'OZ Farm儿童营养成长奶粉900g', '0', 'OZ farm儿童奶粉', '17', '1100', '3', '1', '0', '26', '0');
INSERT INTO `aq_item` VALUES ('491', '', 'ZHHT250', '德国儿童铁元 补血 250ml', '0', '儿童铁元', '0', '615', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('492', '8710428004376', 'ZHXAS850', '雅培金装小安素婴幼儿奶粉850g', '0', '小安素', '17', '10000', '3', '1', '1', '31', '0');
INSERT INTO `aq_item` VALUES ('493', '', 'ZHGMX474', '美国童年时光CHILDLIFE钙镁锌 474ml', '0', '钙镁锌', '9', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('494', '9326847001286', 'THMP', '澳洲土豪麦片  1.3kg', '0', '麦片', '5', '1.3', '1', '1', '1', '1000', '0');
INSERT INTO `aq_item` VALUES ('495', '', 'GSJG100', '羊奶皂摩洛哥坚果味 100g', '0', '羊皂坚果', '16', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('496', '', 'GSYZ100', '羊奶皂椰子味 100g', '0', '羊皂椰子', '16', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('497', '', 'GSET100', '儿童羊奶皂 100g', '0', '儿童羊皂', '16', '100', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('498', '9344949000174', 'ZGMKZ1001', 'Maxigenes-成人奶粉脱脂', '0', 'MAX', '17', '1000', '6', '1', '0', '25', '0');
INSERT INTO `aq_item` VALUES ('499', '9421902960062', 'A204900', 'A2-婴儿奶粉4段900g', '0', 'A4', '21', '900', '3', '1', '4', '18', '0');
INSERT INTO `aq_item` VALUES ('500', '', 'ZHOZTZ900', 'OZ Farm 脱脂奶粉', '0', 'OZ Farm 脱脂奶粉', '17', '0', '1', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('501', 'sd', 'ds', 'test', '1519873781', 'sd', '14', '11', '0', '0', '0', '1000', '0');
INSERT INTO `aq_item` VALUES ('502', '9418783002820', 'A001900', 'Aptamil-金装婴儿奶粉1段900g', '1519873816', 'K1', '25', '900', '6', '1', '1', '1', '0');
INSERT INTO `aq_item` VALUES ('504', '9418783003407', 'AP001900', 'Aptamil-白金婴儿奶粉1段900g', '1520064013', 'P1', '28', '900', '3', '1', '1', '5', '0');
INSERT INTO `aq_item` VALUES ('510', '9418783002967', 'ATHA900', '爱他美HA适度水解奶粉900g', '1521096966', '适度', '25', '0', '3', '1', '1', '28', '0');
INSERT INTO `aq_item` VALUES ('511', '9418783003162', 'AT001900', '爱他美深度水解1段900g', '1521097044', '深度1', '25', '0', '3', '1', '1', '29', '0');
INSERT INTO `aq_item` VALUES ('512', '99418783003179', 'AT002900', '爱他美深度水解2段900g', '1521097543', '深度2', '25', '0', '3', '1', '2', '30', '0');
INSERT INTO `aq_item` VALUES ('513', '', '', 'A2-成人奶粉脱脂', '1523356404', 'A2B', '17', '0', '0', '0', '0', '32', '0');
INSERT INTO `aq_item` VALUES ('514', '', '', '贝拉米米米糊', '1523504481', '米糊', '22', '0', '0', '1', '0', '33', '0');
INSERT INTO `aq_item` VALUES ('515', '9345850006293', 'ZHOZMM900', 'Oz Farm澳滋妈妈奶粉900g', '1526288839', '妈妈奶粉', '17', '1000', '3', '0', '0', '40', '0');
INSERT INTO `aq_item` VALUES ('516', '8710428004413 ', 'ZGYP850', '雅培糖尿病奶粉850g  ', '1527143994', '糖尿病奶粉', '17', '850', '3', '1', '0', '32', '0');
INSERT INTO `aq_item` VALUES ('517', '', 'A2YF900', 'A2孕妇奶粉900g', '1527836823', 'A2孕妇奶粉', '21', '1100', '3', '0', '0', '25', '0');
INSERT INTO `aq_item` VALUES ('518', '9421902960413', 'A2FM400', 'A2麦卢卡蜂蜜奶粉400g', '1530691401', 'A2蜂蜜奶粉', '17', '400', '6', '1', '1', '41', '0');

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_item_type
-- ----------------------------
INSERT INTO `aq_item_type` VALUES ('1', '日常洗洁精', 'RCMF', '', '1', '0', '99');
INSERT INTO `aq_item_type` VALUES ('2', '美护FF', 'MHFF', '', '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('3', '美护蜂毒', 'MHRN', '', '1', '0', '80');
INSERT INTO `aq_item_type` VALUES ('4', '日常杂货', 'RCZH', '', '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('5', '零食杂货', 'LSZH', '', '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('6', '保健嘉宝', 'BJGB', '', '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('7', '保健佳思敏', '	\r\nBJNW', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('8', '保健BIO', 'BJBIO', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('9', '保健杂货', 'BJZH', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('10', '日常红印', 'RCRS', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('11', '保健红印', 'BJRS', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('12', '美护GM', 'MHGM', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('13', '保健HC', '	\r\nBJHC', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('14', '保健BM', 'BJBM', '', '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('15', '保健SW', 'BJSW', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('16', '美护杂货', 'MHZH', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('17', '【奶粉】成人奶粉', 'NFCR', '', '1', '0', '7');
INSERT INTO `aq_item_type` VALUES ('18', '奶粉荷兰美素', 'NFHLMS', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('19', '奶粉牛栏', 'NFN', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('20', '奶粉德爱', 'NFDA', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('21', '【奶粉】A2白金', 'NFA2', '', '1', '0', '5');
INSERT INTO `aq_item_type` VALUES ('22', '【奶粉】贝拉米', 'NFB', '', '1', '0', '4');
INSERT INTO `aq_item_type` VALUES ('23', '【奶粉】可瑞康羊奶', 'NFKG', '', '1', '0', '3');
INSERT INTO `aq_item_type` VALUES ('25', '【奶粉】可瑞康爱他美', '	NFK', '', '1', '0', '1');
INSERT INTO `aq_item_type` VALUES ('26', '默认类别', 'DEFAULT', null, '1', '0', '100');
INSERT INTO `aq_item_type` VALUES ('28', '【奶粉】爱他美铂金', 'NFP', '', '1', '0', '2');
INSERT INTO `aq_item_type` VALUES ('29', '【奶粉】惠氏S26金装', 'NFHS', '', '1', '0', '6');

-- ----------------------------
-- Table structure for aq_log
-- ----------------------------
DROP TABLE IF EXISTS `aq_log`;
CREATE TABLE `aq_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `info` text COMMENT '详情',
  `controller` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `link` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20469 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_log
-- ----------------------------
INSERT INTO `aq_log` VALUES ('20468', '3', '1532077570', '[info]', 'User', 'changepassword', '');

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
-- Records of aq_module
-- ----------------------------
INSERT INTO `aq_module` VALUES ('20', '用户', 'admin', 'User', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('21', '用户-删除', 'admin', 'User', 'del', '1', '1');
INSERT INTO `aq_module` VALUES ('22', '用户-修改自己', 'admin', 'User', 'edit', '2', '1');
INSERT INTO `aq_module` VALUES ('23', '用户-修改用户组', 'admin', 'User', 'updateGroup', '3', '1');
INSERT INTO `aq_module` VALUES ('24', '用户-修改用户有效', 'admin', 'User', 'changeValid', '4', '1');
INSERT INTO `aq_module` VALUES ('25', '用户-修改用户密码', 'admin', 'User', 'changePassword', '5', '1');
INSERT INTO `aq_module` VALUES ('27', '用户-新增', 'admin', 'User', 'add', '7', '1');
INSERT INTO `aq_module` VALUES ('30', '用户组', 'admin', 'UserGroup', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('31', '用户组-新增', 'admin', 'UserGroup', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('32', '用户组-修改', 'admin', 'UserGroup', 'edit', '2', '1');
INSERT INTO `aq_module` VALUES ('33', '用户组-删除', 'admin', 'UserGroup', 'del', '3', '1');
INSERT INTO `aq_module` VALUES ('50', '仓库采购单', 'admin', 'BuyIn', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('51', '采购单-新建', 'admin', 'BuyIn', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('52', '采购单-删除', 'admin', 'BuyIn', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('53', '采购单-审核', 'admin', 'BuyIn', 'checkOk', '3', '1');
INSERT INTO `aq_module` VALUES ('54', '采购单-修改', 'admin', 'BuyIn', 'edit', '4', '1');
INSERT INTO `aq_module` VALUES ('60', '仓库入库单', 'admin', 'BuyInStore', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('61', '入库单-新建', 'admin', 'BuyInStore', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('62', '入库单-废弃', 'admin', 'BuyInStore', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('63', '入库单-审核', 'admin', 'BuyInStore', 'checkOk', '3', '1');
INSERT INTO `aq_module` VALUES ('64', '入库单-修改', 'admin', 'BuyInStore', 'edit', '4', '1');
INSERT INTO `aq_module` VALUES ('70', '仓库出库单', 'admin', 'BuyOut', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('71', '出库单-新建', 'admin', 'BuyOut', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('72', '出库单-废弃', 'admin', 'BuyOut', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('73', '出库单-审核', 'admin', 'BuyOut', 'checkOk', '3', '1');
INSERT INTO `aq_module` VALUES ('74', '出库单-修改', 'admin', 'BuyOut', 'edit', '4', '1');
INSERT INTO `aq_module` VALUES ('150', '商店', 'admin', 'Shop', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('151', '商店-新建', 'admin', 'Shop', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('152', '商店-删除', 'admin', 'Shop', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('153', '商店-修改', 'admin', 'Shop', 'edit', '3', '1');
INSERT INTO `aq_module` VALUES ('160', '商店类型', 'admin', 'ShopType', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('161', '商店类型-新建', 'admin', 'ShopType', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('162', '商店类型-删除', 'admin', 'ShopType', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('163', '商店类型-修改', 'admin', 'ShopType', 'edit', '3', '1');
INSERT INTO `aq_module` VALUES ('170', '仓库', 'admin', 'Store', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('171', '仓库-新建', 'admin', 'Store', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('172', '仓库-删除', 'admin', 'Store', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('173', '仓库-修改', 'admin', 'Store', 'edit', '3', '1');
INSERT INTO `aq_module` VALUES ('174', '仓库-修改审核规则', 'admin', 'Store', 'updateCheckRule', '4', '1');
INSERT INTO `aq_module` VALUES ('180', '调货单', 'admin', 'StoreChange', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('181', '调货单-新建', 'admin', 'StoreChange', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('182', '调货单-删除', 'admin', 'StoreChange', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('183', '调货单-修改', 'admin', 'StoreChange', 'edit', '3', '1');
INSERT INTO `aq_module` VALUES ('184', '调货单-审核', 'admin', 'StoreChange', 'checkOk', '4', '1');
INSERT INTO `aq_module` VALUES ('190', '仓库商品', 'admin', 'StoreItem', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('191', '仓库商品-导出', 'admin', 'StoreItem', 'exportCsv', '1', '1');
INSERT INTO `aq_module` VALUES ('200', '系统配置', 'admin', 'Config', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('201', '系统配置-修改', 'admin', 'Config', 'edit', '1', '1');
INSERT INTO `aq_module` VALUES ('320', '商品', 'admin', 'Item', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('321', '商品-新加', 'admin', 'Item', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('322', '商品-删除', 'admin', 'Item', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('323', '商品-修改', 'admin', 'Item', 'edit', '3', '1');
INSERT INTO `aq_module` VALUES ('324', '商品-修改最小约数', 'admin', 'Item', 'updateSellValue', '4', '1');
INSERT INTO `aq_module` VALUES ('325', '商品-修改审核约束开关', 'admin', 'Item', 'updateCheckLimit', '5', '1');
INSERT INTO `aq_module` VALUES ('330', '商品类别', 'admin', 'ItemType', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('331', '商品类别-新增', 'admin', 'ItemType', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('332', '商品类别-删除', 'admin', 'ItemType', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('333', '商品类别-修改', 'admin', 'ItemType', 'edit', '3', '1');
INSERT INTO `aq_module` VALUES ('400', '配货单', 'admin', 'SellAssign', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('401', '配货单-配货', 'admin', 'SellAssign', 'assignItem', '1', '1');
INSERT INTO `aq_module` VALUES ('402', '配货单-废除', 'admin', 'SellAssign', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('403', '配货单-修改', 'admin', 'SellAssign', 'edit', '3', '1');
INSERT INTO `aq_module` VALUES ('404', '配货单-导出', 'admin', 'SellAssign', 'exportCsv', '4', '1');
INSERT INTO `aq_module` VALUES ('500', '销售单', 'admin', 'Sell', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('501', '销售单-审核', 'admin', 'Sell', 'checkOk', '1', '1');
INSERT INTO `aq_module` VALUES ('502', '销售单-废弃', 'admin', 'Sell', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('503', '销售单-导入', 'admin', 'Sell', 'importData', '3', '1');
INSERT INTO `aq_module` VALUES ('504', '销售单-反审核', 'admin', 'Sell', 'checkNo', '4', '1');
INSERT INTO `aq_module` VALUES ('505', '销售单-修改', 'admin', 'Sell', 'update', '5', '1');
INSERT INTO `aq_module` VALUES ('506', '销售单-导出', 'admin', 'Sell', 'exportCsv', '6', '1');
INSERT INTO `aq_module` VALUES ('508', '销售单-修改跟单员', 'admin', 'Sell', 'updateTrackMan', '8', '1');
INSERT INTO `aq_module` VALUES ('509', '销售单-合单', 'admin', 'Sell', 'mergeOrder', '9', '1');
INSERT INTO `aq_module` VALUES ('510', '销售单-批量合单', 'admin', 'Sell', 'mergeAllOrder', '10', '1');
INSERT INTO `aq_module` VALUES ('511', '销售单-拆单', 'admin', 'Sell', 'splitOrder', '11', '1');
INSERT INTO `aq_module` VALUES ('512', '销售单-批量拆单', 'admin', 'Sell', 'splitAllOrder', '12', '1');
INSERT INTO `aq_module` VALUES ('513', '销售单-同步物流到商城', 'admin', 'Sell', 'syncOrderShipNum', '1', '1');
INSERT INTO `aq_module` VALUES ('514', '销售单-批量修改物流', 'admin', 'Sell', 'importChangeShipnum', '2', '1');
INSERT INTO `aq_module` VALUES ('600', '表格导出配置', 'admin', 'Export', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('601', '表格导出配置-新增', 'admin', 'Export', 'add', '1', '1');
INSERT INTO `aq_module` VALUES ('602', '表格导出配置-修改', 'admin', 'Export', 'update', '2', '1');
INSERT INTO `aq_module` VALUES ('603', '表格导出配置-删除', 'admin', 'Export', 'del', '3', '1');
INSERT INTO `aq_module` VALUES ('700', '数据库备份-所有', 'admin', 'Database', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('701', '数据库备份-保存', 'admin', 'Database', 'save', '1', '1');
INSERT INTO `aq_module` VALUES ('702', '数据库备份-删除', 'admin', 'Database', 'del', '2', '1');
INSERT INTO `aq_module` VALUES ('703', '数据库备份-修改', 'admin', 'Database', 'update', '3', '1');
INSERT INTO `aq_module` VALUES ('704', '数据库备份-恢复', 'admin', 'Database', 'restore', '4', '1');
INSERT INTO `aq_module` VALUES ('750', '入库明细', 'admin', 'InStoreLog', 'all', '0', '1');
INSERT INTO `aq_module` VALUES ('751', '入库明细-导出', 'admin', 'InStoreLog', 'exportCsv', '1', '1');

-- ----------------------------
-- Table structure for aq_sell
-- ----------------------------
DROP TABLE IF EXISTS `aq_sell`;
CREATE TABLE `aq_sell` (
  `id` varchar(255) NOT NULL COMMENT ' 销售订单编号',
  `check_user` int(11) DEFAULT NULL COMMENT '审核人',
  `check_time` int(11) DEFAULT NULL COMMENT '审核时间',
  `status` tinyint(3) DEFAULT '0' COMMENT '审核状态',
  `build_user` int(11) DEFAULT NULL COMMENT '创建者',
  `build_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `refund_status` tinyint(4) DEFAULT '0' COMMENT '退款状态',
  `pay_status` tinyint(3) DEFAULT '0' COMMENT '支付状态',
  `shop_id` int(3) DEFAULT NULL COMMENT '商店编号',
  `shop_order` varchar(255) NOT NULL COMMENT '平台订单编号',
  `customer_name` varchar(255) DEFAULT NULL COMMENT '收货人',
  `customer_addr` varchar(255) DEFAULT NULL COMMENT '收货地址',
  `pay_time` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL COMMENT '分配的仓库id',
  `num` int(11) DEFAULT NULL COMMENT '数量',
  `user_info` text COMMENT '买家备注',
  `customer_account` varchar(255) DEFAULT NULL COMMENT '买家账号',
  `discount` double DEFAULT NULL COMMENT '折扣',
  `info` text COMMENT '备注',
  `user_phone` varchar(255) DEFAULT NULL COMMENT '手机号',
  `pay_money` double DEFAULT NULL COMMENT '支付金',
  `del_info` text COMMENT '废弃原因',
  `sell_type` tinyint(1) DEFAULT '0' COMMENT '订单标识',
  `logistics` varchar(255) DEFAULT NULL COMMENT '物流单号',
  `track_man` varchar(255) DEFAULT NULL COMMENT '跟单员',
  `sell_vip_type` tinyint(4) DEFAULT '0' COMMENT '订单vip标识',
  `sell_vip_info` text COMMENT '订单vip信息',
  `logistics_merge` varchar(255) DEFAULT '' COMMENT '合并的物流单号',
  `order_time` int(11) DEFAULT NULL COMMENT '客户下单时间',
  `user_id_number` varchar(255) DEFAULT '' COMMENT '身份证',
  `customer_province` varchar(255) DEFAULT NULL,
  `customer_city` varchar(255) DEFAULT NULL,
  `customer_area` varchar(255) DEFAULT NULL COMMENT '区',
  `send_user_name` varchar(255) DEFAULT NULL COMMENT '发件人姓名',
  `send_user_phone` varchar(255) DEFAULT NULL COMMENT '发件人手机',
  `unit_price` double DEFAULT '0' COMMENT '商品单价',
  `freight_price` double DEFAULT '0' COMMENT '运费',
  `service_price` double DEFAULT '0' COMMENT '服务费',
  `freight_unit_price` double DEFAULT '0' COMMENT '单价平台运费',
  `service_unit_price` double DEFAULT '0' COMMENT '单件平均服务费',
  `merge_order` varchar(255) DEFAULT '' COMMENT '被合并的订单',
  `assign_order` varchar(255) DEFAULT '' COMMENT '分配单单号',
  PRIMARY KEY (`id`,`shop_order`),
  UNIQUE KEY `idkey` (`id`) USING HASH,
  KEY `check_user` (`check_user`),
  KEY `build_user` (`build_user`),
  KEY `itemid` (`item_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_sell
-- ----------------------------

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
-- Records of aq_sell_assign
-- ----------------------------

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
  `shop_edit_uid` int(11) DEFAULT NULL,
  `shop_edit_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_shop
-- ----------------------------

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
-- Records of aq_shop_type
-- ----------------------------
INSERT INTO `aq_shop_type` VALUES ('1', '	\r\n淘宝');
INSERT INTO `aq_shop_type` VALUES ('2', '自建');

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_store
-- ----------------------------

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
-- Records of aq_store_change
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=670 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_store_item
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of aq_sys_user
-- ----------------------------
INSERT INTO `aq_sys_user` VALUES ('1', 'system', '系统', null, '0', '', 'c20ad4d76fe97759aa27a0c99bff6710', '1', '1', '1');
INSERT INTO `aq_sys_user` VALUES ('3', 'zyx', 'zyx', '', '1518318015', '18664604926', 'e10adc3949ba59abbe56e057f20f883e', '0', '1', '3');

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

-- ----------------------------
-- Records of aq_sys_usergroup
-- ----------------------------
INSERT INTO `aq_sys_usergroup` VALUES ('1', '系统管理员', null, '1');
INSERT INTO `aq_sys_usergroup` VALUES ('2', '普通用户', '[20,22,25,30,50,60,70,150,160,170,180,190,191,200,320,330,400,401,404,500,501,503,506,509,510,511,512,600,601,602,603,700,750,751]', '0');
INSERT INTO `aq_sys_usergroup` VALUES ('3', '高级用户', '[20,21,22,23,24,25,26,27,30,31,32,33,50,51,52,53,54,60,61,62,63,64,70,71,72,73,74,150,151,152,153,160,161,162,163,170,171,172,173,174,180,181,182,183,184,190,191,200,201,320,321,322,323,324,325,330,331,332,333,400,401,402,403,404,500,501,502,503,504,505,506,508,509,510,511,512,513,514,600,601,602,603,700,701,702,703,704,750,751]', '0');
