<?php
/**
 * IndexAction
 * 活动
 * @uses Action
 * @package
 * @version $id$
 * @copyright 2009-2011 SamPeng
 * @author SamPeng <sampeng87@gmail.com>
 * @license PHP Version 5.2 {@link www.sampeng.cn}
 */
class IndexAction extends Action {
	private $appName;
    private $event;

    /**
     * __initialize
     * 初始化
     * @access public
     * @return void
     */
    public function _initialize() {
		//应用名称
		global $ts;
		$this->appName = $ts['app']['app_alias'];
        //设置活动的数据处理层
        $this->event = D( 'Event','event' );
        //读取推荐列表
        $is_hot_list = $this->event->getHotList();
        $this->assign('is_hot_list',$is_hot_list);
        // 活动分类
        $cate = D( 'EventType' )->getType();
        $this->assign( 'category',$cate );
    }

    /**
     * index
     * 首页
     * @access public
     * @return void
     */
    public function index() {
		$order = NULL;
        switch( $_GET['order'] ) {
        	case 'new':    //最新排行
       			$order = 'cTime DESC';
       			$this->setTitle('最新' . $this->appName);
                break;
            case 'following':    
                //关注的人的活动
				// $following = M('weibo_follow')->field('fid')->where("uid={$this->mid} AND type=0")->findAll();
				// foreach($following as $v) {
				// 	$in_arr[] = $v['fid'];
				// }
                // $map['uid'] = array('in',$in_arr);
            // 关注的活动
                $map['uid'] = $this->mid;
                $map['action'] = 'attention';
                $eventIds = M('event_user')->where($map)->field('eventId')->findAll();unset($map);
                foreach ($eventIds as $key => $value) {
                    $ids[$key] = $value['eventId'];
                }
                $map['id'] = array('IN',$ids);
                $this->setTitle('我关注的' . $this->appName);
                break;
	         default:      //默认热门排行
                $order = 'joinCount DESC,attentionCount DESC,cTime DESC';
                $this->setTitle('热门' . $this->appName);
        }

        //查询
        $key = t($_GET['key']);
        if ($key) {
        	$map['title'] = array( 'like',"%".$key."%" );
        	$this->assign('keys',$key);
        	$this->setTitle('搜索' . $this->appName);
        }
        if ($_GET['cid']) {
        	$map['type']  = intval($_GET['cid']);
        	$this->setTitle('分类浏览');
        }
        if ($_GET['city']) { //JTee 2014-1-27
        	//$map['city']  = intval($_GET['city']);
        	$this->setTitle('区域浏览');
        }

        $result  = $this->event->getEventList($map,$order,$this->mid,$_GET['order']);
        foreach($result['data'] as &$val){
        	$guest = D('Event_guest')->field('group_concat(name) as guests')->where('event_id='.$val['id'])->find();
        	$val['guest'] = $guest['guests'];
//         	$user = D('Event_user')->where('eventId ='.$val['id'])->order('id DESC')->limit(8)->select();
//         	$val['user'] = $user;
        }
		$this->assign($result);
		
		//$this->assign('count', $this->event->getEventcount($map));
		
		//JTee 2014-01-13 左侧数据
        $this->_leftData();
        
        //JTee 2014-01-13 置顶游戏
        $toplist = $this->event->where("is_top=1 and is_verify=3")->limit(3)->select();
    	$this->assign('toplist', $toplist);
        
        $this->display();
    }

    /**
     * personal
     * 个人列表
     * @access public
     * @return void
     */
    public function personal() {
    	if ($this->uid == $this->mid)
    		$name = '我';
    	else
    		$name = getUserName($this->uid);

        switch( $_GET['action'] ) {
            case 'join':    //参与的
                $map_join['action'] = 'joinIn';
                $map_join['status'] = 1;
                $map_join['uid']    = $this->uid;
                $eventIds  = D('EventUser')->field('eventId')->where($map_join)->findAll();
                foreach($eventIds as $v) {
                    $in_arr[] = $v['eventId'];
                }
                $map['id'] = array('in',$in_arr);
                $this->setTitle("{$name}参与的{$this->appName}");
                break;
            case 'att':    //关注的
                $map_att['action'] = 'attention';
                $map_att['status'] = 1;
                $map_att['uid']    = $this->uid;
                $eventIds  = D('EventUser')->field('eventId')->where($map_att)->findAll();
                foreach($eventIds as $v) {
                    $in_arr[] = $v['eventId'];
                }
                $map['id'] = array('in',$in_arr);
                $this->setTitle("{$name}关注的{$this->appName}");
                break;
         	default:      //发起的
                $map['uid'] = $this->uid;
                $this->setTitle("{$name}发起的{$this->appName}");
        }
        $result  = $this->event->getEventList($map,'id DESC',$this->mid);
        $this->assign($result);
        $this->assign('name', $name);
        $this->display();
    }

