<?php
/**
 * 通讯录后台管理
 */
// 加载后台控制器
tsload(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');
class AdminAction extends AdministratorAction
{
    function _initialize ()
    {
    	$this->pageTitle['index'] = '通讯录管理';
        
        // tab选项
        $this->pageTab[] = array('title' => '通讯录管理' , 'tabHash' => 'index' , 'url' => U('member/Admin/index'));
        parent::_initialize();
    }
    
    /**
     * 页面列表
     */
    public function index ()
    {
        /*
        $map = array();
        $company_type = M("option")->where("type=2")->order("sort asc")->field("id,title")->select();
        //dump($company_type);
        $this->assign("company_type",$company_type);
        $position_type = M("option")->where("type=3")->order("sort asc")->field("id,title")->select();
        //dump($company_type);
        $this->assign("position_type",$position_type);
        $list = M("member m")->field("m.id,m.name as ture_name,m.sex,m.position,m.position_type,m.phone,m.email,m.qq,m.wechat,m.weibo,m.avatar,m.is_verify,m.uid,m.ctime,c.name as company_name,u.uname,o.title,m.is_verify")->join("left join yx_option o on(m.position_type = o.id)")->join("left join yx_user u on(u.uid=m.uid)")->join("left join yx_company c on(c.id = m.company_id)")->limit(10)->where($map)->select();
        //dump(M()->getLastSql());die;
        dump($list);//die;
        $this->assign("list",$list);
        $this->display();
        */
        $_REQUEST['tabHash'] = 'index';
        //按钮
        //$this->pageButton[] = array('uid','title'=>'搜索', 'onclick'=>"admin.fold('search_form')");
        //$this->pageButton[] = array('uid','title'=>'删除', 'onclick'=>"admin.deleteInfo();");
        $this->pageButton[] = array('uid','title'=>'添加通讯录', 'onclick'=>"location.href='".U('member/Admin/add',array('tabHash'=>'add'))."';");
        $map = array();
        //构造搜索条件
        //列表key值 DOACTION表示操作
        $this->pageKeyList = array('id','apply_name','apply_time','status','true_name','company_name','position','phone','email','QQ','detail','DOACTION');
        $listData = M('member m')->where($map)->field("m.id,m.name as ture_name,m.sex,m.position,m.position_type,m.phone,m.email,m.qq,m.wechat,m.weibo,m.avatar,m.is_verify,m.is_auth,m.uid,m.ctime,c.name as company_name,u.uname,o.title,m.is_verify,u.domain")->join("left join yx_option o on(m.position_type = o.id)")->join("left join yx_user u on(u.uid=m.uid)")->join("left join yx_company c on(c.id = m.company_id)")->order('id desc')->findPage(15);
        //dump($listData);
        $dAvatar = model('Avatar');
        foreach ($listData['data'] as $key => $val)
        {
            $listData['data'][$key]['id'] = $val['id'];
            $thumb = APPS_URL.'/'.APP_NAME.'/_static/nopic.jpg';
            if ($val['logo'])
            {
                $attach = model('Attach')->getAttachById($val['logo']);
                if ($attach)
                {
                    $thumb = getImageUrl($attach['save_path']. $attach['save_name'],100,100,true);   
                }
            }
            $listData['data'][$key]['id'] = $val['id'];
            $listData['data'][$key]['apply_name'] = $val['uname']?$val['uname']:'<a href="'.U('member/admin/bind',array('id' => $val['id'],'tabHash'=>'auth')).'">绑定用户</a>';
            $listData['data'][$key]['apply_time'] =date('Y-m-d',$val['ctime']);
            switch ($val['is_verify'])
            {
                case 0:
                    $is_verify = "未提交审核";
                    break;  
                case 1:
                    $is_verify = "提交审核";
                    break;
                case 2:
                    $is_verify = "审核未通过";
                    break;
                case 3:
                    $is_verify = "审核通过";
                    break;
                default:
                    $is_verify = "未提交审核";
            }
            switch ($val['is_auth'])
            {
                case 0:
                    $is_auth = "未提交认证";
                    break;  
                case 1:
                    $is_auth = "提交认证";
                    break;
                case 2:
                    $is_auth = "认证未通过";
                    break;
                case 3:
                    $is_auth = "认证通过";
                    break;
                default:
                    $is_auth = "未提交认证";
            }
            $listData['data'][$key]['status'] = $is_auth.",".$is_verify;
            $listData['data'][$key]['true_name'] = $val['ture_name'];
            $listData['data'][$key]['company_name'] = $val['company_name'];
            $listData['data'][$key]['position'] = $val['position'];
            $listData['data'][$key]['phone'] = $val['phone'];
            $listData['data'][$key]['email'] = $val['email'];
            $listData['data'][$key]['QQ'] = $val['qq'];
            $listData['data'][$key]['website'] = $val['domain'];
            $listData['data'][$key]['detail'] = '<a href="'.U('member/admin/detail',array('id' => $val['id'],'tabHash'=>'detail')).'">详情</a> ';
            $listData['data'][$key]['DOACTION'] = '<a href="'.U('member/admin/edit',array('id' => $val['id'],'tabHash'=>'edit')).'">编辑</a>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.U('member/admin/dodel',array('id' => $val['id'],'tabHash'=>'dodel')).'">删除</a>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.U('member/admin/auth',array('id' => $val['id'],'tabHash'=>'auth')).'">审核认证</a>
                                                    ';
        }
        $this->displayList($listData);
    }
    public function detail(){
        $id = intval($_GET['id']);
        if(empty($id)){
            $this->error("参数错误");
        }
        $member = M("member m")->join("left join yx_company c on(c.id = m.company_id) ")->where("m.id=".$id);
        $member = $member->field("m.id,m.name,m.sex,m.position,m.phone,m.email,m.qq,m.wechat,m.weibo,m.avatar,m.is_verify,m.is_auth,m.company_id,c.name as company_name,c.province,c.city,m.avatar")->find();
        //$member = $member->find();
        if(empty($member)){
            $this->error("你所选择的通讯录不存在");
        }
        $province = M("area")->where("area_id=".$member["province"])->getField("title");
        $city = M("area")->where("area_id=".$member["city"])->getField("title");
        $member['company_city'] = $province.$city;
        if(!empty($member['avatar'])){
            $avatar = M("attach")->where("attach_id=".$member['avatar'])->find();
            //dump($avatar);
            $member['avatar'] =  array();
            $member['avatar']['url'] = UPLOAD_URL."/".$avatar['save_path'].$avatar['save_name'];
            $member['avatar']['height'] = $avatar['height'];
            $member['avatar']['width'] = $avatar['width'];
            //dump($member['avatar']);
        }
        $member_company = M("member_company")->where("member_id = ".$id)->select();
        //dump($member_company);
        //dump($member);
        $this->assign("member_company",$member_company);
        $this->assign("member",$member);
        $this->display();
    }
    public function edit(){
        $id = intval($_GET['id']);
        if(empty($id)){
            $this->error("参数错误");
        }
        $member = M("member m")->join("left join yx_company c on(c.id = m.company_id) ")->where("m.id=".$id);
        $member = $member->field("m.id,m.name,m.sex,m.position,m.phone,m.email,m.qq,m.wechat,m.weibo,m.avatar,m.is_verify,m.is_auth,m.company_id,c.name as company_name,c.province,c.city,m.avatar,c.is_verify as c_is_verify,c.site,c.address,c.type,m.position_type")->find();
        //$member = $member->find();
        //dump($member);die;
        if(empty($member)){
            $this->error("你所选择的通讯录不存在");
        }
        $province_arr = M("area")->where("pid=0")->field("area_id,title")->select();
        $city_arr = M("area")->where("pid = ".$member['province'])->field("area_id,title")->select();
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
        $this->display();
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
        if(empty($id)){
            $this->error("参数错误");
        }
        $member = M("member m")->where("m.id=".$id);
        $member = $member->find();
        //$member = $member->find();
        //dump($member);die;
        if(empty($member)){
            $this->error("你所选择的通讯录不存在");
        }
        $data = array();

        $company_ret = model('Company')->setCompany();
        //$company_id = $company_ret['id'];
        $data['company_id'] = $company_ret['id'];

        /*
        $company_name = $_POST['company_name'];
        $res = M("company")->where("name = '".$company_name."'")->find();
        $data = array();
        if(empty($res)){
            $data['company_id'] = $this->addcom();

        }else{
            $data['company_id'] = $res['id'];
            if($res['is_verify']!=3){
                //dump($data['company_id']);die;
                $this->editcom($data['company_id']);
            }
        }
        */
        $data['avatar'] = $_POST['member_avatar'];
        $data['name'] = $_POST['name'];
        $data['position'] = $_POST['position'];
        $data['position_type'] = $_POST['position_type'];
        $data['phone'] = $_POST['phone'];
        $data['email'] = $_POST['email'];
        $data['qq'] = $_POST['qq'];
        $data['wechat'] = $_POST['wechat'];
        $data['weibo'] = $_POST['weibo'];
        //dump($data);die;
        M("member")->data($data)->where("id=".$id)->save();
        //dump(M()->getLastSql());die;
        $this->success("修改成功！");
    }
    public function dodel(){
        $id = intval($_GET['id']);
        if(empty($id)){
            $this->error("参数错误");
        }
        $member = M("member m")->where("m.id=".$id);
        $member = $member->find();
        //$member = $member->find();
        //dump($member);die;
        if(empty($member)){
            $this->error("你所选择的通讯录不存在");
        }
        M("member")->where("id=".$id)->delete();
        M("member_company")->where("member_id=".$id)->delete();
        $this->success("删除成功");
    }
    public function add(){
        $position_type = M("option")->where("type=3")->order("sort asc")->field("id,title")->select();
        //dump($company);
        $this->assign("position_type",$position_type);
        $this->display();
    }
    public function doadd(){
        //dump($_POST);die;
        $company_name = $_POST['company_name'];
        $res = M("company")->where("name = '".$company_name."'")->find();
        $data_member = array();
        //dump($res);die;
        /*
        if(empty($res)){
            $data_member['company_id'] = $this->addcom();
        }else{
            $data_member['company_id'] = $res['id'];
        }
        */
        $company_ret = model('Company')->setCompany();
        //$company_id = $company_ret['id'];
        $data_member['company_id'] = $company_ret['id'];



        $data_member['avatar'] = $_POST['member_avatar'];
        $data_member['name'] = $_POST['name'];
        $data_member['position'] = $_POST['position'];
        $data_member['position_type'] = $_POST['position_type'];
        $data_member['phone'] = $_POST['phone'];
        $data_member['email'] = $_POST['email'];
        $data_member['qq'] = $_POST['qq'];
        $data_member['sex'] = 0;
        $data_member['wechat'] = $_POST['wechat'];
        $data_member['weibo'] = $_POST['weibo'];
        $new_member_id = M("member")->data($data_member)->add();
        $this->assign("member_id",$new_member_id);
        $this->display();
    }
    public function auth(){
        $id = intval($_GET['id']);
        if(empty($id)){
            $this->error("参数错误");
        }
        $member = M("member m")->where("m.id=".$id)->find();
        //$member = $member->find();
        //dump($member);die;
        if(empty($member)){
            $this->error("你所选择的通讯录不存在");
        }

        $this->assign("member",$member);
        $this->display();
    }
    public function bind(){
        $id = intval($_GET['id']);
        if(empty($id)){
            $this->error("参数错误");
        }
        $member = M("member m")->where("m.id=".$id)->find();
        //$member = $member->find();
        //dump($member);die;
        if(empty($member)){
            $this->error("你所选择的通讯录不存在！");
        }
        if(!empty($member['uid'])){
            $this->error("你所选择的通讯录已经绑定了用户！");
        }
        if(!empty($_GET['uid'])){
            $member_has_bind = M("member")->where("uid=".$_GET['uid'])->find();
            if(!empty($member_has_bind)){
                $this->error("该用户已经被绑定");
            }
            $user = M("user")->where("uid=".$_GET['uid'])->find();
            if(empty($user)){
                $this->error("该用户不存在");
            }
            M("member")->where("id=".$id)->data(array("uid"=>$_GET['uid'],"ctime"=>time()))->save();
            $_SERVER["HTTP_REFERER"] = U('member/Admin/index');
            $this->success("绑定成功");
        }
        $this->assign("member",$member);
        $this->display();
    }
    public function addcode(){
        $id = $_GET['id'];
        $inviteCode = tsmd5("1".microtime(true).rand(1111,9999)."1");
        $data = array();
        $data['code'] = $inviteCode;
        $data['inviteCode'] = 1;
        $data['is_admin'] = 1;
        $data['type'] = "link";
        $data['is_used'] = 0;
        $res = M("invite_code")->data($data)->add();
        if($res){
            echo(json_encode(array("status"=>1,"code"=>$inviteCode)));die;
        }else{
            echo(json_encode(array("status"=>0)));die;
        }
    }
    /*
    private function addcom(){
        $data = $_POST;
        $data_insert = array();
        $data_insert['name'] = htmlspecialchars($data['company_name']);
        $data_insert['type'] = htmlspecialchars($data['company_type']);
        $data_insert['teams'] = intval($data['company_size']);
        $data_insert['introduce'] = htmlspecialchars($data['company_introduce']);
        $data_insert['province'] = htmlspecialchars($data['company_province']);
        $data_insert['city'] = htmlspecialchars($data['company_city']);
        $data_insert['site'] = htmlspecialchars($data['company_site']);
        $data_insert['linkman'] = htmlspecialchars($data['company_linkman']);
        $data_insert['phone'] = htmlspecialchars($data['company_phone']);
        $data_insert['email'] = htmlspecialchars($data['company_email']);
        $data_insert['qq'] = htmlspecialchars($data['company_qq']);
        $data_insert['is_verify'] = 0;
        $data_insert['is_auth'] = 0;
        $data_insert['is_recommend'] = 0;
        $data_insert['is_top'] = 0;
        $data_insert['uid'] = 1;
        $data_insert['ctime'] = time();
        //dump($data_insert);die;

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
                //dump($data_12);die;
                M("company_service")->data($data_12)->add();
                //dump(M()->getLastSql());die;
                break;
            default:
        }
        return $new_id;
    }
    private function editcom($id){
        $data = $_POST;
        $data_insert = array();
        $data_insert['name'] = htmlspecialchars($data['company_name']);
        $data_insert['type'] = htmlspecialchars($data['company_type']);
        $data_insert['teams'] = intval($data['company_size']);
        $data_insert['introduce'] = htmlspecialchars($data['company_introduce']);
        $data_insert['province'] = htmlspecialchars($data['company_province']);
        $data_insert['city'] = htmlspecialchars($data['company_city']);
        $data_insert['site'] = htmlspecialchars($data['company_site']);
        $data_insert['linkman'] = htmlspecialchars($data['company_linkman']);
        $data_insert['phone'] = htmlspecialchars($data['company_phone']);
        $data_insert['email'] = htmlspecialchars($data['company_email']);
        $data_insert['qq'] = htmlspecialchars($data['company_qq']);
        M("company")->data($data_insert)->where("id=".$id)->save();
        //dump($data_insert);die;
        $company_id = $id;
        $type = $data_insert['type'];
        switch ($data_insert['type'])
        {
            case "5":
                $company_cp = M("company_cp")->where("company_id=".$company_id)->find();
                if(empty($company_cp)){
                    M("company_cp")->data(array("tags"=>$data['tags'],"company_id"=>$company_id))->add();
                }else{
                    M("company_cp")->where("company_id=".$company_id)->data(array("tags"=>$data['tags']))->save();
                }
                break;  
            case "7":
                $model_tmp =  M("company_channel");
                $data_tmp = array();
                $data_tmp['types'] = implode(",",$data['channel_type']);
                $data_tmp['require'] = intval($data['platform']);
                $data_tmp['models'] = implode(",",$data['pricing_model']);
                $data_tmp['areas'] = implode(",",$data['cooperation_area']);
                //dump($data_7);die;
                $company_tmp = $model_tmp->where("company_id=".$company_id)->find();
                if(empty($company_tmp)){
                    $data_tmp['company_id'] = $company_id;
                    $model_tmp->data($data_tmp)->add();
                }else{
                    $model_tmp->where("company_id=".$company_id)->data($data_tmp)->save();
                }
                break;
            case "8":
                $model_tmp =  M("company_platform");
                $data_tmp = array();
                $data_tmp['types'] = implode(",",$data['shop_type']);
                $company_tmp = $model_tmp->where("company_id=".$company_id)->find();
                if(empty($company_tmp)){
                    $data_tmp['company_id'] = $company_id;
                    $model_tmp->data($data_tmp)->add();
                }else{
                    $model_tmp->where("company_id=".$company_id)->data($data_tmp)->save();
                }
                break;
            case "9":
                $model_tmp =  M("company_publish");
                $data_tmp = array();
                $data_tmp['areas'] = implode(",",$data['cooperation_area']);
                $data_tmp['platform_require'] = implode(",",$data['platform']);
                $data_tmp['network_require'] = intval($data['platform']);
                $data_tmp['stage_require'] = intval($data['game_phase']);
                $data_tmp['team_require'] = htmlspecialchars($data['game_team_require']);
                $data_tmp['is_experience'] = intval($data['experience']);
                $data_tmp['games'] = intval($data['has_game_num']);
                $data_tmp['price'] = intval($data['has_cost']);
                $company_tmp = $model_tmp->where("company_id=".$company_id)->find();
                if(empty($company_tmp)){
                    $data_tmp['company_id'] = $company_id;
                    $model_tmp->data($data_tmp)->add();
                }else{
                    $model_tmp->where("company_id=".$company_id)->data($data_tmp)->save();
                }
                break;
            case "10":
                $model_tmp =  M("company_outer");
                $data_tmp = array();
                $data_tmp['wholes'] = implode(",",$data['product_outsourcing']);
                $data_tmp['arts'] = implode(",",$data['arts_outsourcing']);
                $data_tmp['musics'] = implode(",",$data['music_outsourcing']);
                $data_tmp['tests'] = implode(",",$data['test_outsourcing']);
                $data_tmp['programs'] = implode(",",$data['program_outsourcing']);
                $data_tmp['plans'] = implode(",",$data['plan_outsourcing']);
                $company_tmp = $model_tmp->where("company_id=".$company_id)->find();
                if(empty($company_tmp)){
                    $data_tmp['company_id'] = $company_id;
                    $model_tmp->data($data_tmp)->add();
                }else{
                    $model_tmp->where("company_id=".$company_id)->data($data_tmp)->save();
                }
                break;
            case "11":
                $model_tmp =  M("company_investment");
                $data_tmp = array();
                $data_tmp['stage'] = implode(",",$data['investment_phase']);
                $data_tmp['require'] = htmlspecialchars($data['project_require']);
                $data_tmp['is_experience'] = intval($data['experience']);
                $data_tmp['games'] = intval($data['has_game_num']);
                $data_tmp['price'] = intval($data['has_cost']);
                $company_tmp = $model_tmp->where("company_id=".$company_id)->find();
                if(empty($company_tmp)){
                    $data_tmp['company_id'] = $company_id;
                    $model_tmp->data($data_tmp)->add();
                }else{
                    $model_tmp->where("company_id=".$company_id)->data($data_tmp)->save();
                }
                break;
            case "12":
                $model_tmp =  M("company_service");
                $data_tmp = array();
                $data_tmp['types'] = implode(",",$data['service_type']);
                $company_tmp = $model_tmp->where("company_id=".$company_id)->find();
                if(empty($company_tmp)){
                    $data_tmp['company_id'] = $company_id;
                    $model_tmp->data($data_tmp)->add();
                }else{
                    $model_tmp->where("company_id=".$company_id)->data($data_tmp)->save();
                }
                break;
            default:
        }
    }
    */
}
?>