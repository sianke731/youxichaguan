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

 Date: 02/09/2014 20:52:38 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `yx_company_channel`
-- ----------------------------
DROP TABLE IF EXISTS `yx_company_channel`;
CREATE TABLE `yx_company_channel` (
  `company_id` int(10) NOT NULL,
  `types` varchar(255) NOT NULL,
  `require` tinyint(1) NOT NULL,
  `models` varchar(255) NOT NULL,
  `user_cover` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='渠道公司';

-- ----------------------------
--  Records of `yx_company_channel`
-- ----------------------------
BEGIN;
INSERT INTO `yx_company_channel` VALUES ('1', '1', '1', '1', '1'), ('0', '1', '0', '1', '1'), ('7', '1', '2', '1', '1'), ('14', '1', '3', '1', '1,2'), ('15', '1,4,8,1,4', '1', '1', '1');
COMMIT;