    /**
     * addEvent
     * 发起活动
     * @access public
     * @return void
     */
    public function addEvent() {
        $this->_createLimit($this->mid);
        
        $member = M("member")->field("is_verify")->getBYUid($this->mid);
        //dump($member);die;
        //var_dump(!empty($member));die;
        if(!empty($member)){
            $this->assign("has_exist","1");
        }else{
            $company_type = M("option")->where("type=2")->order("sort asc")->field("id,title")->select();
            //dump($company);
            $this->assign("company_type",$company_type);
            $position_type = M("option")->where("type=3")->order("sort asc")->field("id,title")->select();
            $this->assign("position_type",$position_type);
            $this->assign("has_exist","0");
        }

        $province_arr = M("area")->where("pid=0")->order("sort asc")->select();
        $this->assign("province_arr",$province_arr);
        //字段属性
        $types = D('Event_type')->select();
        $types_list = array();
        foreach($types as $t){
            $types_list[$t['id']] = $t['name'];
        }
        //dump($types_list);die;
        $this->assign("type",$types_list);
        $this->assign("organizer_type",tsconfig('organizer_type'));
        $this->assign("cost",tsconfig('event_cost'));
        $this->assign("is_verify",tsconfig('yesorno'));
        $this->assign("is_recommend",tsconfig('yesorno'));
        $this->assign("opts",tsconfig('yesorno'));


        $typeDao = D( 'EventType' );
        //$this->assign('type',$typeDao->getType());
        $this->setTitle('发起' . $this->appName);
        $this->display();
    }
	/**
     * _creatLimit
     * 条件限制判断
     * @access public
     * @return void
     */
    private function _createLimit($uid){
		$config = event_getConfig();
		if(!$config['canCreate']){
			$this->error('禁止发起'.$this->appName);
		}
    	if($config['credit']){
			$userCredit = model('Credit')->getUserCredit($uid);
    		if($userCredit['credit'][$config['credit_type']]['value']<$config['credit']){
    			$this->error($userCredit['credit'][$config['credit_type']]['alias'].'小于'.$config['credit'].'点，不允许发起'.$this->appName);
    		}
    	}
        $timeLimit = $config['limittime'];
    	if($timeLimit){
    	   $regTime = M('User')->getField('ctime',"uid={$uid}");
    	   $difference = (time()-$regTime)/3600;
    	   if($difference<$timeLimit){
    	       $this->error('帐号注册时间小于'.$config['limittime'].'小时，不允许发起'.$this->appName);
    	   }
    	}
    }
    /**
     * doAddEvent
     * 添加活动
     * @access public
     * @return void
     */
    public function doAddEvent() {
    	//echo '<meta http-equiv=Content-Type content="text/html;charset=utf-8">';
        //dump($_FILES);dump($_POST);die;
        //$data['attach_type'] =  'event';
        //$data['upload_type'] =  'image';
        
        $this->_createLimit($this->mid);
        $map['title']      = t($_POST['title']);
        $map['address']    = t($_POST['address']);
        $map['limitCount'] = intval(t($_POST['limitCount']));
        $map['type']       = intval($_POST['type']);
        $map['explain']    = h($_POST['explain']);
        $map['contact']    = t($_POST['contact']);
        $map['deadline']   = $this->_paramDate($_POST['deadline']);
        $map['sTime']      = $this->_paramDate($_POST['sTime']);
        $map['eTime']      = $this->_paramDate($_POST['eTime']);
        $map['sponsor']      = t($_POST['sponsor']);//主办方
        $map['organizer_type']      = t($_POST['organizer_type']);//协办or承办
        $map['organizer']      = t($_POST['organizer']);//承办商or协办商
        $map['media']    = t($_POST['media']);
        $map['traffic']      = t($_POST['traffic']);//交通路线
        $map['notice']      = keyWordFilter(t($_POST['notice']));//注意事项
        $map['uid']        = $this->mid;
        //echo "string";die;
        //$map['name']       = getUserName($this->mid);
        if(!t($_POST['title'])){
            $this->error("活动标题不能为空");
        }
        if(!t($_POST['address'])){
            $this->error("活动地址不能为空");
        }
        if(intval($_POST['type']) == 0){
            $this->error("请选择活动分类");
        }
        if( $map['sTime'] > $map['eTime'] ) {
            $this->error( "结束时间不得早于开始时间" );
        }
		if( $map['sTime'] < mktime(0, 0, 0, date('M'), date('D'), date('Y')) ) {
            $this->error( "开始时间不得早于当前时间" );
        }
        /*if( $map['deadline'] < time() ) {
            $this->error( "报名截止时间不得早于当前时间" );
        }
        if( $map['deadline'] > $map['eTime'] ) {
        	$this->error('报名截止时间不能晚于结束时间');
        }*/

		$string=iconv("UTF-8","GBK", t($map['explain']));
        $length = strlen($string);
        if($length < 20){
        	$this->error('介绍不得小于20个字符');
        }
        //var_dump($_POST['is_verify']);die;
        //数据操作之前进行添加通讯录及公司的验证
        if($_POST['is_verify']){
            if(!t($_POST['name'])){
                $this->error("通讯录姓名不能为空");
            }
            //公司字段验证开始
            if(!t($_POST['company_name'])){
                $this->error("公司名称不能为空");
            }
            if(!t($_POST['company_size'])){
                $this->error("团队规模未选择");
            }
            if(!t($_POST['company_introduce'])){
                $this->error("团队实力介绍不能为空");
            }
            if(!t($_POST['company_province'])){
                $this->error("公司所在省份未选择");
            }
            if(!t($_POST['company_city'])){
                $this->error("公司所在城市未选择");
            }
            if(!t($_POST['company_address'])){
                $this->error("公司地址不能为空");
            }
            if(!t($_POST['company_site'])){
                $this->error("公司官网或微博不能为空");
            }
            if(!t($_POST['company_linkman'])){
                $this->error("公司联系人不能为空");
            }
            if(!t($_POST['company_phone'])){
                $this->error("公司电话不能为空");
            }
            if(!t($_POST['company_email'])){
                $this->error("公司邮箱不能为空");
            }
            if(!t($_POST['company_qq'])){
                $this->error("公司QQ不能为空");
            }
            //公司验证结束
            if(!t($_POST['position'])){
                $this->error("职位不能为空");
            }
            if(!t($_POST['position_type'])){
                $this->error("职位类型未选择");
            }
            if(!t($_POST['phone'])){
                $this->error("通讯录电话不能为空");
            }
            if(!t($_POST['email'])){
                $this->error("通讯录邮箱不能为空");
            }
            if(!t($_POST['qq'])){
                $this->error("通讯录QQ不能为空");
            }
            /*貌似非必填
            if(!t($_POST['weibo'])){
                $this->error("通讯录微博不能为空");
            }
            if(!t($_POST['wechat'])){
                $this->error("通讯录微博不能为空");
            }
            */
            //todo 通讯录图片验证
        }



        //处理省份，市，区
        //list( $opts['province'],$opts['city'],$opts['area'] ) = explode(" ",safe($_POST['city']));
        $opts['province'] = t($_POST['province']);
        
        $opts['city'] = t($_POST['city']);

        //得到上传的图片
        //$data['attach_type'] =  'event';
        //$data['upload_type'] =  'image';
        //$cover = model('attach')->upload($data);

        //处理选项
        $opts['cost']        = intval( $_POST['cost'] );
        /*
        $opts['costExplain'] = t( $_POST['costExplain'] );
        $opts['costExplain'] = keyWordFilter(t($_POST['costExplain']));
        
        $friend              = isset( $_POST['friend'] )?1:0;
        $allow               = isset( $_POST['allow'] )?1:0;
        $opts['opts']        = array( 'friend'=>$friend,'allow'=>$allow );
        */


//         if( $addId = $this->event->doAddEvent($map, $opts, $cover)) {
		if($addId=34){
        	//修复活动地点
        	$province = M("area")->where(array("area_id"=>$_POST['province']))->getField("title");
        	$city = M("area")->where(array("area_id"=>$_POST['city']))->getField("title");
        	M("event_opts")->where(array("id"=>$addId))->data(array("province"=>$province,"city"=>$city))->save();
        	
        	//todo 处理活动议程
        	$hdyc_s = $_POST["hdyc_s"];//议程开始时间
        	$hdyc_e = $_POST["hdyc_e"];//议程结束时间
        	$hdyc_d = $_POST["hdyc_d"];//议程说明
        	//$num_yc = count($hdyc_d);
//         	dump($hdyc_d);die;
        	foreach ($hdyc_d as $k => $v){
//         		M("event_times")->data(array("event_id"=>$addId,"sTime"=>$hdyc_s[$k],"eTime"=>$hdyc_e[$k],"description"=>$hdyc_d[$k]))->add();
        	}
        	
        	//todo 处理嘉宾
        	$data['attach_type'] =  'event';
        	$data['upload_type'] =  'image';
//         	 echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
//         	 dump($_FILES);die;
        	$cover = model('attach')->upload($data);
//         	dump($cover);die;
        	//$cover = model('attach')->upload($data);
        	//dump($cover);die;
        	$guest_name = $_POST["guest_name"];//嘉宾名称
        	$guest_position = $_POST["guest_position"];//嘉宾职位
        	$guest_desc = $_POST["guest_desc"];//嘉宾说明
        	$temp_cover = array();
//         	dump($cover["info"]);die;
        	foreach ($cover["info"] as $key => $value){
        		$temp_cover[$value["key"]] = $value["attach_id"];
        	}
//         	dump($guest_name);die;
        	foreach ($guest_name as $k => $v){
        		//dump("guest_img_".$k);
        		//dump(array("event_id"=>$addId,"name"=>$guest_name[$k],"position"=>$guest_position[$k],"description"=>$guest_desc[$k],"avatar"=>$temp_cover[("guest_img_".$k)]));
        		$tmp_data = array("event_id"=>$addId,"name"=>$guest_name[$k],"position"=>$guest_position[$k],"description"=>$guest_desc[$k],"avatar"=>$temp_cover[("guest_img_".$k)]);
//         		dump($tmp_data);//die;
        		if(M("event_guest")->create($tmp_data)){
        			$res = M("event_guest")->data($tmp_data)->add();
//         			dump($res);
//         			dump(M()->getLastSql());die;
        		}else{
        			$this->error(M("event_guest")->getError());
        		}
        	}
//         	die;
        	
        	
        	//dump($_POST['is_verify']);die;
            //活动添加成功 添加公司和通讯录
            if($_POST['is_verify']){
            	$data = array();            	
            	$company_ret = model('Company')->setCompany();
            	//dump($company_ret);die;
            	$data['company_id'] = $company_ret['id'];
            	$data['avatar'] = $_POST['member_avatar'];
            	$data['name'] = $_POST['name'];
            	$data['position'] = $_POST['position'];
            	$data['position_type'] = $_POST['position_type'];
            	$data['phone'] = $_POST['phone'];
            	$data['email'] = $_POST['email'];
            	$data['qq'] = $_POST['qq'];
            	$data['wechat'] = $_POST['wechat'];
            	$data['weibo'] = $_POST['weibo'];
            	$member = M("member m")->where("m.uid=".$this->mid);
            	$member = $member->find();
            	//$member = $member->find();
            	//dump($member);die;
            	if(empty($member)){
            		$data['uid'] = $this->mid;
		            $data['ctime'] = time();
		            //M("member")->data($data)->add();
		            //dump($data);die;
		            if(!M("member")->create($data)){
		            	$this->error("通讯录添加失败");
		            }
		            M("member")->add();
		            //dump(M()->getLastSql());die;
		            $this->success("新增成功！");
            	}else{
            		$this->error("你已经开通通讯录!");
            	}
            }
            /*
            $cover['status'] && $attachid = $cover['info'][0]['attach_id'];
        	model('Feed')->syncToFeed('我发布了一个新活动“'.t($_POST['title']).'”,详情请点击'.U('event/Index/eventDetail',array('id'=>$addId,'uid'=>$this->mid)),$this->mid,$attachid,$from);
            model('Credit')->setUserCredit($this->mid,'add_event');
            */
			// $this->assign('jumpUrl',U('/Index/eventDetail',array('id'=>$addId,'uid'=>$this->mid)));
            $this->success($this->appName.'添加成功');
            //$res['id'] = $addId;
            //$res['uid'] = $this->mid;
            //exit($this->ajaxReturn($res, $this->appName.'发布成功', 1));
        }else{
			$this->error($this->appName.'添加失败');
		}
    }

