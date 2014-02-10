<?php
//获取活动封面存储地址
function getCover($coverId,$width=100,$height=100){
	if($coverId > 0)
		$cover = model('Attach')->where("attach_id=$coverId")->find();
	if($cover){
		$cover	=	getImageUrl($cover['save_path'].$cover['save_name'],$width,$height,true);
	}else{
		$cover	=	SITE_URL.'/apps/event/_static/images/hdpic1.gif';
	}
	return $cover;
}

//转换平台
function changePlatform($plat){
	$platform = C('game_platform');
	$plats = explode(",",$plat); 
	$plats = array_flip($plats);
	$plats = array_intersect_key($platform,$plats);
	$plat_str = implode(',',$plats);
	return $plat_str;
}

//转换需求目标
function changeTargets($target){
	$need_targets = C('need_targets');
	$targets = explode(",",$target); 
	$targets = array_flip($targets);
	$targets = array_intersect_key($need_targets,$targets);
	$targets_str = implode(',',$targets);
	return $targets_str;
}
