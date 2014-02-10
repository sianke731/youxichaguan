CREATE TABLE  `yx_pages` (
`id` INT( 10 ) NOT NULL AUTO_INCREMENT ,
`title` VARCHAR( 255 ) NOT NULL ,
`keywords` VARCHAR( 255 ) NOT NULL ,
`description` VARCHAR( 500 ) NOT NULL ,
`content` TEXT NOT NULL ,
`uid` INT( 10 ) NOT NULL ,
`ctime` INT( 10 ) NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  '单页表';