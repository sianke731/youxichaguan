<?php
/**
 * 公司控制器
 * @author JTee <sianke731@126.com> 
 * @version TS3.0
 */
class IndexAction extends Action {

	/**
	 * 公司首页
	 */
	public function index() {
		$key = $_GET['key'];
		$this->assign('keys', $key);
		$condition = '';
		if($key){
			$condition = ' AND name like "%'.$key.'%"';
		}
		
		$area = t($_GET['area']);
		$this->assign('area', $area);
		if($area){
			$condition = ' AND city="'.$area.'"';
		}
		$type = t($_GET['type']);
		$this->assign('type', $type);
		if($type){
			$condition = ' AND type="'.$type.'"';
		}
		$count = M('Company')->where('is_verify=3'.$condition)->order('id DESC')->count();
		$this->assign('count', $count);
		$list = M('Company')->where('is_verify=3'.$condition)->order('id DESC')->findPage(20);
		$this->assign($list);
		$this->assign('verify_list', C('verify_status'));  //审核状态
		$this->assign('channel_type', C('channel_type'));  //公司类型
		$this->assign('company_size', C('company_size'));  //公司规模
		
		//查询区域
		$areas = D('Option')->where('type=1')->order('sort ASC,id DESC')->select();
		$this->assign('areas', $areas);
		//查询类型
		$types = D('Option')->where('type=2')->order('sort ASC,id DESC')->select();
		$this->assign('types', $types);
		
		$flags = $_GET;
		unset($flags['app']);
		unset($flags['mod']);
		unset($flags['act']);
		$this->assign('flags', $flags);
		switch ($type){
			case '5'://CP
				//查询标签
				$game_tags = D('Game_tag')->order('sort ASC,id DESC')->select();
				$this->assign('game_tags', $game_tags);
				break;
			case '7': //渠道
				$this->assign('cooperation_area', C('cooperation_area')); //合作区域
				$this->assign('channel_type', C('channel_type')); //渠道类型
				$this->assign('pricing_model', C('pricing_model')); //定价模式
				break;
			case '9'://发行
				$this->assign('cooperation_area', C('cooperation_area')); //合作区域
				$this->assign('platform', C('platform')); //合作平台
				$this->assign('game_phase', C('game_phase')); //合作游戏阶段要求
				foreach($list['data'] as &$val){
					$val['game'] = D('Game')->where('is_verify=3')->limit(4)->select();
				}
				//print_r($list);die;
				$this->assign($list);
				break;
			case '10'://外包
				$this->assign('outsourcing_type', C('outsourcing_type')); //外包类型
				break;
			case '12'://投资
				$this->assign('game_phase', C('game_phase')); //合作游戏阶段要求
				break;
			case '11'://服务
				$this->assign('service_type', C('service_type')); //服务类型
				break;
			default:
				break;
		}
		
		$this->setTitle('公司首页');
		$this->display();
	}
	
