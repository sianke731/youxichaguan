<?php
/**
 * 产品控制器
 * @author JTee <sianke731@126.com> 
 * @version TS3.0
 */
class IndexAction extends Action {

	/**
	 * 产品首页
	 */
	public function index() {
		changePlatform('1,3');
		$key = t($_GET['key']);
		$this->assign('keys', $key);
		$condition = '';
		if($key){
			$condition = ' AND a.name like "%'.$key.'%"';
		}
		
		$phase = t($_GET['phase']);
		$this->assign('phase', $phase);
		if($phase){
			$condition .= ' AND FIND_IN_SET("'.$phase.'",stage)';
		}
		$plat = t($_GET['platform']);
		$this->assign('plat', $plat);
		if($plat){
			$condition .= ' AND FIND_IN_SET("'.$plat.'",platform)';
		}
		$area = t($_GET['area']);
		$this->assign('area', $area);
		if($area){
			$condition .= ' AND FIND_IN_SET("'.$area.'",areas)';
		}
		$tag = t($_GET['tag']);
		$this->assign('tag', $tag);
		if($tag){
			$condition .= ' AND FIND_IN_SET("'.$tag.'",tags)';
		}
		$order = t($_GET['order'])?t($_GET['order']):'id';
		$this->assign('order', $order);
		//7日内查看最多
		$hotlist = M('Game a')->join(C('DB_PREFIX').'company b ON a.company_id=b.id')->field('a.id,a.name,a.company_id,a.is_verify,a.ctime,a.logo,b.name as company_name,a.views')
			->where('a.is_verify=3')->order('a.views DESC')->limit(10)->select();
		$this->assign('hotlist',$hotlist);
		$count = M('Game a')->join(C('DB_PREFIX').'company b ON a.company_id=b.id')->where('a.is_verify=3'.$condition)->order('a.'.$order.' DESC')->count();
		$this->assign('count', $count);
		$list = M('Game a')->join(C('DB_PREFIX').'company b ON a.company_id=b.id')->field('a.id,a.name,a.company_id,a.is_verify,a.ctime,a.logo,b.name as company_name,a.tags,a.stage,a.platform,a.targets')->where('a.is_verify=3'.$condition)->order('a.'.$order.' DESC')->findPage(16);
		$this->assign($list);

		$this->assign('game_phase', C('game_phase'));  //阶段
		$this->assign('platform', C('game_platform'));  //平台
		//$this->assign('cooperation_area', C('cooperation_area'));  //地域
		$this->assign('cooperation_area', C('user_cover'));  //地域
		//查询标签
		$game_tags = D('Game_tag')->order('sort ASC,id DESC')->select();
		$this->assign('game_tags', $game_tags);
		
		$this->setTitle('产品首页');
		$this->display();
	}
	
	/**
	 * 产品详情
	 */
	public function show() {
		$id = intval($_GET['id']);
		$isCollection = M('Collection')->getCollection($id,'game');
		$this->assign('is_collection', $isCollection);
		
		$data = M('Game a')->join(C('DB_PREFIX').'company b ON a.company_id=b.id')->field('a.*,b.name as company_name,b.teams,b.introduce as company_introduce')->where('a.is_verify=3 AND a.id='.$id)->find();
		if(empty($data)){
			$this->error('找不到该产品');
		}
		$this->assign($data);
		$this->assign('verify_list', C('verify_status'));  //审核状态
		$this->assign('auth_list', C('auth_status'));  //认证状态
		$this->assign('game_schedule', C('game_schedule'));  //游戏进度
		$this->assign('game_stage', C('game_stage'));  //游戏阶段
		$this->assign('game_platform', C('game_platform'));  //游戏平台
		$this->assign('need_targets', C('need_targets'));  //需求目标
		$this->assign('cooperation_area', C('cooperation_area'));  //需求目标
		$this->assign('company_size', C('company_size'));  //团队规模
		$this->assign('program_platform', C('program_platform'));  //程序平台
		$this->assign('yesorno', C('yesorno'));  //是否
		$this->assign('has_cost', C('has_cost'));  //曾代理费
		//代理商
		$agentlist = M('Game_agent a')->where('a.game_id='.$id)->order('a.id DESC')->select();
		//print_r($agentlist);die;
		$this->assign('agentlist',$agentlist);
		//新加入产品
		$newlist = M('Game a')->join(C('DB_PREFIX').'company b ON a.company_id=b.id')->field('a.id,a.name,a.company_id,a.is_verify,a.ctime,a.logo,b.name as company_name,a.views,a.tags,a.stage,a.platform,a.targets')
		->where('a.is_verify=3')->order('a.id DESC')->limit(5)->select();
		$this->assign('newlist',$newlist);
		$this->setTitle($data['name'].'产品详情');
		$this->display();
	}
	
	
	/**
	 * 我的产品
	 */
	public function mygame() {
		$map['a.uid'] = $GLOBALS['ts']['mid'];
		$list = M('Game a')->join(C('DB_PREFIX').'company b ON a.company_id=b.id')->field('a.id,a.name,a.company_id,a.is_verify,a.ctime,a.logo,b.name as company_name')->where($map)->order('a.id DESC')->findPage(20);
		/*foreach( $list['data'] as &$value ){
			$value['cover'] = getCover($value['logo']);
		}*/
		$this->assign('verify_list', C('verify_status'));
		$this->assign($list);
		$this->setTitle('我的产品');
		$this->display();
	}
	
