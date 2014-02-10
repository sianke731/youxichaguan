<?php
// 升级入口方法
function updateDB () {
	DBchang();
	updateVersion();
}

function DBchang () {
	$installfile = DATA_PATH.'/update/download/unzip/updateDB.sql';
	M()->executeSqlFile($installfile, false);
}

// 更新版本号
function updateVersion () {
	// 更新后台的JS版本
	$info = model('Xdata')->get('admin_Config:site');
	$info['sys_version'] = '2013110801';
	model('Xdata')->put('admin_Config:site', $info);	
	// 记录本次版本号
	model('Xdata')->put('update:version', '2013110801');
}
