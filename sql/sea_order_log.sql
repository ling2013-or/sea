/*
Navicat MySQL Data Transfer

Source Server         : 172
Source Server Version : 50163
Source Host           : 115.28.4.172:3306
Source Database       : sea

Target Server Type    : MYSQL
Target Server Version : 50163
File Encoding         : 65001

Date: 2016-09-04 23:00:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sea_order_log
-- ----------------------------
DROP TABLE IF EXISTS `sea_order_log`;
CREATE TABLE `sea_order_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `description` varchar(150) NOT NULL DEFAULT '' COMMENT '文字描述',
  `operate_time` int(11) NOT NULL DEFAULT '0' COMMENT '处理时间',
  `operate_rule` char(2) NOT NULL DEFAULT '0' COMMENT '操作角色',
  `operate_user` varchar(30) NOT NULL DEFAULT '' COMMENT '操作人',
  `order_state` tinyint(2) NOT NULL DEFAULT '1' COMMENT '订单状态：0-已取消，1-未付款，2-已付款，3-已发货，4-已收货',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='订单处理记录表';

-- ----------------------------
-- Records of sea_order_log
-- ----------------------------
INSERT INTO `sea_order_log` VALUES ('1', '1', '提交了订单', '1447812047', '买家', '', '1');
INSERT INTO `sea_order_log` VALUES ('2', '2', '提交了订单', '1447812385', '买家', '', '1');
INSERT INTO `sea_order_log` VALUES ('5', '2', '', '1448330565', '0', 'admin', '0');
INSERT INTO `sea_order_log` VALUES ('6', '1', '', '1469169890', '0', 'admin', '0');
INSERT INTO `sea_order_log` VALUES ('7', '1', '', '1469172855', '0', 'admin', '2');
INSERT INTO `sea_order_log` VALUES ('8', '1', '', '1469361610', '0', 'admin', '2');
