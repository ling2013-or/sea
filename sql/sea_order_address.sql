/*
Navicat MySQL Data Transfer

Source Server         : 172
Source Server Version : 50163
Source Host           : 115.28.4.172:3306
Source Database       : sea

Target Server Type    : MYSQL
Target Server Version : 50163
File Encoding         : 65001

Date: 2016-09-04 23:00:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sea_order_address
-- ----------------------------
DROP TABLE IF EXISTS `sea_order_address`;
CREATE TABLE `sea_order_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '土地区域ID',
  `area_id` int(11) NOT NULL DEFAULT '0' COMMENT '地区ID',
  `city_id` int(11) NOT NULL DEFAULT '0' COMMENT '市ID',
  `p_id` int(11) DEFAULT NULL COMMENT '省份ID',
  `area_info` varchar(100) NOT NULL DEFAULT '' COMMENT '省市县',
  `address` varchar(500) NOT NULL DEFAULT '' COMMENT '地址',
  `telphone` varchar(40) NOT NULL DEFAULT '' COMMENT '电话',
  `company` varchar(50) NOT NULL DEFAULT '' COMMENT '公司名',
  `is_defaule` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否默认：0-否，1-是',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='发货地址信息表';

-- ----------------------------
-- Records of sea_order_address
-- ----------------------------
INSERT INTO `sea_order_address` VALUES ('1', '1', '37', '36', '1', '北京欢迎您', '北京　北京市　东城区　北京欢迎您', '13467106666', '', '0');
INSERT INTO `sea_order_address` VALUES ('2', '1', '37', '36', '1', '鸟巢附近110', '北京　北京市　东城区　鸟巢附近110', '13466666666', '', '0');
INSERT INTO `sea_order_address` VALUES ('3', '1', '41', '36', '1', '小胡同113房', '北京　北京市　朝阳区　小胡同113房', '13455555555', '', '1');
INSERT INTO `sea_order_address` VALUES ('4', '1', '37', '36', '1', '流星家园115号', '北京　北京市　东城区　流星家园115号', '18500369772', 'nihao ', '0');
INSERT INTO `sea_order_address` VALUES ('5', '1', '37', '36', '1', '马连开花221', '北京　北京市　东城区', '13467109364', '', '0');
INSERT INTO `sea_order_address` VALUES ('6', '1', '37', '36', '1', '马连开花21', '北京　北京市　东城区　马连开花21', '13467163094', '', '0');
