<?php
/**
 * 公司管理配置控制器
 * @author dng <imdengshuang@qq.com> 
 */
// 加载后台控制器
tsload ( APPS_PATH . '/admin/Lib/Action/AdministratorAction.class.php' );
class  CompanyAction extends AdministratorAction {
	
	/**
	 * 初始化，页面标题，用于双语
	 */
	public function _initialize() {
		parent::_initialize ();
	}
	
	public function index(){	
		$company_type = M("option")->where("type=2")->select();
		$this->assign("company_type",$company_type);
		//dump($company_type);
		$company_type_selected = htmlspecialchars($_POST['company_type_selected']);
		$map = "1=1";
		if(!empty($company_type_selected)){
			$map.=" and type=".$company_type_selected;
		}
		$name = htmlspecialchars($_POST['name']);
		if(!empty($name)){
			$map .= " and name like '%".$name."%'";
		}
		//dump($company_type);
		//$list = M("company as c")->where("1=1")->join("left join area as a on(c.city=a.area_id)")->getField("c.id,a.title,c.name");
		$list = M("company as c")->where($map)->field("c.id,a.title,c.name,c.is_recommend")->join("left join yx_area as a on(c.city=a.area_id)")->select();
		//$list = D("company")->select();
		//var_dump(function_exists("D"));
		//dump(M()->getLastSql());
		$this->assign("list",$list);
		$this->display();

	}
	public function add(){
		$company_type = M("option")->where("type=2")->order("sort asc")->field("id,title")->select();
		//dump($company_type);
		$this->assign("company_type",$company_type);
	 	$this->assign("company_size",C("company_size"));
	 	$province_arr = M("area")->where("pid=0")->field("area_id,title")->select();
	 	$this->assign("province_arr",$province_arr);
		$this->display();
	}
	public function doadd(){
		//dump($_POST);
		$data = $_POST;
		$data_insert = array();
		$data_insert['name'] = htmlspecialchars($data['name']);
		$data_insert['type'] = htmlspecialchars($data['type']);
		$data_insert['teams'] = intval($data['company_size']);
		$data_insert['introduce'] = htmlspecialchars($data['company_introduce']);
		$data_insert['province'] = htmlspecialchars($data['province']);
		$data_insert['city'] = htmlspecialchars($data['city']);
		$data_insert['site'] = htmlspecialchars($data['url']);
		$data_insert['linkman'] = htmlspecialchars($data['person']);
		$data_insert['phone'] = htmlspecialchars($data['tele']);
		$data_insert['email'] = htmlspecialchars($data['email']);
		$data_insert['qq'] = htmlspecialchars($data['qq']);
		$data_insert['logo'] = intval($data['logo_company']);
		$data_insert['is_verify'] = 0;
		$data_insert['is_auth'] = 0;
		$data_insert['is_recommend'] = 0;
		$data_insert['is_top'] = 0;
		$data_insert['uid'] = 1;
		$data_insert['ctime'] = time();
		$new_id = M("company")->data($data_insert)->add();
		//dump($new_id);die;
		switch ($data_insert['type'])
		{
			case "5":
				M("company_cp")->data(array("company_id"=>$new_id,"tags"=>$data['tags']))->add();
				break;  
			case "7":
				$data_7 = array();
				$data_7['company_id'] = $new_id;
				$data_7['types'] = implode(",",$data['channel_type']);
				$data_7['require'] = intval($data['platform']);
				$data_7['models'] = implode(",",$data['pricing_model']);
				$data_7['areas'] = implode(",",$data['cooperation_area']);
				M("company_channel")->data($data_7)->add();
				break;
			case "8":
				$data_8 = array();
				$data_8['company_id'] = $new_id;
				$data_8['types'] = implode(",",$data['shop_type']);
				M("company_platform")->data($data_8)->add();
				break;
			case "9":
				$data_9 = array();
				$data_9['company_id'] = $new_id;
				$data_9['areas'] = implode(",",$data['cooperation_area']);
				$data_9['platform_require'] = implode(",",$data['platform']);
				$data_9['network_require'] = intval($data['platform']);
				$data_9['stage_require'] = intval($data['game_phase']);
				$data_9['team_require'] = htmlspecialchars($data['game_team_require']);
				$data_9['is_experience'] = intval($data['experience']);
				$data_9['games'] = intval($data['has_game_num']);
				$data_9['price'] = intval($data['has_cost']);
				M("company_publish")->data($data_9)->add();
				break;
			case "10":
				$data_10 = array();
				$data_10['company_id'] = $new_id;
				$data_10['wholes'] = implode(",",$data['product_outsourcing']);
				$data_10['arts'] = implode(",",$data['arts_outsourcing']);
				$data_10['musics'] = implode(",",$data['music_outsourcing']);
				$data_10['tests'] = implode(",",$data['test_outsourcing']);
				$data_10['programs'] = implode(",",$data['program_outsourcing']);
				$data_10['plans'] = implode(",",$data['plan_outsourcing']);
				M("company_outer")->data($data_10)->add();
				break;
			case "11":
				$data_11 = array();
				$data_11['company_id'] = $new_id;
				$data_11['stage'] = implode(",",$data['investment_phase']);
				$data_11['require'] = htmlspecialchars($data['project_require']);
				$data_11['is_experience'] = intval($data['experience']);
				$data_11['games'] = intval($data['has_game_num']);
				$data_11['price'] = intval($data['has_cost']);
				M("company_investment")->data($data_11)->add();
				break;
			case "12":
				$data_12 = array();
				$data_12['company_id'] = $new_id;
				$data_12['types'] = implode(",",$data['service_type']);
				M("company_service")->data($data_12)->add();
				break;
			default:
		}
		$this->success();
	}
	public function get_city(){
		$province_id = intval($_GET['province_id']);
		$city = M("area")->where("pid=".$province_id)->field("area_id,title")->select();
		echo(json_encode($city));die;
	}
	public function edit_company_ajax(){
		$company_type = intval($_GET['company_type']);
		$company_id = $_GET['company_id'];
		switch ($company_type)
		{
			case "5":
				$cp = M("company_cp")->where("company_id=".$company_id)->find();
				$tj_tags = M("game_tag")->where("type=1")->field("id,title")->select();
				if(!empty($cp['tags'])){
					$tags = M("game_tag")->where("id in(".$cp['tags'].")")->field("title")->select();
					$selectedIds = explode(",", $cp['tags']);
				}
			 	$this->assign("tag_num",5);
			 	$this->assign("tags",$tags);
			 	$this->assign("selectedIds",$selectedIds);
			 	$this->assign("tj_tags",$tj_tags);
			 	$this->assign("tags_my",$cp['tags']);
				break;  
			case "7":
				$channel = M("company_channel")->where("company_id=".$company_id)->find();
				$this->assign("channel",$channel);
			 	$this->assign("channel_type",C("channel_type"));
			 	$this->assign("platform",C("platform"));
			 	$this->assign("pricing_model",C("pricing_model"));
			 	$this->assign("cooperation_area",C("cooperation_area"));
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
				//dump($publish);
			 	$this->assign("cooperation_area",C("cooperation_area"));
			 	$this->assign("platform",C("platform"));
			 	$this->assign("networking",C("networking"));
			 	$this->assign("game_phase",C("game_phase"));
			 	$this->assign("has_game_num",C("has_game_num"));
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
		$this->display("ajax_company_type_".$company_type);
	}
	public function add_company_ajax(){
		$company_type = intval($_GET['company_type']);
		switch ($company_type)
		{
			case "5":
			 	$this->assign("tag_num",5);
				break;  
			case "7":
			 	$this->assign("channel_type",C("channel_type"));
			 	$this->assign("platform",C("platform"));
			 	$this->assign("pricing_model",C("pricing_model"));
			 	$this->assign("cooperation_area",C("cooperation_area"));
				break;
			case "8":
			 	$this->assign("shop_type",C("shop_type"));
				break;
			case "9":
			 	$this->assign("cooperation_area",C("cooperation_area"));
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
		$this->display("ajax_company_type_".$company_type);
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
	public function edit(){
		$id = $_GET['id']?$_GET['id']:1;
		if(empty($id)){
			$this->error("参数错误");
		}
		$company = M("company")->where("id=".$id)->find();
		$this->assign("company",$company);
		$company_type = M("option")->where("type=2")->order("sort asc")->field("id,title")->select();
		//dump($company);
		$this->assign("company_type",$company_type);
	 	$this->assign("company_size",C("company_size"));
	 	$province_arr = M("area")->where("pid=0")->field("area_id,title")->select();
	 	$city_arr = M("area")->where("pid = ".$company['province'])->field("area_id,title")->select();
	 	//dump(count($city_arr));
	 	$this->assign("city_arr",$city_arr);
	 	$this->assign("province_arr",$province_arr);
	 	$pic = M("attach")->where("attach_id=".$company['logo'])->find();
	 	$this->assign("pic",$pic);
	 	//dump($pic);
	 	$this->display();
	}
	public function doedit(){
		$company_id = $_GET['company_id'];

		//dump($_POST);
		$data = $_POST;
		$data_insert = array();
		$data_insert['name'] = htmlspecialchars($data['name']);
		$data_insert['type'] = htmlspecialchars($data['type']);
		$data_insert['teams'] = intval($data['company_size']);
		$data_insert['introduce'] = htmlspecialchars($data['company_introduce']);
		$data_insert['province'] = htmlspecialchars($data['province']);
		$data_insert['city'] = htmlspecialchars($data['city']);
		$data_insert['site'] = htmlspecialchars($data['url']);
		$data_insert['linkman'] = htmlspecialchars($data['person']);
		$data_insert['phone'] = htmlspecialchars($data['tele']);
		$data_insert['email'] = htmlspecialchars($data['email']);
		$data_insert['qq'] = htmlspecialchars($data['qq']);
		$data_insert['logo'] = intval($data['logo_company']);
		$data_insert['is_verify'] = 0;
		$data_insert['is_auth'] = 0;
		$data_insert['is_recommend'] = 0;
		$data_insert['is_top'] = 0;
		$data_insert['uid'] = 1;
		$data_insert['ctime'] = time();
		M("company")->data($data_insert)->where("company_id=".$company_id)->save();
		switch ($data_insert['type'])
		{
			case "5":
				M("company_cp")->where("company_id=".$company_id)->data(array("tags"=>$data['tags']))->save();
				break;  
			case "7":
				$data_7 = array();
				$data_7['types'] = implode(",",$data['channel_type']);
				$data_7['require'] = intval($data['platform']);
				$data_7['models'] = implode(",",$data['pricing_model']);
				$data_7['areas'] = implode(",",$data['cooperation_area']);
				M("company_channel")->where("company_id=".$company_id)->data($data_7)->save();
				break;
			case "8":
				$data_8 = array();
				$data_8['types'] = implode(",",$data['shop_type']);
				M("company_platform")->where("company_id=".$company_id)->data($data_8)->save();
				break;
			case "9":
				$data_9 = array();
				$data_9['areas'] = implode(",",$data['cooperation_area']);
				$data_9['platform_require'] = implode(",",$data['platform']);
				$data_9['network_require'] = intval($data['platform']);
				$data_9['stage_require'] = intval($data['game_phase']);
				$data_9['team_require'] = htmlspecialchars($data['game_team_require']);
				$data_9['is_experience'] = intval($data['experience']);
				$data_9['games'] = intval($data['has_game_num']);
				$data_9['price'] = intval($data['has_cost']);
				M("company_publish")->where("company_id=".$company_id)->data($data_9)->save();
				break;
			case "10":
				$data_10 = array();
				$data_10['wholes'] = implode(",",$data['product_outsourcing']);
				$data_10['arts'] = implode(",",$data['arts_outsourcing']);
				$data_10['musics'] = implode(",",$data['music_outsourcing']);
				$data_10['tests'] = implode(",",$data['test_outsourcing']);
				$data_10['programs'] = implode(",",$data['program_outsourcing']);
				$data_10['plans'] = implode(",",$data['plan_outsourcing']);
				M("company_outer")->where("company_id=".$company_id)->data($data_10)->save();
				break;
			case "11":
				$data_11 = array();
				$data_11['stage'] = implode(",",$data['investment_phase']);
				$data_11['require'] = htmlspecialchars($data['project_require']);
				$data_11['is_experience'] = intval($data['experience']);
				$data_11['games'] = intval($data['has_game_num']);
				$data_11['price'] = intval($data['has_cost']);
				M("company_investment")->where("company_id=".$company_id)->data($data_11)->save();
				break;
			case "12":
				$data_12 = array();
				$data_12['types'] = implode(",",$data['service_type']);
				M("company_service")->where("company_id=".$company_id)->data($data_12)->save();
				break;
			default:
		}
	}
	public function del(){
		$id = $_GET['id'];
		$company = M("company")->where("id=".$id)->find();
		if(empty($company)){
			$this->error();
		}
		switch ($company['type'])
		{
			case "5":
				M("company_cp")->where("company_id=".$id)->delete();
				break;  
			case "7":
				M("company_channel")->where("company_id=".$id)->delete();
				break;
			case "8":
				M("company_platform")->where("company_id=".$id)->delete();
				break;
			case "9":
				M("company_publish")->where("company_id=".$id)->delete();
				break;
			case "10":
				M("company_outer")->where("company_id=".$id)->delete();
				break;
			case "11":
				M("company_investment")->where("company_id=".$id)->delete();
				break;
			case "12":
				M("company_service")->where("company_id=".$id)->delete();
				break;
			default:
		}
		M("company")->where("id=".$id)->delete();
		$this->success();
	}



}