<?php
/**
 * 公司后台管理
 */
// 加载后台控制器
tsload(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');
class AdminAction extends AdministratorAction
{
    function _initialize ()
    {
    	$this->pageTitle['index'] = '公司管理';
        
        // tab选项
        $this->pageTab[] = array('title' => '公司管理' , 'tabHash' => 'index' , 'url' => U('company/Admin/index'));
        parent::_initialize();
    }
    
    /**
     * 页面列表
     */
    public function index ()
    {
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
        $company_ret = model('Company')->setCompany();
        //$this->addcom();
        $this->success();
    }
    public function get_city(){
        $province_id = intval($_GET['province_id']);
        $city = M("area")->where("pid=".$province_id)->field("area_id,title")->select();
        echo(json_encode($city));die;
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
        $company_ret = model('Company')->setCompany();
        //$this->editcom($company_id);
        $this->success();
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
    public function auth(){
    	$id = intval($_GET["id"]);
    	if(empty($id)){
    		$this->error("参数错误");
    	}
    	$auth = M("company")->where(array("id"=>$id))->field("is_auth,is_verify")->find();
    	if(empty($auth)){
    		$this->error("你索选择的公司不存在");
    	}
//     	dump($auth);
    	$this->assign("auth",$auth);
    	$this->display();
    }
    public function doauth(){
    	$id = intval($_GET["company_id"]);
    	if(empty($id)){
    		$this->error("参数错误");
    	}
    	$auth = M("company")->field("is_auth,is_verify")->where(array("id"=>$id))->find();
    	if(empty($auth)){
    		$this->error("你索选择的公司不存在");
    	}
    	if(!M("company")->create()){
    		$this->error(M("company")->getError());
    	}
    	M("company")->where(array("id"=>$id))->save();
//     	dump(M()->getLastSql());die;
    	$this->success();
    }
    public function has_pub(){
    	$id = intval($_GET["id"]);
    	if(empty($id)){
    		$this->error("参数错误");
    	}
    	$company = M("company")->field("id,name")->where(array("id"=>$id))->find();
    	if(empty($company)){
    		$this->error("参数错误,你索选择的公司不存在");
    	}
    	$has_pub = M("company_has_pub")->where(array("company_id"=>$id))->field("id,icon,area,platform,cost,name")->select();
    	$cooperation_area = C("cooperation_area");
    	$platform = C("platform");
    	$this->assign("cost",C("has_cost"));
    	foreach ($has_pub as $k => $v){
    		$has_pub[$k]["icon"] = model("attach")->getAttachById($v["icon"]);
    		// 					$has_pub_game[$k]["area_show"] =
    		$area_show = explode(",", $v["area"]);
    		// 					dump($area_show);die;
    		$tmp_11 = array();
    		foreach ($area_show as $ke => $va){
    			// 						dump($cooperation_area[$va]);
    	
    			$tmp_11[] = $cooperation_area[$va];
    		}
    		$has_pub[$k]["area_show"] = join(" ",$tmp_11);
    			
    		$platform_show = explode(",", $v["platform"]);
    		// 					dump($area_show);die;
    		$tmp_11 = array();
    		foreach ($platform_show as $ke => $va){
    			// 						dump($cooperation_area[$va]);
    				
    			$tmp_11[] = $platform[$va];
    		}
    		$has_pub[$k]["platform_show"] = join(" ",$tmp_11);
    			
    		//die;
    	}
    	//dump($has_pub);//die;
    	$this->assign("has_pub",$has_pub);
    	$this->display();
    }
    public function del_has_pub(){
    	$id = $_GET["id"];
    	if(empty($id)){
    		$this->error("参数错误");
    	}
    	M("company_has_pub")->where(array("id"=>$id))->delete();
    	$this->success();
    }
    public function add_has_pub(){
    	$company_id = $_GET["company_id"];
    	if(empty($company_id)){
    		$this->error("参数错误!");
    	}
    	$company = M("company")->getById($company_id);
    	if(empty($company)){
    		$this->error("你所选择的公司不存在");
    	}
    	$this->assign("cooperation_area",C("user_cover"));
    	$this->assign("platform",C("platform"));
    	$this->assign("company_id",$company_id);
    	$this->assign("has_cost",C("has_cost"));
    	$this->display();
    }
    public function doadd_has_pub(){
//     	dump($_POST);
    	$company_id = $_GET["company_id"];
    	if(empty($company_id)){
    		$this->error("参数错误!");
    	}
    	$company = M("company")->getById($company_id);
    	if(empty($company)){
    		$this->error("你所选择的公司不存在");
    	}
    	$data = array();
    	$data["company_id"] = $company_id;
    	$data["icon"] = $_POST["logo_company"];
    	$data["area"] = join(",",$_POST["company_widget_game_area"]);
    	$data["platform"] = implode(",",$_POST["company_widget_game_platform"]);
    	$data["cost"] = t($_POST["company_widget_game_cost"]);
    	$data["name"] = t($_POST["company_widget_game_name"]);
    	$model_has_pub = M("company_has_pub");
    	if(!$model_has_pub->create($data)){
    		$this->error($model_has_pub->getError());
    	}
//     	dump($data);
    	$model_has_pub->add($data);
//     	dump(M()->getLastSql());die;
    	$this->success("成功");
//     	    	dump($_POST);
    }
}
?>