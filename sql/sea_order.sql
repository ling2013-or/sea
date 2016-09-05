/*
Navicat MySQL Data Transfer

Source Server         : 172
Source Server Version : 50163
Source Host           : 115.28.4.172:3306
Source Database       : sea

Target Server Type    : MYSQL
Target Server Version : 50163
File Encoding         : 65001

Date: 2016-09-04 23:00:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sea_order
-- ----------------------------
DROP TABLE IF EXISTS `sea_order`;
CREATE TABLE `sea_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(50) DEFAULT '' COMMENT '订单编号',
  `uid` int(11) DEFAULT '0' COMMENT '用户ID',
  `order_price` decimal(11,2) DEFAULT '0.00' COMMENT '订单金额',
  `pay_money` decimal(11,2) DEFAULT '0.00' COMMENT '支付金额',
  `pay_type` tinyint(1) DEFAULT '0' COMMENT '支付方式 0：未支付  1：支付宝支付，2：微信支付  3：线下',
  `pay_time` int(11) DEFAULT '0' COMMENT '支付时间',
  `pay_number` varchar(50) DEFAULT NULL COMMENT '支付单号',
  `plan_month` int(2) DEFAULT '0' COMMENT '养殖周期',
  `plan_id` varchar(200) DEFAULT NULL COMMENT '养殖计划ID，逗号隔开',
  `refund_number` varchar(50) DEFAULT NULL COMMENT '退款单号',
  `refund_time` int(11) DEFAULT '0' COMMENT '退款时间',
  `order_pay_num` varchar(30) DEFAULT '0' COMMENT '平台支付账户',
  `type` tinyint(1) DEFAULT '0' COMMENT '产品类型：0：产品 1：套餐',
  `add_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `start_time` int(11) DEFAULT '0' COMMENT '开始时间',
  `end_time` int(11) DEFAULT '0' COMMENT '结束时间',
  `reciver_name` varchar(255) DEFAULT NULL COMMENT '收款人姓名',
  `reciver_tel` varchar(15) DEFAULT '' COMMENT '收货人手机号码',
  `province_id` int(11) DEFAULT '0' COMMENT '省份Id',
  `city_id` int(11) DEFAULT '0' COMMENT '城市Id',
  `address` varchar(255) DEFAULT '' COMMENT '用户详细收货地址（省市县，乡镇）',
  `breed_time` int(11) DEFAULT '0' COMMENT '养殖时间',
  `over_time` int(11) DEFAULT '0' COMMENT '完成时间',
  `order_message` varchar(255) DEFAULT NULL COMMENT '卖家留言',
  `order_status` tinyint(1) DEFAULT '0' COMMENT '订单状态 -1：删除 0：未支付 1：已取消  2：已支付 3：养殖中  :4：待评论(养殖完成)   5：已完成  6：退款中 7：已退款',
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sea_order
-- ----------------------------
INSERT INTO `sea_order` VALUES ('1', '20161109999', '2', '3.00', '3.00', '2', '1469361610', null, '0', null, null, '0', '0', '0', '0', '0', '0', '嗡嗡嗡', '18888888888', '0', '0', '', '1471948895', '0', null, '4');
INSERT INTO `sea_order` VALUES ('11', '8000000000004001', '40', '100.00', '0.00', '0', '0', null, '0', null, null, '0', '0', '0', '1473000885', '0', '0', '小会', '0', null, '22', '222', '0', '0', null, '0');
INSERT INTO `sea_order` VALUES ('12', '8000000000004001', '40', '100.00', '0.00', '0', '0', null, '0', null, null, '0', '0', '0', '1473000936', '0', '0', '小会', '0', null, '22', '222', '0', '0', null, '0');
