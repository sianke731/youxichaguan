<?php
if (!defined('SITE_PATH')) exit();

return array(
	//用户覆盖区域
	"user_cover"=>array(
		"1" => "国内",
		"2" => "港澳台",
		"3" => "韩国",
		"4" => "日本",
		"5" => "北美",
		"6" => "欧洲",
		"7" => "俄罗斯",
		"8" => "中东北非",
		"9" => "印度",
		"10" => "南美",
	),
	//渠道类型
	"channel_type"=>array(
		"1" => "广告（IOS）",
		"2" => "广告（Android）",
		"3" => "平台（Android）",
		"4" => "平台（IOS）",
		"5" => "游戏媒体",
		"6" => "手游预装",
		"7" => "其他",
	),
	//合作平台要求
	"platform"=>array(
		"1"=>"不限",
		"2"=>"IOS正版",
		"3"=>"Android",
		"4"=>"IOS 越狱",
	),
	//定价模式
	"pricing_model"=>array(
		"1"=>"CPA（按效果）",
		"2"=>"CPS（按销售）",
		"3"=>"CPM（按展示）",
		"4"=>"CPI（按安装）",
		"5"=>"CPC（按点击）",
		"6"=>"其他",
	),
	//合作方式
	"cooperation_type"=>array(
		"1"=>"独代",
		"2"=>"联运",
	),
	//合作区域
	"cooperation_area"=>array(
		"1"=>"国内",
		"2"=>"港澳台",
		"3"=>"海外",
	),
	//商店类型
	"shop_type"=>array(
		"1"=>"应用商店-IOS",
		"2"=>"应用商店-Android",
	),
	//合作游戏联网要求
	"networking"=>array(
		"1"=>"不限",
		"2"=>"单机",
		"3"=>"联网",
	),
	//合作游戏阶段要求
	"game_phase"=>array(
		"1"=>"立项",
		"2"=>"demo",
		"3"=>"测试",
		"4"=>"已上线",
	),
	//曾发行游戏数
	"has_game_num"=>array(
		"1"=>"1~3个",
		"2"=>"4~6个",
		"3"=>"7~10个",
		"4"=>"10~20个",
		"5"=>"20个以上",
	),
	//曾代理费用
	"has_cost"=>array(
		"1"=>"100万以内",
		"2"=>"200~400万",
		"3"=>"400~600万",
		"4"=>"600~800万",
		"5"=>"800~1000万",
		"6"=>"1000~1300万",
		"7"=>"1300~1500万",
		"8"=>"2000万以上",
	),
		
	//外包类型
	"outsourcing_type"=>array(
		"1"=>"产品整包",
		"2"=>"美术外包",
		"3"=>"音乐外包",
		"4"=>"测试外包",
		"5"=>"程序外包",
		"6"=>"企划外包",
	),
	//产品外包
	"product_outsourcing"=>array(
		"1"=>"手游-IOS",
		"2"=>"手游-Android",
		"3"=>"手游-其他",
	),
	//美术外包
	"arts_outsourcing"=>array(
		"1"=>"原画设计",
		"2"=>"海报设计",
		"3"=>"2D角色",
		"4"=>"2D场景",
		"5"=>"3D角色",
		"6"=>"3D场景",
		"7"=>"UI设计",
		"8"=>"特效制作",
		"9"=>"ICON图标",
		"10"=>"CG动画",
		"11"=>"像素画",
		"12"=>"其他",
	),
	//音乐外包
	"music_outsourcing"=>array(
		"1"=>"音乐歌曲",
		"2"=>"游戏音效",
		"3"=>"动画配音",
		"4"=>"声优录制",
		"5"=>"其他",
	),
	//测试外包
	"test_outsourcing"=>array(
		"1"=>"功能测试",
		"2"=>"压力测试",
		"3"=>"体验测试",
		"4"=>"用户数据测试",
		"5"=>"环境测试",
		"6"=>"其他",
	),
	//程序外包
	"program_outsourcing"=>array(
		"1"=>"解决方案（套件）",
		"2"=>"技术支持",
		"3"=>"模块外包",
		"4"=>"算法外包",
		"5"=>"其他",
	),
	//企划外包
	"plan_outsourcing"=>array(
		"1"=>"项目计划书",
		"2"=>"投资方案",
		"3"=>"设计方案",
		"4"=>"世界观设计",
		"5"=>"剧情任务",
		"6"=>"系统方案设计",
		"7"=>"数值设计",
		"8"=>"数据分析",
		"9"=>"其他",
		),
	//主投游戏阶段
	"investment_phase"=>array(
		"1"=>"种子期",
		"2"=>"初创期",
		"3"=>"成长期",
		"4"=>"扩张期",
	),
	//服务类型
	"service_type"=>array(
		"1"=>"Android排行榜优化",
		"2"=>"IOS排行榜优化",
		"3"=>"支付SDK",
		"4"=>"媒体公关",
		"5"=>"游戏版本号/备案",
		"6"=>"IDC机房",
		"7"=>"ICP代理",
	),
	//公司/团队规模
	"company_size"=>array(
		"1"=>"10人以内",
		"2"=>"10-20人",
		"3"=>"20-50人",
		"4"=>"50-100人",
		"5"=>"100-200人",
		"6"=>"200人以上",
	),
		
		
		
/*****************************************************/
		//需求目标
		"need_targets"=>array(
				"1"=>"找独代",
				"2"=>"找联运平台",
				"3"=>"找发行商",
				"4"=>"出售/收编",
				"5"=>"投资/融资",
				"6"=>"找渠道",
		),
		//游戏完成度
		"game_schedule"=>array(
				"0"=>"请选择",
				"1"=>"10%",
				"2"=>"20%",
				"3"=>"30%",
				"4"=>"40%",
				"5"=>"50%",
				"6"=>"60%",
				"7"=>"70%",
				"8"=>"80%",
				"9"=>"90%",
				"10"=>"已完成",
		),
		//游戏阶段
		"game_stage"=>array(
				"1"=>"立项",
				"2"=>"demo",
				"3"=>"测试",
				"4"=>"已上线",
		),
		//游戏平台
		"game_platform"=>array(
			"1"=>"IOS正版",
			"2"=>"Android",
			"3"=>"IOS 越狱",
		),
		//程序平台
		"program_platform"=>array(
			"1"=>"Cocos2D-X",
			"2"=>"Unity3D",
			"3"=>"Unreal/虚幻",
			"4"=>"自研引擎",
			"5"=>"Lua",
			"6"=>"Flash",
			"7"=>"HTML5",
			"8"=>"Object-C",
			"9"=>"C++",
			"10"=>"Java",
			"11"=>"其它",
		),
		//是否
		"yesorno"=>array(
				"0"=>"否",
				"1"=>"是",
		),
		//是否审核
		"yesorno_status"=>array(
				"0"=>"否",
				"3"=>"是",
		),
		
		//承办方类型
		'organizer_type'=>array(
				'1'=>'承办方',
				'2'=>'协办方',
		),
		
		//活动经费
		'event_cost'=>array(
				'0'=>'免费',
				'1'=>'AA制',
				'2'=>'50元以下',
				'3'=>'50-200元',
				'4'=>'200-500元',
				'5'=>'500-1000元',
				'6'=>'1000元以上',
		),
		
		//审核状态
		'verify_status'=>array(
				'0'=>'未审核',
				'1'=>'待审核',
				'2'=>'审核未通过',
				'3'=>'已审核',
		),
		
		//认证状态
		'auth_status'=>array(
				'0'=>'未认证',
				'1'=>'待认证',
				'2'=>'认证未通过',
				'3'=>'已认证',
		),
		
		//星期几
		'week_number'=>array(
				"0"=> "星期日",
				"1"=> "星期一",
				"2"=> "星期二",
				"3"=> "星期三",
				"4"=> "星期四",
				"5"=> "星期五",
				"6"=> "星期六"
		),
);