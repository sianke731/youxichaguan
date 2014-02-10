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
