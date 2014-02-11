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

 Date: 02/09/2014 20:52:54 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `yx_company_publish`
-- ----------------------------
DROP TABLE IF EXISTS `yx_company_publish`;
CREATE TABLE `yx_company_publish` (
  `company_id` int(10) NOT NULL,
  `areas` varchar(255) NOT NULL,
  `platform_require` varchar(255) NOT NULL,
  `network_require` tinyint(1) NOT NULL,
  `stage_require` tinyint(1) NOT NULL,
  `team_require` varchar(500) NOT NULL,
  `is_experience` tinyint(1) NOT NULL,
  `games` tinyint(1) NOT NULL,
  `price` tinyint(1) NOT NULL,
  `cooperation_type` varchar(20) NOT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='发行公司';

-- ----------------------------
--  Records of `yx_company_publish`
-- ----------------------------
BEGIN;
INSERT INTO `yx_company_publish` VALUES ('12', '1', '1', '1', '1', 'asdfasdf', '0', '0', '0', ''), ('11', '2', '2', '2', '2', '对团队游戏要求', '0', '1', '1', ''), ('14', '2', '1', '1', '1', '对团队游戏要求', '0', '0', '0', '');
COMMIT;

