/*
Navicat MySQL Data Transfer

Source Server         : 172
Source Server Version : 50163
Source Host           : 115.28.4.172:3306
Source Database       : sea

Target Server Type    : MYSQL
Target Server Version : 50163
File Encoding         : 65001

Date: 2016-09-04 23:00:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sea_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `sea_order_goods`;
CREATE TABLE `sea_order_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0' COMMENT '订单编号（自增Id）',
  `goods_id` int(11) DEFAULT NULL COMMENT '产品Id',
  `goods_price` decimal(11,3) DEFAULT NULL,
  `goods_num` int(11) DEFAULT '0' COMMENT '产品数量(份)',
  `goods_type` tinyint(1) DEFAULT '0' COMMENT '产品类型 0：产品  1：套餐',
  `goods_name` varchar(255) DEFAULT NULL COMMENT '产品名称',
  `goods_cover` text COMMENT '产品封面图',
  `zone_id` int(11) DEFAULT '0' COMMENT '分区ID',
  `camera_id` int(11) DEFAULT '0' COMMENT '摄像头ID',
  `plan_id` varchar(255) DEFAULT '' COMMENT '养殖计划ID',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='订单产品表';

-- ----------------------------
-- Records of sea_order_goods
-- ----------------------------
INSERT INTO `sea_order_goods` VALUES ('1', '1', '1', '8000.000', '1', '0', '不知道', '/uploads/Picture/goods/2016-07-20/ae5e9c334511b8e728c87abffa515347.jpg', '1', '1', '|1|2|4|');
INSERT INTO `sea_order_goods` VALUES ('2', '1', '2', '9000.000', '1', '0', '321', '/uploads/Picture/goods/2016-07-20/ae5e9c334511b8e728c87abffa515347.jpg', '1', '1', '|1|');
INSERT INTO `sea_order_goods` VALUES ('3', '8', '1', '100.000', '1', '0', '海参', '/uploads/Picture/goods/2016-08-28/328e86512e8f094d003da94844f098fc.jpg', '1', '0', '');
INSERT INTO `sea_order_goods` VALUES ('4', '9', '1', '100.000', '1', '0', '海参', '/uploads/Picture/goods/2016-08-28/328e86512e8f094d003da94844f098fc.jpg', '1', '0', '');