    /**
     * doAction
     * 参与活动
     * @access public
     * @return void
     */
    public function doAction() {
        $data['id']   = intval( $_POST['id'] );
        $data['uid']  = $this->mid;
        $allow        = intval( $_POST['allow'] );
        $data['action'] = t( $_POST['action'] );
        $this->event->setMid( $this->mid );
        //检测id和uid是否为0
        if( false == $this->checkUrl( $data ) ) {
            echo -4;
        }
        // 判断活动人数是否已满不能参加
        $limitCount = $this->event->where( 'id ='.$data['id'] )->field('limitCount,eTime')->find();
        if($limitCount['limitCount'] <= 0){
            if($allow){
                echo -5;
            }
        }
        if($limitCount['eTime'] < time()){
            echo -6;
        }
        if($allow){
            echo $this->event->doAddUser( $data,$allow );
        }
        if(!$allow){
            echo $this->event->doAddUser( $data,$allow );
        }
        // echo  111;
    }

    /**
     * doAction
     * 取消参加
     * @access public
     * @return void
     */
    public function doDelAction() {
        $data['id']     = intval( $_POST['id'] );
        $data['uid']    = $this->mid;
        $allow          = intval( $_POST['allow'] );
        $data['action'] = t( $_POST['action'] );
        //检测id和uid是否为0
        if( false == $this->checkUrl( $data ) ) {
            echo -4;
            return;
        }
        echo trim($this->event->doDelUser( $data ));

    }