	/**
	 * 删除产品
	 */
	public function del() {
		$map['uid'] = $GLOBALS['ts']['mid'];
		$map['id'] = intval($_GET['id']);
		$ret = M('Game')->where($map)->find();
		if($ret){
			$result = M('Game')->where('id='.$map['id'])->delete();
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
	
	/**
	 * 收藏产品
	 */
	public function collection() {
		$uid = $GLOBALS['ts']['mid'];
		if($uid){
			$map['id'] = intval($_GET['id']);
			$ret = M('Game')->where($map)->find();
			if($ret){
				$data['uid'] = $uid;
				$data['source_id'] = $map['id'];
				$data['source_table_name'] = 'game';
				$data['source_app'] = 'game';
				$result = M('Collection')->addCollection($data);
				if($result){
					$this->success('收藏成功');
				}else{
					$this->error('收藏失败');
				}
			}else{
				$this->error('没有对应的产品');
			}
		}else{
			$this->error('对不起，你还未登录');
		}
	}
	
	/**
	 * 取消收藏产品
	 */
	public function delCollection() {
		$uid = $GLOBALS['ts']['mid'];
		if($uid){
			$id = intval($_GET['id']);
			$result = M('Collection')->delCollection($id,'game',$uid);
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
	 * 新增产品
	 */
	public function add(){
		//dump(tsconfig('game_stage'));
		if($_POST["submit"]){
			$data = array();
// 			dump($_POST);die;
			if($_POST["first"]||($_POST["old_company"]==0)){
// 				echo 123;die;
				$company_ret = model('Company')->setCompany();
				$data["company_id"] = $company_ret["id"];
			}else{
				$data["company_id"] = t($_POST["old_company"]);
			}			
			$data["name"] = t($_POST["name"]);
			$data["targets"] = join(",",$_POST["targets"]);
			$data["areas"] = join(",",$_POST["areas"]);
			$data["tags"] = t($_POST["tags"]);
			$data["schedule"] = t($_POST["schedule"]);
			$data["stage"] = t($_POST["stage"]);
			$data["stage_date"] = t($_POST["stage_date"]);
			$data["download"] = t($_POST["download"]);
			$data["platform"] = join(",",$_POST["platform"]);
			$data["is_online"] = t($_POST["is_online"]);
			$data["logo"] = t($_POST["active_img"]);
			$data["program"] = join(",",$_POST["program"]);
			$data["introduce"] = t($_POST["introduce"]);
			$data["is_verify"] = 0;
			$data["is_auth"] = 0;
			$data["is_recommend"] = 0;
			$data["is_top"] = 0;
			$data["views"] = 0;
			$data["collects"] = 0;
			$data["uid"] = $this->mid;
			$data["ctime"] = time();
// 			dump($data);die;
			//处理代理商公司信息
			if(!M("game")->create($data)){
				$this->error(M("game")->getError());
			}else{
				$add_id = M("game")->add();
			}
			if($add_id){
				$agent_hidden = $_POST["agent_hidden"];
				foreach ($agent_hidden as $k => $v){
					$tmp_arr = explode("-", $v);
					$insert_arr = array();
					$insert_arr["game_id"] = $add_id;
					$insert_arr["name"] = $tmp_arr["0"];
					$insert_arr["area"] = $tmp_arr["1"];
					$insert_arr["platform"] = $tmp_arr["2"];
					$insert_arr["price"] = $tmp_arr["3"];
					M("game_agent")->data($insert_arr)->add();
				}
				$this->success();
			}
// 			dump($data);
// 			dump($_POST);
// 			die;
		}else{
			$game_list = M("game")->where(array("uid"=>$this->mid))->select();
			foreach ($game_list as $k => $v){
				$tmp[] = $v["company_id"];
			}
			$tmp = array_unique($tmp);
			$map = array();
			$map['id']  = array('in',$tmp);
			$old_company = M("company")->where($map)->select();
	// 		dump($old_company);die;
			$this->assign("old_company",$old_company);
			$tj_tags = M("game_tag")->where("type=1")->field("id,title")->select();
			$this->assign("tj_tags",$tj_tags);
			//$this->assign("cooperation_area",tsconfig('cooperation_area'));
			$this->assign('cooperation_area', tsconfig('user_cover'));  //地域
			$this->assign("need_targets",tsconfig('need_targets'));
			$this->assign("program_platform",tsconfig('program_platform'));
			$this->assign("is_online",tsconfig('yesorno'));
			$this->assign("game_platform",tsconfig('game_platform'));
			$this->assign("game_schedule",tsconfig('game_schedule'));
			$this->assign("game_stage",tsconfig('game_stage'));
			$this->assign("has_cost",tsconfig('has_cost'));
			$this->display();
		}
	}
	public function game_subscribe_add(){
		if($_POST["submit"]){
			// dump($_POST);
			$self_id = $this->mid;
			$_POST["uid"] = $self_id;
			$game_subscribe = M("game_subscribe")->getByUid($self_id);
			if(empty($game_subscribe)){
				if(!M("game_subscribe")->create()){
					$this->error(M("game_subscribe")->getError());
				}else{
					M("game_subscribe")->add();
					$this->success("添加订阅成功");
				}
			}else{
				$this->error("你已经订阅了无需再次订阅");
			}
			// $game_subscribe = M("game_subscribe")->add();
		}else{
			$self_id = $this->mid;
			$game_subscribe = M("game_subscribe")->getByUid($self_id);
			if(!empty($game_subscribe)){
				$this->error("你已经订阅了无需再次订阅");
			}
			$this->assign("game_platform",tsconfig('game_platform'));
			$this->assign("game_stage",tsconfig('game_stage'));
			$tj_tags = M("game_tag")->where("type=1")->field("id,title")->select();
			$this->assign("tj_tags",$tj_tags);
			$this->display("game_subscribe");
		}
	}
	public function game_subscribe_edit(){
		$self_id = $this->mid;
		//$_POST["uid"] = $self_id;
		$game_subscribe = M("game_subscribe")->getByUid($self_id);
		if(empty($game_subscribe)){
			$this->error("你还未订阅,请先新增订阅");
		}
		if($_POST["submit"]){
			// dump($_POST);
			if(!M("game_subscribe")->create()){
				$this->error(M("game_subscribe")->getError());
			}else{
				M("game_subscribe")->where(array("uid"=>$self_id))->save();
				$this->success("修改订阅成功");
			}
			// $game_subscribe = M("game_subscribe")->add();
		}else{
			$this->assign("game_platform",tsconfig('game_platform'));
			$this->assign("game_stage",tsconfig('game_stage'));
			$tj_tags = M("game_tag")->where("type=1")->field("id,title")->select();
			$this->assign("tj_tags",$tj_tags);
			$game_subscribe["platform"] = explode(",", $game_subscribe["platform"]);
			$this->assign("game_subscribe",$game_subscribe);
			if(!empty($game_subscribe['tags'])){
				$tags = M("game_tag")->where("id in(".$game_subscribe['tags'].")")->field("title,id")->select();
				$selectedIds = explode(",", $game_subscribe['tags']);
			}
			$this->assign("tag_num",5);
			$this->assign("tags",$tags);
// 				dump($tags);
			$this->assign("selectedIds",$selectedIds);
			$this->assign("tj_tags",$tj_tags);
			$this->assign("tags_my",$cp['tags']);
			// dump($game_subscribe);
			$this->display("game_subscribe");
		}
	}
	/*
	*自动发送订阅邮件3天版
	*/
	public function dogame_subscribe_3day(){
		$game_subscribe_list = M("game_subscribe")->where(array("cycle"=>1))->select();
		if(empty($game_subscribe_list)||!is_array($game_subscribe_list)){
			die;
		}
		// dump($game_subscribe_list);die;
		foreach ($game_subscribe_list as $k => $v) {
			$map = array();
			$map["is_verify"] = 1;
			$map["is_auth"] = 1;
			// $map["cycle"] = 1;
			$map["stage"] = $v["stage"];
			if(!empty($v["platform"])){
				$tmp_platform = explode(",", $v["platform"]);
				foreach ($tmp_platform as $ke => $va) {
					// $map["platform"][] = array(array('like','%'.$va), array('like','%'.$va.'%'), array('like',$va.'%'),'or'); 
					$map["platform"][] = array('like','%,'.$va);
					$map["platform"][] = array('like','%,'.$va.',%');
					$map["platform"][] = array('like',$va.',%');
				}
				$map["platform"][] = 'or';
			}
			if(!empty($v["tags"])){
				$tmp_tags = explode(",", $v["tags"]);
				foreach ($tmp_tags as $ke => $va) {
					// $map["platform"][] = array(array('like','%'.$va), array('like','%'.$va.'%'), array('like',$va.'%'),'or'); 
					$map["tags"][] = array('like','%,'.$va);
					$map["tags"][] = array('like','%,'.$va.',%');
					$map["tags"][] = array('like',$va.',%');
				}
				$map["tags"][] = 'or';
			}

			// $where1["platform"] = array();
			// $map["platform"] = array();
			$game_list = M("game")->field("name,download,introduce,logo")->where($map)->select();
			$body="<div><ul>";
			foreach ($game_list as $key => $value) {
				$body.="<li>游戏:".$value["name"]."<p>".$value["introduce"]."<br/>下载地址:<a href='".$value["download"]."'>".$value["download"]."</a></p></li>";
			}
			$body .="</ul></div>";
			$body_template = '<style>a.email_btn,a.email_btn:link,a.email_btn:visited{background:#0F8CA8;padding:5px 10px;color:#fff;width:80px;text-align:center;}</style><div style="width:540px;border:#0F8CA8 solid 2px;margin:0 auto"><div style="color:#bbb;background:#0f8ca8;padding:5px;overflow:hidden;zoom:1"><div style="float:right;height:15px;line-height:15px;padding:10px 0;display:none">2012年07月15日</div>
					<div style="float:left;overflow:hidden;position:relative"><a><img style="border:0 none" src="'.$GLOBALS['ts']['site']['logo'].'"></a></div></div>
					<div style="background:#fff;padding:20px;min-height:300px;position:relative">		<div style="font-size:14px;">			
						            	<p style="padding:0 0 20px;margin:0;font-size:12px">'.$body.'</p>
						            </div></div><div style="background:#fff;">
			            <div style="text-align:center;height:18px;line-height:18px;color:#999;padding:6px 0;font-size:12px">若不想再收到此类邮件，请点击<a href="'.U('game/Index/game_subscribe_cancel').'" style="text-decoration:none;color:#3366cc">取消订阅</a>或者<a href="'.U('game/Index/game_subscribe_edit').'" style="text-decoration:none;color:#3366cc">修改订阅</a></div>
			            <div style="line-height:18px;text-align:center"><p style="color:#999;font-size:12px">'.$site['site_footer'].'</p></div>
			        </div></div>';
			$mail_title = "游戏订阅";
			model('Mail')->send_email($v['email'],$mail_title,$body_template);
			echo(123);die;
			$this->success();
			die;
			// dump($game_list);
			// dump(M()->getLastSql());die;
		}
	}
	public function game_subscribe_cancel(){
		$self_id = $this->mid;
		//$_POST["uid"] = $self_id;
		$game_subscribe = M("game_subscribe")->getByUid($self_id);
		if(empty($game_subscribe)){
			$this->error("你还未订阅,无法取消订阅");
		}
		M("game_subscribe")->where(array("uid"=>$self_id))->delete();
		$this->assign('jumpUrl',U('game/Index/index'));
		$this->success("成功取消订阅");
	}
}