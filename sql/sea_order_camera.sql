/*
Navicat MySQL Data Transfer

Source Server         : 172
Source Server Version : 50163
Source Host           : 115.28.4.172:3306
Source Database       : sea

Target Server Type    : MYSQL
Target Server Version : 50163
File Encoding         : 65001

Date: 2016-09-04 23:00:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sea_order_camera
-- ----------------------------
DROP TABLE IF EXISTS `sea_order_camera`;
CREATE TABLE `sea_order_camera` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `video_code` varchar(255) DEFAULT '0' COMMENT '视频标号（腾讯直播）',
  `video_id` int(11) DEFAULT '0' COMMENT '视频接口表中的自增ID',
  `title` varchar(50) DEFAULT NULL COMMENT '视频标题',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单视频表';

-- ----------------------------
-- Records of sea_order_camera
-- ----------------------------