    /**
     * eventDetail
     * 活动详细页
     * @access public
     * @return void
     */
    public function eventDetail() {
        $id   = intval( $_GET['id'] );
        $uid  = intval( $_GET['uid'] );
        $test = array( $id,$uid );
        //检测id和uid是否为0
        if( false == $this->checkUrl( $test ) ) {
            $this->assign('jumpUrl',U('event/Index/index'));
            $this->error( "错误的访问页面，请检查链接" );
        }

        $this->event->setMid( $this->mid );
        if($result = $this->event->getEventContent( $id,$uid )) {
            // 图片大小控制
            $result['cover']     = getCover($result['coverId'],200,200);
        	//计算待审核人数
	        if( $this->mid == $result['uid'] )
	            $result['verifyCount'] = D( 'EventUser' )->where( "status = 0 AND action='joinIn' AND eventId ={$result['id']}" )->count();
            $this->assign($result);
            $this->assign('event', $result);
            $attentionUids = getSubByKey($result['attention'],'uid');
            $memberUids = getSubByKey($result['member'],'uid');
            // $uids = array_unique(array_merge(array($result['uid']),$attentionUids,$memberUids));

            // if($result['uid'] == $this->mid){
            //     $uids = $this->mid;
            // }
            $this->assign('user_info',model('User')->getUserInfoByUids($uids));
            $this->assign('user_info',model('User')->getUserInfoByUids($result['uid']));
            $this->setTitle($result['title'].' - '.$result['time'].' - '.$result['city'].' - '.$result['address'].' - '.$result['type']);
            
            //JTee 2014-01-11 活动时间
            $times = D('Event_times')->where('event_id='.$id)->order('id asc')->select();
            $this->assign('times', $times);
            //JTee 2014-01-10 活动嘉宾
            $guests = D('Event_guest')->where('event_id='.$id)->order('id asc')->select();
            $this->assign('guests', $guests);
            
            //JTee 2014-01-13 左侧数据
            $this->_leftData();
            $creater = M('member m')->field("m.*,c.name company_name")->where(array("m.uid"=>$result["uid"]))->join("left join yx_company c on(c.id=m.company_id)")->find();
            //dump(M()->getLastSql());die;
            //dump($creater);
            $creater_info = model('User')->getUserInfoByUids($result["uid"]);
            //dump($creater_info);
            $this->assign("creater",$creater);
            $this->assign("creater_info",$creater_info[$result["uid"]]);
            
            //JTee 2014-02-09 活动人员
            $joinMember = $this->event->getMember(array('action'=>'joinIn','status'=>1),$result['uid']);
            $this->assign('joinMember', $joinMember );
            
            $verifyMember = $this->event->getMember(array('action'=>'joinIn','status'=>0),$result['uid']);
            $this->assign('verifyMember', $verifyMember );
            
            $this->display();
        }else {
            $this->assign('jumpUrl',U('event/Index/index'));
            $this->error( '错误的访问页面，请检查链接' );
        }
    }

