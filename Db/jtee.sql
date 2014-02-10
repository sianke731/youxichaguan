-- 添加审核字段
ALTER TABLE `yx_event` ADD `is_verify` TINYINT( 1 ) NOT NULL COMMENT '0未审核，1审核' AFTER `feed_id` ;

-- 修改活动标题类型
ALTER TABLE `yx_event` CHANGE `title` `title` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '活动标题';
 
-- 添加交通路线和注意事项
ALTER TABLE `yx_event` ADD `traffic` VARCHAR( 500 ) NOT NULL COMMENT '交通路线' AFTER `is_verify` ,
ADD `notice` TINYTEXT NOT NULL COMMENT '注意事项' AFTER `traffic` ;

-- 添加主办方、承办方
ALTER TABLE `yx_event` ADD `sponsor` VARCHAR( 200 ) NOT NULL COMMENT '主办方' AFTER `notice` ,
ADD `organizer_type` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1承办方 2协办方 默认为1' AFTER `sponsor` ,
ADD `organizer` VARCHAR( 500 ) NOT NULL COMMENT '承办方' AFTER `organizer_type` ;

-- 添加置顶、推荐
ALTER TABLE `yx_event` ADD `is_recommend` TINYINT( 1 ) NOT NULL COMMENT '0未推荐 1推荐' AFTER `is_verify` ,
ADD `is_top` TINYINT( 1 ) NOT NULL COMMENT '0未置顶　1置顶' AFTER `is_recommend` ;

-- 添加活动嘉宾表
CREATE TABLE `yx_event_guest` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`event_id` INT( 10 ) NOT NULL ,
`name` VARCHAR( 20 ) NOT NULL ,
`position` VARCHAR( 50 ) NOT NULL ,
`avatar` INT( 10 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = '活动嘉宾表';

-- 添加活动时间表
CREATE TABLE `yx_event_times` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`event_id` INT( 10 ) NOT NULL ,
`sTime` INT( 10 ) NOT NULL ,
`eTime` INT( 10 ) NOT NULL ,
`description` VARCHAR( 500 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = '活动时间表' ;

-- 更改文章分类字段类型
ALTER TABLE `yx_news` CHANGE `type_id` `type_id` VARCHAR( 100 ) NULL DEFAULT '0' COMMENT '分类ID';

-- 添加意见反馈表
CREATE TABLE  `yx_suggest` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`uid` INT( 11 ) NOT NULL ,
`name` VARCHAR( 50 ) NOT NULL ,
`email` VARCHAR( 50 ) NOT NULL ,
`message` TEXT NOT NULL ,
`ctime` INT( 11 ) NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;