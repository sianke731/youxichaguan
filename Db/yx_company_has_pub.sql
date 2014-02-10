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

 Date: 02/09/2014 20:52:28 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `yx_company_has_pub`
-- ----------------------------
DROP TABLE IF EXISTS `yx_company_has_pub`;
CREATE TABLE `yx_company_has_pub` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `area` varchar(100) NOT NULL,
  `platform` varchar(100) NOT NULL,
  `cost` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `yx_company_has_pub`
-- ----------------------------
BEGIN;
INSERT INTO `yx_company_has_pub` VALUES ('1', '12', '490', '1,', '1,', '1', '1'), ('2', '12', '491', '1,2,', '1,2,', '6', '2'), ('5', '12', '502', '2,4', '2', '1', '已经发行的游戏');
COMMIT;