	/**
	 * 公司详情
	 */
	public function show() {
		$id = intval($_GET['id']);
		$data = M('Company a')->where('a.is_verify=3 AND a.id='.$id)->find();
		if(empty($data)){
			$this->error('找不到该公司');
		}
		$this->assign($data);
		
		//是否收藏
		$isCollection = M('Collection')->getCollection($id,'company');
		$this->assign('is_collection', $isCollection);
		
		//新加入公司
		$newCompany = D('Company')->field('id,name,logo')->where('is_verify=3')->order('id DESC')->limit(15)->select();
		$this->assign('newCompany' , $newCompany);
		
		$this->assign('channel_type', C('channel_type'));  //公司类型
		$this->assign('company_size', C('company_size'));  //公司规模
		$this->setTitle($data['name'].'公司详情');
		
		switch ($data['type']){
			case '5'://CP
				//查找公司旗下产品
				$games = D('Game')->where('company_id='.$id.' AND is_verify=3')->select();
				$this->assign('games', $games); 
				break;
			case '7': //渠道
				$other = D('Company_channel')->where('company_id='.$id)->find();
				$this->assign('other', $other);
				$this->assign('cooperation_area', C('cooperation_area')); //合作区域
				$this->assign('channel_type', C('channel_type')); //渠道类型
				$this->assign('pricing_model', C('pricing_model')); //定价模式
				$this->assign('platform', C('platform')); //平台要求
				break;
			case '9'://发行
				//查找公司旗下产品
				$games = D('Game')->where('company_id='.$id.' AND is_verify=3')->select();
				$this->assign('games', $games); 
				$other = D('Company_publish')->where('company_id='.$id)->find();
				$this->assign('other', $other);
				$this->assign('cooperation_area', C('cooperation_area')); //合作区域
				$this->assign('platform', C('platform')); //合作平台
				$this->assign('game_phase', C('game_phase')); //合作游戏阶段要求
				$this->assign('networking', C('networking')); //合作游戏联网要求
				break;
			case '10'://外包
				$other = D('Company_outer')->where('company_id='.$id)->find();
				$this->assign('other', $other);
				$this->assign('outsourcing_type', C('outsourcing_type')); //外包类型
				$this->assign('product_outsourcing', C('product_outsourcing')); //产品外包
				$this->assign('arts_outsourcing', C('arts_outsourcing')); //美术外包
				$this->assign('music_outsourcing', C('music_outsourcing')); //音乐外包
				$this->assign('test_outsourcing', C('test_outsourcing')); //测试外包
				$this->assign('program_outsourcing', C('program_outsourcing')); //程序外包
				$this->assign('plan_outsourcing', C('plan_outsourcing')); //企划外包
				break;
			case '12'://投资 
				$other = D('Company_investment')->where('company_id='.$id)->find();
				$this->assign('other', $other);
				$this->assign('investment_phase', C('investment_phase')); //主投游戏阶段
				$this->assign('has_game_num', C('has_game_num')); //曾投资游戏数
				$this->assign('has_cost', C('has_cost')); //曾投资总额
				break;
			case '11'://服务
				$other = D('Company_service')->where('company_id='.$id)->find();
				$this->assign('other', $other);
				$this->assign('service_type', C('service_type')); //服务类型
				break;
			default:
				break;
		}
		$this->display();
	}
	
	
	/**
	 * 我的公司
	 */
	public function mycompany() {
		$map['uid'] = $GLOBALS['ts']['uid'];
		$list = M('Company')->field('id,name,uid,is_verify,type,ctime,logo')->where($map)->order('id DESC')->findPage(20);
		
		$this->assign('verify_list', C('verify_status'));
		$this->assign($list);
		$this->setTitle('我的公司');
		$this->display();
	}
	