    /**
     * member
     * 活动成员
     * @access public
     * @return void
     */
    public function member() {
        $id = intval( $_GET['id'] );
        //检查url参数
        if( false == $this->checkUrl( array( $id ) ) ) {
            $this->error( "错误的访问页面，请检查链接" );
        }

        //检查id是否存在
        if( false == $event = $this->event->where( 'id='.$id )->field( 'uid,id,title,joinCount,attentionCount,optsId' )->find() )
            $this->error( $this->appName.'已删除或取消' );

        $this->assign( $event );

        //计算待审核人数
        if( $this->mid == $event['uid'] )
            $verifyCount = D( 'EventUser' )->where( "status = 0 AND action='joinIn' AND eventId ={$event['id']}" )->count();
        $this->assign( 'verifyCount',$verifyCount );

        //获得action对应的成员
        switch( $_GET['action'] ) {
            case "att":
                $map['action'] = 'attention';
                $map['status'] = 1;
                break;
            case "join":
                $map['action'] = 'joinIn';
                $map['status'] = 1;
                break;
            case 'verify':
                $map['action'] ='joinIn';
                $map['status'] = 0;
                break;
            default:
                $map['action'] = array( 'in',"'admin','attention','joinIn'" );
                $map['status'] = 1;
        }
        $map['eventId'] = $event['id'];
        //取得成员列表
        $result = $this->event->getMember($map,$event['uid']);
        //dump($result);
        $this->assign( $result );
        $where = array("u.eventId"=>$event["id"],"u.status"=>$map['status'],"u.action"=>$map['action']);
        $event_user = M("event_user u")->field("u.cTime apply_time,u.contact,u.status,m.*,c.name company_name,us.uname,us.uid")->join("left join yx_user us on(us.uid=u.uid)")->join("left join yx_member m on(m.uid=u.uid)")->join("left join yx_company c on(m.company_id=c.id)")->where($where)->select();
        //dump($event_user);//die;
        $this->assign("event_user",$event_user);
        $this->setTitle('成员列表');
        $this->assign( 'id',$id );
        $this->display();
    }
    /*
     * doauth
     * 处理申请成员
     */
    public function doauth(){
    	$id = intval( $_GET['id'] );
    	$uid = $this->event->where( 'id='.$id )->getField( 'uid' );
    	if( $uid != $this->mid ) {
    		$this->error( '您没有权限编辑这个'.$this->appName ) ;
    	}
    	if(empty($_POST["uid"])||empty($_POST["message"])){
    		$this->error("提交信息不完整");
    	}
    	$uids = $_POST["uid"];
    	$auth = $_POST["auth"];
    	foreach ($uids as $k => $v){
    		//判断用户是否参加了该活动
    		$event_user = M("event_user")->where(array("uid"=>$v,"id"=>$id))->find();
    		if(!empty($event_user)){
    			$map['uid'] = $v;
    			$user_info = M('member')->where($map)->find();
    			$data = array();
    			$data['email'] = $user_info['email'];
    			if($auth==0){
    				$data['title'] = "您的活动参与申请通过了";
    				M("event_user")->where(array("id"=>$id,"uid"=>$v))->setField("status",1);
    			}elseif($auth==1){
    				$data['title'] = "您的活动参与申请被拒了";
    				M("event_user")->where(array("id"=>$id,"uid"=>$v))->setField("status",2);
    			}else{
    				$data['title'] = "参与的活动的创建者给你发送消息";
    			}
    			$data['body'] = $_POST['message'];
    			
    			
    			//
    			
    			
    			
    			model('Mail')->send_email($data['email'],$data['title'],$data['body']);
    			
    		}
    	}
    	dump($_POST);die;
    }

    /**
     * edit
     * 编辑活动
     * @access public
     * @return void
     */
    public function edit(  ) {
        $id = intval( $_GET['id'] );
        $uid = $this->event->where( 'id='.$id )->getField( 'uid' );
        if( $uid != $this->mid ) {
            $this->error( '您没有权限编辑这个'.$this->appName ) ;
        }

        $typeDao = D( 'EventType' );
        $this->event->setMid( $this->mid );
        if($result = $this->event->getEventContent( $id,$uid )) {
        	//dump($result);
        	$arr = explode(" ", $result["city"]);
        	//dump($arr);
        	$province = $arr["0"];
        	$city = $arr["1"];
        	$province_arr = M("area")->where("pid=0")->order("sort asc")->select();
        	$province_id = M("area")->where("title like '%".$province."%'")->getField("area_id");
        	//dump(M()->getLastSql());die;
        	$city_id = M("area")->where("title like '%".$city."%'")->getField("area_id");
        	//dump(M()->getLastSql());die;
        	//dump($city_id);
        	$this->assign("province_id",$province_id);
        	$this->assign("city_id",$city_id);
        	//dump($province_id);
        	$city_arr = M("area")->where("pid=".$province_id)->select();
        	//dump($city_arr);die;
        	$this->assign("city_arr",$city_arr);
        	$this->assign("province_arr",$province_arr);
        	//字段属性
        	$types = D('Event_type')->select();
        	$types_list = array();
        	foreach($types as $t){
        		$types_list[$t['id']] = $t['name'];
        	}
        	//dump($types_list);die;
        	$this->assign("types_list",$types_list);
        	$this->assign("organizer_type_list",tsconfig('organizer_type'));
        	$this->assign("cost_list",tsconfig('event_cost'));
        	$this->assign("is_verify",tsconfig('yesorno'));
        	$this->assign("is_recommend",tsconfig('yesorno'));
        	$this->assign("opts",tsconfig('yesorno'));
        	 
            $this->assign( $result );
            $this->display('edit');
        }else {
            $this->error( '错误的访问页面，请检查链接' );
        }

    }

