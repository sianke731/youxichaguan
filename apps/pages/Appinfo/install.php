<?php
/**
 * 安装单页应用
 * @author JTee <sianke731@126.com>
 * @version TS3.0
 */
if (! defined('SITE_PATH'))
{
    exit();
}

//先卸载
include_once(APPS_PATH.'/pages/Appinfo/uninstall.php');
    
// SQL文件
$sql_file = APPS_PATH . '/pages/Appinfo/install.sql';
$res = D('')->executeSqlFile($sql_file);
if(!empty($res))
{
	echo $res['error_code'];
	echo '<br />';
	echo $res['error_sql'];
	//清除已导入的数据
	include_once(APPS_PATH.'/pages/Appinfo/uninstall.php');
	exit;
}

//生成语言缓存
model('Lang')->createCacheFile('PUBLIC',0);