	/**
	 * 删除公司
	 */
	public function del() {
		$map['uid'] = $GLOBALS['ts']['uid'];
		$map['id'] = intval($_GET['id']);
		$ret = M('Company')->where($map)->find();
		if($ret){
			$result = M('Company')->where('id='.$map['id'])->delete();
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
	
	/**
	 * 收藏公司
	 */
	public function collection() {
		$uid = $GLOBALS['ts']['mid'];
		if($uid){
			$map['id'] = intval($_GET['id']);
			$ret = M('Company')->where($map)->find();
			if($ret){
				$data['uid'] = $uid;
				$data['source_id'] = $map['id'];
				$data['source_table_name'] = 'company';
				$data['source_app'] = 'company';
				$result = M('Collection')->addCollection($data);
				if($result){
					$this->success('收藏成功');
				}else{
					$this->error('收藏失败');
				}
			}else{
				$this->error('没有对应的公司');
			}
		}else{
			$this->error('对不起，你还未登录');
		}
	}
	
	/**
	 * 取消收藏
	 */
	public function delCollection() {
		$uid = $GLOBALS['ts']['mid'];
		if($uid){
			$id = intval($_GET['id']);
			$result = M('Collection')->delCollection($id,'company',$uid);
			if($result){
				$this->success('取消收藏成功');
			}else{
				$this->error('取消收藏失败');
			}
		}else{
			$this->error('对不起，你还未登录');
		}
	}
	/*
	 * 编辑公司
	 */
	public function edit(){
		$id = intval($_GET["id"]);
		if(empty($id)){
			$this->error("id为空,参数错误");
		}
		$company = M("company")->getBYid($id);
		if(empty($company)){
			$this->error("你所选择的公司不存在");
		}
		if($company['is_verify']==1){
			$this->error("该公司已认证,不允许修改");
		}
		if(empty($_POST["submit"])){
			$pic = model('attach')->getAttachById($company["logo"]);
	// 		dump($pic);//die;
			$this->assign("company_id",$company["id"]);
			$this->assign("pic",$pic);
			$this->display("add");
		}else{
			$company_ret = model('Company')->setCompany();
			if(!empty($_POST["logo_company"])){
				M("company")->where(array("id"=>$company_ret["id"]))->setField("logo",$_POST["logo_company"]);
			}
			$this->success();
			//dump($_POST);
		}
	}
	/*
	 * 新增公司
	 */
	public function add(){
		if(empty($_POST["submit"])){
			$this->display();
		}else{
			//dump($_POST);die;
			$company_ret = model('Company')->setCompany();
			if(!empty($_POST["logo_company"])){
				M("company")->where(array("id"=>$company_ret["id"]))->setField("logo",$_POST["logo_company"]);
			}
			$this->success();
		}
	}
	public function edit_company_ajax(){
		$company_type = intval($_GET['company_type']);
		$company_id = $_GET['company_id'];
		$company = M("company")->getById($company_id);
		$this->assign("company",$company);
		switch ($company_type)
		{
			case "5":
				$cp = M("company_cp")->where("company_id=".$company_id)->find();
				$tj_tags = M("game_tag")->where("type=1")->field("id,title")->select();
				if(!empty($cp['tags'])){
					$tags = M("game_tag")->where("id in(".$cp['tags'].")")->field("title,id")->select();
					$selectedIds = explode(",", $cp['tags']);
				}
				$this->assign("tag_num",5);
				$this->assign("tags",$tags);
// 				dump($tags);
				$this->assign("selectedIds",$selectedIds);
				$this->assign("tj_tags",$tj_tags);
				$this->assign("tags_my",$cp['tags']);
				break;
			case "7":
				$channel = M("company_channel")->where("company_id=".$company_id)->find();
				$channel['user_cover'] = explode(",", $channel['user_cover']);
				$this->assign("channel",$channel);
				$this->assign("channel_type",C("channel_type"));
				$this->assign("platform",C("platform"));
				$this->assign("pricing_model",C("pricing_model"));
				$this->assign("user_cover",C("user_cover"));
				break;
			case "8":
				$platform = M("company_platform")->where("company_id=".$company_id)->find();
				$platform = explode(",", $platform['types']);
				//dump($temp);
				$this->assign("platform",$platform);
				$this->assign("shop_type",C("shop_type"));
				break;
			case "9":
				$publish = M("company_publish")->where("company_id=".$company_id)->find();
				$publish['areas'] = explode(",", $publish['areas']);
				$this->assign("publish",$publish);
				
				
				$has_pub_game = M("company_has_pub")->where("company_id=".$company_id)->select();
				$cooperation_area = C("cooperation_area");
				$platform = C("platform");
				foreach ($has_pub_game as $k => $v){
					$has_pub_game[$k]["icon"] = model("attach")->getAttachById($v["icon"]);
// 					$has_pub_game[$k]["area_show"] = 
					$area_show = explode(",", $v["area"]);
// 					dump($area_show);die;
					$tmp_11 = array();
					foreach ($area_show as $ke => $va){
// 						dump($cooperation_area[$va]);
						
						$tmp_11[] = $cooperation_area[$va];
					}
					$has_pub_game[$k]["area_show"] = join(" ",$tmp_11);
					
					$platform_show = explode(",", $v["platform"]);
					// 					dump($area_show);die;
					$tmp_11 = array();
					foreach ($platform_show as $ke => $va){
						// 						dump($cooperation_area[$va]);
					
						$tmp_11[] = $platform[$va];
					}
					$has_pub_game[$k]["platform_show"] = join(" ",$tmp_11);
					
					//die;
				}
// 				dump($has_pub_game);
				$this->assign("has_pub_game",$has_pub_game);
				//dump($publish);
				$this->assign("cooperation_area",C("user_cover"));
				//dump(C("cooperation_type"));die;
				$this->assign("cooperation_type",C("cooperation_type"));
				$this->assign("platform",$platform);
				$this->assign("networking",C("networking"));
				$this->assign("game_phase",C("game_phase"));
				$this->assign("has_cost",C("has_cost"));
				break;
			case "10":
				$outer = M("company_outer")->where("company_id=".$company_id)->find();
				//dump($outer);
				$outer['wholes'] = explode(",", $outer['wholes']);
				$outer['arts'] = explode(",", $outer['arts']);
				$outer['musics'] = explode(",", $outer['musics']);
				$outer['tests'] = explode(",", $outer['tests']);
				$outer['programs'] = explode(",", $outer['programs']);
				$outer['plans'] = explode(",", $outer['plans']);
				$this->assign("outer",$outer);
				//dump($outer);
				$this->assign("product_outsourcing",C("product_outsourcing"));
				$this->assign("arts_outsourcing",C("arts_outsourcing"));
				$this->assign("music_outsourcing",C("music_outsourcing"));
				$this->assign("test_outsourcing",C("test_outsourcing"));
				$this->assign("plan_outsourcing",C("plan_outsourcing"));
				$this->assign("program_outsourcing",C("program_outsourcing"));
				break;
			case "11":
				$investment = M("company_investment")->where("company_id=".$company_id)->find();
				//dump($investment);
				$investment['stage'] = explode(",", $investment['stage']);
				$this->assign("investment",$investment);
				$this->assign("investment_phase",C("investment_phase"));
				$this->assign("has_game_num",C("has_game_num"));
				$this->assign("has_cost",C("has_cost"));
				break;
			case "12":
				$service = M("company_service")->where("company_id=".$company_id)->find();
				//dump($service);
				$service['types'] = explode(",", $service['types']);
				//dump($service);
				$this->assign("service",$service);
	
				$this->assign("service_type",C("service_type"));
				break;
			default:
				die;
		}
		if($_GET['is_index']){
			$this->display("ajax_company_type_index_".$company_type);
		}else{
			$this->display("ajax_company_type_".$company_type);
		}
	}
	public function add_company_ajax(){
		$company_type = intval($_GET['company_type']);
		switch ($company_type)
		{
			case "5":
				$tj_tags = M("game_tag")->where("type=1")->field("id,title")->select();
				$this->assign("tj_tags",$tj_tags);
				$this->assign("tag_num",5);
				break;
			case "7":
				$this->assign("user_cover",C("user_cover"));
				$this->assign("channel_type",C("channel_type"));
				$this->assign("platform",C("platform"));
				$this->assign("pricing_model",C("pricing_model"));
				$this->assign("cooperation_area",C("cooperation_area"));
				break;
			case "8":
				$this->assign("shop_type",C("shop_type"));
				break;
			case "9":
				$this->assign("cooperation_area",C("user_cover"));
				$this->assign("cooperation_type",C("cooperation_type"));
				$this->assign("platform",C("platform"));
				$this->assign("networking",C("networking"));
				$this->assign("game_phase",C("game_phase"));
				$this->assign("has_game_num",C("has_game_num"));
				$this->assign("has_cost",C("has_cost"));
				break;
			case "10":
				$this->assign("product_outsourcing",C("product_outsourcing"));
				$this->assign("arts_outsourcing",C("arts_outsourcing"));
				$this->assign("music_outsourcing",C("music_outsourcing"));
				$this->assign("test_outsourcing",C("test_outsourcing"));
				$this->assign("plan_outsourcing",C("plan_outsourcing"));
				$this->assign("program_outsourcing",C("program_outsourcing"));
				break;
			case "11":
				$this->assign("investment_phase",C("investment_phase"));
				$this->assign("has_game_num",C("has_game_num"));
				$this->assign("has_cost",C("has_cost"));
				break;
			case "12":
				$this->assign("service_type",C("service_type"));
				break;
			default:
				die;
		}
		if($_GET['is_index']){
			$this->display("ajax_company_type_index_".$company_type);
		}else{
			$this->display("ajax_company_type_".$company_type);
		}
	}
	public function getTagId(){
		// 获取标签名称
		$name = t($_POST['name']);
		// 判断标签是否为空
		if(empty($name)) {
			$res['status'] = 0;
			$res['info'] = L('PUBLIC_TAG_NOEMPTY');
			exit(json_encode($res));
		}
		$tagInfo = M("game_tag")->where("type=0 and title='$name'")->getField("id");
		//dump(M()->getLastSql());die;
		if(empty($tagInfo)){
			$tagInfo = M("game_tag")->add(array('title' => $name,"type"=>0,"addtime"=>time(),"sort"=>999));
		}
		//dump($tagInfo);die;
		//dump(M()->getLastSql());die;
		if($tagInfo === false) {
			$res['status'] = 0;
			$res['info'] = '获取标签ID失败';
		} else {
			$res['status'] = 1;
			$res['info'] = '获取标签ID成功';
			$res['data'] = $tagInfo;
		}
	
		exit(json_encode($res));
	}
	public function get_city(){
		$province_id = intval($_GET['province_id']);
		$city = M("area")->where("pid=".$province_id)->field("area_id,title")->select();
		echo(json_encode($city));die;
	}
	
}