    /**
     * doEditEvent
     * 修改活动
     * @access public
     * @return void
     */
    public function doEditEvent() {
        $id['id'] = intval( $_POST['id'] );
        //判断作者
        if ( !CheckAuthorPermission( D('Event') , $id['id'] ) ){
        	$this->error( '对不起，您没有权限进行该操作' );
        }
        $id['optsId'] = intval( $_POST['optsId'] );
        $map['title']      = t($_POST['title']);
        $map['address']    = t($_POST['address']);
        $map['limitCount'] = intval(t( $_POST['limitCount'] ));
        $map['type']       = intval($_POST['type']);
        $map['explain']    = h($_POST['explain']);
        $map['contact']    = t($_POST['contact']);
        $map['deadline'] = $deadline = $this->_paramDate( $_POST['deadline'] );
        $map['sTime']    = $stime = $this->_paramDate($_POST['sTime']);
        $map['eTime']    = $etime = $this->_paramDate($_POST['eTime']);

        if(!t($_POST['title'])){
            $this->error("活动标题不能为空");
        }
        if(!t($_POST['address'])){
            $this->error("活动地址不能为空");
        }
        if(intval($_POST['type']) == 0){
            $this->error("请选择活动分类");
        }
        if( $stime > $etime) {
            $this->error( "结束时间不得早于开始时间" );
        }
        if( $deadline > $etime) {
            $this->error( "报名截止时间不能晚于结束时间" );
        }

        $string=iconv("UTF-8","GBK", t($map['explain']));
        $length = strlen($string);
        if($length < 20){
            $this->error('活动介绍不得小于20个字符');
        }

        //处理省份，市，区
        list( $opts['province'],$opts['city'],$opts['area'] ) = explode( " ",safe($_POST['city']));

        //得到上传的图片
        $config     =   event_getConfig();
 		$options['userId']		=	$this->mid;
		$options['max_size']    =   $config['photo_max_size'];
		$options['allow_exts']	=	$config['photo_file_ext'];
       

        //处理选项
        $opts['cost']        = intval( $_POST['cost'] );
        $opts['costExplain'] = t( $_POST['costExplain'] );
        $friend              = isset( $_POST['friend'] )?1:0;
        $allow                = isset( $_POST['allow'] )?1:0;
        $opts['opts']        = array( 'friend'=>$friend,'allow'=>$allow );

        if( $this->event->doEditEvent($map, $opts, $cover, $id )) {
        	 //$this->assign('jumpUrl',U('eventDetail',array('id'=>$id['id'],'uid'=>$this->mid)));
             $this->success('修改成功！');
            /*
             $res['id'] = intval( $_POST['id'] );
            $res['uid'] = $this->mid;
            return $this->ajaxReturn($res, $this->appName.'发布成功', 1);
            */
        }
    }
    public function myactive(){
        $self_id = $this->mid;
        $act = t($_GET['ac'])?t($_GET['ac']):"join";
        if($act == "join"){
            $map = array();
            //$map['u.status'] = 1;
            $map['u.uid'] = $self_id;
            $map['u.action'] = "joinIn";
            //$list = D("event e")->join("event_user u on(u)")
            $list = D("event_user u")->field("e.id,e.cTime,e.title,e.joinCount,u.status")->join("yx_event e on(u.eventId = e.id and u.uid=$self_id)")->where($map)->select();
            //dump(M()->getLastSql());die;
            //dump($list);
            $this->assign("list",$list);
            $this->display("join");
        }else{
            $map = array();
            $map['uid'] = $self_id;
            $list = $this->event->getEventList($map);
            //dump($list);
            $this->assign("list",$list);
            $this->display("create");
        }
    }



    /**
     * doEndAction
     * 结束活动
     * @access public
     * @return void
     */
    public function doEndAction() {
        $id = $_POST['id'];
        $this->event->setMid( $this->mid );

        //检查安全性，防止非管理员访问
        $uid = $this->event->where( 'id='.$id )->getField( 'uid' );
        if( $uid != $this->mid ){
            echo -1;
        }
        if($this->event->where( 'id='.$id )->setField( 'eTime',time() )){
            echo 1;
        }else{
            echo 0;
        }
        // echo $this->event->doEditData( time(),$id );
    }    
    /**
     * doEndAction
     * 结束活动
     * @access public
     * @return void
     */
    public function doCloseAction() {
        $id = $_POST['id'];
        $this->event->setMid( $this->mid );

        //检查安全性，防止非管理员访问
        $uid = $this->event->where( 'id='.$id )->getField( 'uid' );
        if( $uid != $this->mid ){
            echo -1;
        }
        if($this->event->where( 'id='.$id )->setField( 'deadline',time() )){
            echo 1;
        }else{
            echo 0;
        }
        // echo $this->event->doEditData( time(),$id );
    }

    /**
     * doAgreeAction
     * 同意申请
     * @access public
     * @return void
     */
    public function doAgreeAction() {
        $data['id']      = intval( $_POST['id'] );
        $data['eventId'] = intval( $_POST['eventId'] );
        $data['uid']     = intval( $_POST['uid'] );
        $join_uid = intval($_POST['join_uid']);
        $result = $this->event->getEventContent( $data['eventId'],$data['uid'] );
        $map['eventId']  = intval($_POST['eventId']);
        $map['status']   = 0;
        $user   = M('event_user')->where($map)->findAll();
        $userCount = count($user);

        //检查操作权限
        if( $this->mid != D('Event')->getField('uid',"id={$data['eventId']}") ){
            echo  -4;
            return;
        }
        if($result['lc'] <=0 && $result['lc'] != '无限制'){
            echo -5;
            return ;
        }
        //检测id和uid是否为0
        if( false == $this->checkUrl( $data ) ) {
            echo -4;
            return;
        }
        $res = $this->event->doArgeeUser( $data);
        if($res){
            model('Credit')->setUserCredit($join_uid,'join_event');
        }
        echo trim($res);
    }

    /**
     * doAdminAction
     * 删除成员
     * @access public
     * @return void
     */
    public function doAdminAction() {
        $admin          = t( $_POST['admin'] );
        $data['uid']    = intval( $_POST['uid'] );      //被操作的用户
        $data['id']     = intval( $_POST['eventId'] );  //被操作的活动
        $data['action'] = t( $_POST['action'] );    //被操作的用户的动作

        //检查操作权限
        if( $this->mid != D('Event')->getField('uid',"id={$data['id']}") ){
        	echo  -4;
        	return;
        }

        //检查链接合法性
        if( !$this->checkUrl( $data ) ) {
            echo -4;
            return;
        }
        switch ( $admin ) {
            case 'user':   //成员管理
                echo $this->event->doDelUser( $data );
                return;
                break;
            default:
        //TODO 更多的操作
        }

    }

