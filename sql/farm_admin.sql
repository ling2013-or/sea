/*
Navicat MySQL Data Transfer

Source Server         : locahost
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : farm

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-07-16 23:55:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for farm_admin
-- ----------------------------
DROP TABLE IF EXISTS `farm_admin`;
CREATE TABLE `farm_admin` (
  `admin_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `group_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户组ID',
  `admin_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '管理员身份 1普通 2超管',
  `admin_name` varchar(36) NOT NULL COMMENT '管理员用户名',
  `admin_pwd` char(32) NOT NULL COMMENT '用户密码',
  `admin_salt` char(16) NOT NULL COMMENT '密码盐参',
  `true_name` varchar(24) NOT NULL DEFAULT '' COMMENT '管理员真实姓名',
  `img` varchar(256) NOT NULL DEFAULT '' COMMENT '管理员头像',
  `phone` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(52) NOT NULL DEFAULT '' COMMENT '邮箱',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '管理员状态 -1删除 1正常',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of farm_admin
-- ----------------------------
INSERT INTO `farm_admin` VALUES ('1', '1', '1', 'admin', '10bf9d3c7961b727bf60f4b11222e1ec', '49ba59abbe56e057', 'Mr Admin', '1.jpg', '15888888888', 'Mr@scbin.com', '1', '1446613130', '1446798064');
INSERT INTO `farm_admin` VALUES ('3', '22', '1', 'user', 'a4c52f19a7d3dd410e83be609433713a', '849696c9339f1917', 'Mr U', '', '18666666666', 'MRU@scbin.com', '1', '1446805194', '1448263986');
INSERT INTO `farm_admin` VALUES ('4', '1', '1', '测试管理', '98312eea16d19447a7bb9e0fb4f22a3f', '244bd73d10275201', '123', '', '123123', '123123', '-1', '1447237067', '1447237067');
INSERT INTO `farm_admin` VALUES ('5', '1', '1', '测试12', '8ba3898a63089ca945e2eccb2e816a7c', 'ddfd09fffb898823', '', '', '', '', '-1', '1447299986', '1447300672');
INSERT INTO `farm_admin` VALUES ('6', '1', '1', 'TESTER', 'b2b8c995f4bc6596b7d322c095d34fb7', '5440d933a6c3f432', '123', '', '123', '123123123', '-1', '1447749554', '1447749566');
INSERT INTO `farm_admin` VALUES ('16', '22', '1', 'test', 'ff147822b42aca86ca0b00f235874448', '09e197aff2a5f800', '123', '', '1888888888', 'admin@farm.com', '1', '1448256783', '1448263787');
