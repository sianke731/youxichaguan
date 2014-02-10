/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50533
 Source Host           : localhost
 Source Database       : youxichaguan

 Target Server Type    : MySQL
 Target Server Version : 50533
 File Encoding         : utf-8

 Date: 01/24/2014 16:13:00 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `yx_game_subscribe`
-- ----------------------------
DROP TABLE IF EXISTS `yx_game_subscribe`;
CREATE TABLE `yx_game_subscribe` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `cycle` int(1) DEFAULT NULL COMMENT '1/3天 2/5天',
  `platform` varchar(10) DEFAULT NULL,
  `stage` varchar(10) DEFAULT NULL,
  `tags` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