        /**
         * doDeleteEvent
         * 删除活动
         * @access public
         * @return void
         */
        public function doDeleteEvent(){
            $eventid['id']  = intval($_REQUEST['id']);    //要删除的id.
            $eventid['uid'] = $this->mid;
            if ( !CheckAuthorPermission( D('Event') , $eventid['id'] ) ){
            	echo 0;exit;
            }
            
            $result         = $this->event->doDeleteEvent($eventid);
            if( false != $result){
                echo 1;
            }else{
                echo 0;               //删除失败
            }
        }

    /**
     * 分享活动
     */
    public function ShareEvent(){
        $eventId = intval($_POST['eventId']);
        // 判断活动是否结束
        $limitCount = $this->event->where( 'id ='.$eventId )->field('limitCount,eTime')->find();
        if($limitCount['eTime'] < time()){
            echo -6;
        }else{
            $eventDetail = $this->event->find($eventId);
            if(model('Feed')->shareToFeed('我分享了一个活动“'.$eventDetail['title'].'”,详情请点击'.U('event/Index/eventDetail',array('id'=>$eventId,'uid'=>$eventDetail['uid'])),$this->mid,$eventDetail['coverId'],0)){
                echo 1;
            }else{
                echo 0;
            }  
        }
        
    }

    /**
     * _paramDate
     * 解析日期
     * @param mixed $date
     * @access private
     * @return void
     */
    private function _paramDate( $date ) {
        $date_list = explode( ' ',safe($date) );
        list( $year,$month,$day ) = explode( '-',$date_list[0] );
        list( $hour,$minute,$second ) = explode( ':',$date_list[1] );
        return mktime( $hour,$minute,$second,$month,$day,$year );
    }

    /**
     * checkUrl
     * 检查url参数是否合法
     * @param array $data
     * @access public
     * @return void
     */
    public function checkUrl(array $data ) {
        $count1 = count( $data );
        $count2 = count( array_filter( $data ));
        if( $count2 < $count1 ) {
            return false;
        }else {
            return true;
        }
    }

    
    /**
     * 活动页面左侧数据读取
     * JTee 2014-01-13
     */
    private function _leftData(){
    	$this->assign('week_number', C('week_number'));  //星期几
    	//计算本周开始时间
    	$start = date('Y-m-').(date('d')-date('N')+1).' 00:00:00';
    	$end = date('Y-m-').(date('d')-date('N')+7).' 23:59:59';
    	//echo '本周第一天'.$start.'本周最后一天'.$end;die;
    	//本周开始活动
    	$thisweek  = $this->event->where("sTime >= '.strtotime($start).' and sTime <= '.strtotime($end).' and is_verify=3")->select();
    	$this->assign('thisweek', $thisweek);
    	//下周开始活动
    	$nextweek  = $this->event->where("sTime > '.strtotime($end).' and is_verify=3")->select();
    	$this->assign('nextweek', $nextweek);
    }
    
