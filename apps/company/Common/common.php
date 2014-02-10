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


//替换数组
function changeArray($list,$str,$str_replace){
	$list[$str] = $str_replace;
	return $list;
}

//替换数组
function changeNumToWords($list,$words){
	$list = explode(",",$list);
	$list = array_flip($list);
	$list = array_intersect_key($words,$list);
	$str = implode(',',$list);
	return $str;
}

//转换区域
function changeAreas($area,$areas){
	$area = explode(",",$area);
	$area = array_flip($area);
	$area = array_intersect_key($areas,$area);
	$str = implode(',',$area);
	return $str;
}

function getAreaName($area){
	$area = model('Area')->getAreaById($area);
	return $area['title'];
}
