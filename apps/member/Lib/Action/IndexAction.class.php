<?php
/**
 * 通讯录后台管理
 */
// 加载后台控制器
class IndexAction extends Action
{
    function _initialize ()
    {

    }
    
    /**
     * 页面列表
     */
    public function index(){
    	$total_num = M("member")->count();
    	$this->assign("total_num",$total_num);
    	$company_type = M("option")->where(array("type"=>2))->select();
    	$this->assign("company_type",$company_type);
    	$position_type = M("option")->where(array("type"=>3))->select();
    	$this->assign("position_type",$position_type);
    	$self_id = $this->mid;
    	$member = M("member")->field("is_auth")->where(array("uid"=>$self_id))->find();
    	if($member){
    		$this->assign("has_member",1);
    		$this->assign("member",$member);
    	}else{
    		$this->assign("has_member",0);
    	}
    	$map = array();
    	if(!empty($_GET["city"])){
    		$map["c.city"] = $_GET["city"];
    	}
    	if(!empty($_GET["position_type"])){
    		$map["o.id"] = $_GET["position_type"];
    	}
    	if(!empty($_GET["company_type"])){
    		$map["c.type"] = $_GET["company_type"];
    	}
    	if(!empty($_GET["order"])){
    		if($_GET["order"]=="time"){
    			$order = "m.ctime desc";
    			$this->assign('order','time');
    		}elseif($_GET["order"]=="verify"){
    			$order = "m.is_verify desc";
    			$this->assign('order','verify');
    		}elseif($_GET["order"]=="views"){
    			$order = "m.views desc";
    			$this->assign('order','views');
    		}else{
    			$order = "m.collects desc";
    			$this->assign('order','collects');
    		}
    	}else{
    		$order = "m.id desc";
    	}
    	if(!empty($_GET["keyword"])){
    		$where['m.name']  = array('like', '%'.$_GET["keyword"].'%');
    		$where['c.name']  = array('like', '%'.$_GET["keyword"].'%');
    		$where['_logic'] = 'or';
    		$map['_complex'] = $where;
    	}
    	$tmp = M("member m")->join("left join yx_option o on(m.position_type = o.id)");
    	$tmp = $tmp->join("left join yx_company c on(c.id=m.company_id)")->join("left join yx_option o1 on(o1.id=c.type)");
    	$list = $tmp->where($map)->field("m.id,c.name as company_name,m.name,m.position,m.avatar")->order($order)->select();
    	
    	foreach ($list as $k => $v){
    		//dump($v["avatar"]);
    		$avatar_data = model("attach")->getAttachByIds($v["avatar"]);
    		//dump($avatar_data);
    		$list[$k][avatar_url] = UPLOAD_URL."/".$avatar_data[0]['save_path'].$avatar_data[0]['save_name'];
    	}
    	//dump($list);
    	$this->assign("list",$list);
    	$this->display();
    }
    public function mycard(){
        $self_id = $this->mid;
        $member = M("member m")->join("left join yx_company c on(c.id = m.company_id) ")->where("m.uid=".$self_id);
        $member = $member->field("m.id,m.name,m.sex,m.position,m.phone,m.email,m.qq,m.wechat,m.weibo,m.avatar,m.is_verify,m.is_auth,m.company_id,m.address as myadress,c.name as company_name,c.province,c.city,m.avatar,c.is_verify as c_is_verify,c.site,c.address,c.type,m.position_type")->find();
        //dump(M()->getLastSql());die;
        $province = M("area")->getByArea_id($member['province']);
        $city = M("area")->getByArea_id($member['city']);
        //dump($province);die;
        $this->assign("province",$province);
        $this->assign("city",$city);
        if(!empty($member['avatar'])){
    		$avatar_data = model("attach")->getAttachByIds(	$member["avatar"]);
    		
    		//dump($avatar);
    		$member['avatar'] = UPLOAD_URL."/".$avatar_data[0]['save_path'].$avatar_data[0]['save_name'];
               //dump($member['avatar']);
        }
        $member_company = M("member_company")->where("member_id = ".$self_id)->select();
        //dump(M()->getLastSql());die;
        //dump($member_company);die;
        /*
        $position_type = M("option")->getById($member['position_type']);
        $this->assign("position_type",$position_type);
        */
        $this->assign("member_company",$member_company);
        $this->assign("member",$member);
        //dump($member);
        $this->display();
    }
    public function edit(){
        $self_id = $this->mid;
        //dump($this->mid);die;
        //$info = M("member")->getByUid($self_id);

        $member = M("member m")->join("left join yx_company c on(c.id = m.company_id) ")->where("m.uid=".$self_id);
        $member = $member->field("m.id,m.name,m.sex,m.position,m.phone,m.email,m.qq,m.wechat,m.weibo,m.avatar,m.is_verify,m.is_auth,m.company_id,c.name as company_name,c.province,c.city,m.avatar,c.is_verify as c_is_verify,c.site,c.address,c.type,m.position_type")->find();
        //$member = $member->find();
        //dump($member);die;
        //if(empty($member)){
            //$this->error("你所选择的通讯录不存在");
        //}
        $province_arr = M("area")->where("pid=0")->field("area_id,title")->select();
        if(!empty($member['province'])){
            $city_arr = M("area")->where("pid = ".$member['province'])->field("area_id,title")->select();
        }
        $this->assign("province_arr",$province_arr);
        $this->assign("city_arr",$city_arr);
        $member['company_city'] = $province.$city;
        if(!empty($member['avatar'])){
            $avatar = M("attach")->where("attach_id=".$member['avatar'])->find();
            //dump($avatar);
            $member['avatar'] =  array();
            $member['avatar']['id'] = $avatar['attach_id'];
            $member['avatar']['url'] = UPLOAD_URL."/".$avatar['save_path'].$avatar['save_name'];
            $member['avatar']['height'] = $avatar['height'];
            $member['avatar']['width'] = $avatar['width'];
            //dump($member['avatar']);
        }
        $member_company = M("member_company")->where("member_id = ".$id)->select();
        //dump($member_company);
        $company_type = M("option")->where("type=2")->order("sort asc")->field("id,title")->select();
        //dump($company);
        $this->assign("company_type",$company_type);
        $position_type = M("option")->where("type=3")->order("sort asc")->field("id,title")->select();
        //dump($company);
        $this->assign("position_type",$position_type);
        $this->assign("member_company",$member_company);
        $this->assign("member",$member);



        //dump($info);die;
        $this->assign("self_id",$self_id);




        $this->display("edit");              
    }
    public function ajax_qzjl(){
        $id = intval($_GET['id']);
        if(empty($id)){
            echo(json_encode(array("status"=>1,"msg"=>"id为空")));die;
        }
        $member = M("member m")->join("left join yx_company c on(c.id = m.company_id) ")->where("m.id=".$id);
        if(empty($member)){
            echo(json_encode(array("status"=>1,"msg"=>"你所选择的通讯录不存在")));die;
        }
        $new_id = M("member_company")->data(array("member_id"=>$id,"name"=>htmlspecialchars($_POST['name_input']),"position"=>htmlspecialchars($_POST['position_input']),"product"=>htmlspecialchars($_POST['product_input'])))->add();
        echo(json_encode(array("status"=>1,"msg"=>"添加成功","new_id"=>$new_id)));die;
    }
    public function ajax_qzjl_del(){
        $id = intval($_GET['id']);
        if(empty($id)){
            echo(json_encode(array("status"=>1,"msg"=>"id为空")));die;
        }
        $member_company = M("member_company")->where("id=".$id)->find();
        if(empty($member_company)){
            echo(json_encode(array("status"=>1,"msg"=>"该条职业经历不存在")));die;
        }
        M("member_company")->where("id=".$id)->delete();
        echo(json_encode(array("status"=>1,"msg"=>"删除成功")));die;
    }
    public function doedit(){
        $id = intval($_GET['id']);
        $data = array();

        $company_ret = model('Company')->setCompany();
        //$company_id = $company_ret['id'];
        $data['company_id'] = $company_ret['id'];
        $data['avatar'] = $_POST['active_img'];
        $data['name'] = $_POST['name'];
        $data['sex'] = $_POST['sex'];
        $data['position'] = $_POST['position'];
        $data['position_type'] = $_POST['position_type'];
        $data['phone'] = $_POST['phone'];
        $data['email'] = $_POST['email'];
        $data['qq'] = $_POST['qq'];
        $data['wechat'] = $_POST['wechat'];
        $data['weibo'] = $_POST['weibo'];
        if(empty($id)){
            $data['uid'] = $this->mid;
            $data['ctime'] = time();
            M("member")->data($data)->add();
            //dump(M()->getLastSql());die;
            $this->success("修改成功！");
        }else{
            $member = M("member m")->where("m.id=".$id);
            $member = $member->find();
            //$member = $member->find();
            //dump($member);die;
            if(empty($member)){
                $this->error("你所选择的通讯录不存在");
            }
            //dump($data);die;
            M("member")->data($data)->where("id=".$id)->save();
            //dump(M()->getLastSql());die;
            $this->success("修改成功！");
        }

    }
    public function detail(){
    	$id = $_GET["id"];
    	if(empty($id)){
    		$this->error("参数错误!");
    	}
    	$self_id = $this->mid;
    	$member = M("member m")->join("left join yx_company c on(c.id = m.company_id) ")->where("m.id=".$id);
    	$member = $member->field("m.id,m.name,m.sex,m.position,m.phone,m.email,m.qq,m.wechat,m.weibo,m.avatar,m.is_verify,m.is_auth,m.company_id,m.address as myadress,c.name as company_name,c.province,c.city,m.avatar,c.is_verify as c_is_verify,c.site,c.address,c.type,m.position_type")->find();
    	//dump(M()->getLastSql());die;
    	$province = M("area")->getByArea_id($member['province']);
    	$city = M("area")->getByArea_id($member['city']);
    	//dump($province);die;
    	$this->assign("province",$province);
    	$this->assign("city",$city);
    	if(!empty($member['avatar'])){
    		$avatar_data = model("attach")->getAttachByIds($member["avatar"]);
    		
    		//dump($avatar_data);
    		$member['avatar'] = UPLOAD_URL."/".$avatar_data[0]['save_path'].$avatar_data[0]['save_name'];
    		//dump($member['avatar']);
    	}
    	
    	M('Member')->where('id='.$id)->setInc('views'); //加浏览量
    	
    	$member_company = M("member_company")->where("member_id = ".$self_id)->select();
    	$this->assign("member_company",$member_company);
    	//dump($member);
    	$this->assign("member",$member);
    	$this->display();
    }
    public function addcollect(){
    	$id = $_GET["id"];
    	$self_id = $this->mid;
    	if(empty($id)){
    		$this->error("参数错误");
    	}
    	$member = M("member")->where(array("id"=>$id))->find();
    	if(empty($member)){
    		$this->error("你要收藏的通讯录不存在");
    	}
    	if($self_id==$member["uid"]){
    		$this->error("不能收藏自己的通讯录");
    	}
 
    	$data = array();
    	$data["uid"] = $self_id;
    	$data["source_id"] = $id;
    	$data["source_table_name"] = "member";
    	$data["source_app"] = "member";
    	$collection = M("collection")->where($data)->find();
    	if(!empty($collection)){
    		$this->error("收藏失败,已经收藏!");
    	}
    	$data["ctime"] = time();
    	M("collection")->data($data)->add();
    	$this->success("收藏成功!");
    }
}
?>