    public function upload_img(){
    	//echo 123;die;
    	//dump($_FILES);die;
    	$data['attach_type'] =  'event';
    	$data['upload_type'] =  'image';
    	
    	$cover = model('attach')->upload($data);
		//dump($cover);die;
		if($cover["status"]){
			$data=array();
			$data['status'] =1;
			//$data['data'] = array();
			$data['data']['attach_id'] = $cover["info"][0]["attach_id"];
			$data['data']['width'] = $cover["info"][0]["width"];
			$data['data']['height'] = $cover["info"][0]["height"];
			$data['data']['url'] = "data/upload/".$cover["info"][0]["save_path"].$cover["info"][0]["save_name"];
		}else{
			$data['status'] =0;
		}
		echo(json_encode($data));die;
    }
    public function xls_out(){
    	$id = intval( $_GET['id'] );
    	if(empty($id)){
    		$this->error("参数错误,参数为空");
    	}
    	$self_id = $this->mid;
    	$event = M("event")->where(array("uid"=>$self_id,"id"=>$id))->find();
    	if(empty($event)){
    		$this->error("该活动不存在或你不为创建者");
    	}
    	//echo 22;
    	require_once APPS_PATH.'/event/Lib/ORG/PHPExcel/PHPExcel.php';
    	require_once APPS_PATH.'/event/Lib/ORG/PHPExcel/PHPExcel/Writer/Excel5.php';
    	//echo 123;
    	// 创建一个处理对象实例
    	$objExcel = new PHPExcel();
    	//dump($objExcel);die;
    	// 创建文件格式写入对象实例, uncomment
    	$objWriter = new PHPExcel_Writer_Excel5($objExcel);     // 用于其他版本格式
    	$objExcel->setActiveSheetIndex(0);
    	$objActSheet = $objExcel->getActiveSheet();
    	
    	//设置当前活动sheet的名称
    	$title = '活动导出';
    	$objActSheet->setTitle($title);
    	
    	$event_user = M("event_user u")->field("u.cTime apply_time,u.contact,u.status,m.*,c.name company_name,us.uname")->join("left join yx_user us on(us.uid=u.uid)")->join("left join yx_member m on(m.uid=u.uid)")->join("left join yx_company c on(m.company_id=c.id)")->where(array("u.eventId"=>$event["id"],"u.status"=>1))->select();
    	//dump($event_user);die;
    	$objActSheet->setCellValue('A1', $event["title"]);
    	$objActSheet->setCellValue("A3","申请者");
    	$objActSheet->setCellValue("B3","联系方式");
    	$objActSheet->setCellValue("C3","申请时间");
    	$objActSheet->setCellValue("D3","状态");
    	$objActSheet->setCellValue("E3","真实姓名");
    	$objActSheet->setCellValue("F3","手机");
    	$objActSheet->setCellValue("G3","公司");
    	$objActSheet->setCellValue("H3","职位");
    	$objActSheet->setCellValue("I3","QQ");
    	$objActSheet->setCellValue("J3","邮箱");

    	
    	foreach ($event_user as $k =>$v){
    		$objActSheet->setCellValue("A".($k+4),$v["uname"]);
    		$objActSheet->setCellValue("B".($k+4),$v["contact"]);
    		$objActSheet->setCellValue("C".($k+4),date("Y-m-d H:i",$v["apply_time"]));
    		if($v["status"] == 0){
    			$objActSheet->setCellValue("D".($k+4),"未审核");
    		}elseif($v["status"] == 1){
    			$objActSheet->setCellValue("D".($k+4),"通过审核");
    		}else{
    			$objActSheet->setCellValue("D".($k+4),"拒绝");
    		}
    		$objActSheet->setCellValue("E".($k+4),$v["name"]);
    		$objActSheet->setCellValue("F".($k+4),$v["phone"]);
    		$objActSheet->setCellValue("G".($k+4),$v["company_name"]);
    		$objActSheet->setCellValue("H".($k+4),$v["position"]);
    		$objActSheet->setCellValue("I".($k+4),$v["qq"]);
    		$objActSheet->setCellValue("J".($k+4),$v["email"]);
    	}
    	
    	/*
    	$objActSheet->setCellValue('A1', "活动名称");
    	$objActSheet->setCellValue('B1', $event['title']);
    	$objActSheet->setCellValue('A2', "活动时间");
    	$objActSheet->setCellValue('B2', date("Y-m-d H:i:s",$event['sTime'])."-".date("Y-m-d H:i:s",$event['eTime']));
    	$objActSheet->setCellValue('A3', "所在城市");
    	$event_opt = M("event_opts")->where(array("id"=>$id))->find();
    	$objActSheet->setCellValue('B3', $event_opt['province'].$event_opt['city']);
    	$objActSheet->setCellValue('A4', "活动地点");
    	$objActSheet->setCellValue('B4', $event['address']);
    	$objActSheet->setCellValue('A5', "需要人数");
    	$objActSheet->setCellValue('B5', $event['limitCount']);
        $objActSheet->setCellValue('A6', "活动类别");
        $typeDao = D( 'EventType' );
        $event_type = $typeDao->where(array("id"=>$event['type']))->getField("name");
        $objActSheet->setCellValue('B6', $event_type);
        $objActSheet->setCellValue('A7', "截止时间");
        $objActSheet->setCellValue('B7', date("Y-m-d H:i:s",$event['deadline']));
        $objActSheet->setCellValue('A8',"报名方式");
        $objActSheet->setCellValue('B8',$event['contact']);
        $objActSheet->setCellValue('A9','活动费用');
        $objActSheet->setCellValue('B9',$event['cost']);
        $objActSheet->setCellValue('A9','活动介绍');
        $objActSheet->setCellValue('B9',$event['explain']);
        $objActSheet->setCellValue('A10','主办方');
        $objActSheet->setCellValue('B10',$event['sponsor']);
        $organizer_type = tsconfig('organizer_type');
        $objActSheet->setCellValue('A11',$organizer_type[$event['organizer_type']]);
        $objActSheet->setCellValue('B11',$event['organizer']);
        $objActSheet->setCellValue('A12','交通路线');
        $objActSheet->setCellValue('B12',$event['traffic']);
        $objActSheet->setCellValue('A13','注意事项');
        $objActSheet->setCellValue('B13',$event['notice']);
        $objActSheet->setCellValue('A14','嘉宾');
        $objActSheet->setCellValue('A15','姓名');
        $objActSheet->setCellValue('B15','职位');
        $objActSheet->setCellValue('C15','说明');
        $guest_list = M("event_guest")->where(array("event_id"=>$event['id']))->select();
        if($guest_list){
        	$tmp = 1;
        	foreach ($guest_list as $k =>$v){
        		$objActSheet->setCellValue('A'.(16+$k),$v['name']);
        		$objActSheet->setCellValue('B'.(16+$k),$v['position']);
        		$objActSheet->setCellValue('C'.(16+$k),$v['description']);
        		$tmp = (16+$k);
        	}
        }
        $objActSheet->setCellValue('A'.(1+$tmp),'活动议程');
        $objActSheet->setCellValue('A'.(2+$tmp),'时间段');
        $objActSheet->setCellValue('B'.(2+$tmp),'活动细节');
        $times_list = M("event_times")->where(array("event_id"=>$event['id']))->select();
        if($times_list){
        	//$tmp = 1;
        	foreach ($times_list as $k =>$v){
//         		dump(3+$tmp+$k); 
        		$objActSheet->setCellValue('A'.(3+$tmp+$k),date('Y-m-d H:s',$v['sTime']).'到'.date('Y-m-d H:s',$v['eTime']));
        		$objActSheet->setCellValue('B'.(3+$tmp+$k),$v['description']);
        		$tmp = (16+$k);
        	}
        }
        //die;
        */
    	$outputFileName = $title.".xls";
    	$attachmentHeader = encodeExcelName($outputFileName,'UTF-8');
    	
    	//到文件
    	////$objWriter->save($outputFileName);
    	//or
    	//到浏览器
    	header("Content-Type: application/force-download");
    	header("Content-Type: application/octet-stream");
    	header("Content-Type: application/download");
    	header($attachmentHeader);
    	header("Content-Transfer-Encoding: binary");
    	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    	header("Pragma: no-cache");
    	$objWriter->save('php://output');
    	exit;
    	
    }
    
    
}
