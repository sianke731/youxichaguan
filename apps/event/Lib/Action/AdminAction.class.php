<?php
    /**
     * AdminAction 
     * 活动管理
     * @uses Action
     * @package Admin
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
	import('admin.Action.AdministratorAction');
	class AdminAction extends AdministratorAction {
        /**
         * event 
         * EventModel的实例化对象
         * @var mixed
         * @access private
         */
        private $event;

        /**
         * config 
         * EventConfig的实例化对象
         * @var mixed
         * @access private
         */

        public function _initialize(){
	        //管理权限判定
	        parent::_initialize();
            $this->event = D( 'Event' );
        }

        /**
         * basic 
         * 基础设置管理
         * @access public
         * @return void
         */
        public function index (){
        	$config   = model('Xdata')->lget('event');
            $this->assign($config);

            $credit_types = model('Credit')->getCreditType();
            //dump($credit_types);
            $this->assign('credit_types',$credit_types); 

            $this->display();

        } 

        /**
         * doChangeBase 
         * 修改全局设置
         * @access public
         * @return void
         */
        public function doChangeBase (){
	        //变量过滤 todo:更细致的过滤
	        foreach($_POST as $k=>$v){
	            $config[$k] =   t($v);
	        }
	        //$config['limitsuffix'] = preg_replace("/bmp\|||\|bmp/",'',$config['photo_file_ext']);//过滤bmp
	        if(model('Xdata')->lput('event',$config)){
	            $this->assign('jumpUrl', U('event/Admin/index'));
	            $this->success('设置成功！');
	        }else{
	            $this->error('设置失败！');
	        }
        }

        /**
         * eventlist 
         * 获得所有人的eventlist
         * @access public
         * @return void
         */
        public function eventlist (){
        	//get搜索参数转post
	        if(!empty($_GET['type'])){
	           $_POST['type'] = $_GET['type'];
	        }
            //为使搜索条件在分页时也有效，将搜索条件记录到SESSION中
	        if ( !empty($_POST) ) {
	            $_SESSION['admin_search'] = serialize($_POST);
	        }else if ( isset($_GET[C('VAR_PAGE')]) ) {
	            $_POST = unserialize($_SESSION['admin_search']);
	        }else {
	            unset($_SESSION['admin_search']);
	        }   
	        $this->assign('isSearch', isset($_POST['isSearch'])?'1':'0');   
	
	        $_POST['uid']   && $map['uid']    =   intval($_POST['uid']);
	        $_POST['id']    && $map['id']     =   intval($_POST['id']);
            $_POST['type']  && $map['type']   =   intval($_POST['type']);
            $_POST['title'] && $map['title'] =   array( 'like','%'.t( $_POST['title'] ).'%' );
            //处理时间
//            $_POST['sTime'] && $_POST['eTime'] && $map['cTime'] = $this->event->DateToTimeStemp(t( $_POST['sTime'] ),t( $_POST['eTime'] ) );
            $_POST['sTime'] && $_POST['eTime'] && $map['cTime'] = $this->event->DateToTimeStemp(t( date("Ymd",strtotime($_POST['sTime'])) ),t(date("Ymd",strtotime($_POST['eTime']))) );
	        //处理排序过程
            $order = isset( $_POST['sorder'] )?t( $_POST['sorder'] )." ".t( $_POST['eorder'] ):"cTime DESC";
	        $_POST['limit']     && $limit         =   intval( t( $_POST['limit'] ) );
            
	        $order && $list  = $this->event->getList($map,$order,$limit);
            $type_list = D('EventType')->getType();
            $this->assign( $_POST );
            $this->assign( $list );
            $this->assign( 'type_list',$type_list );

            $this->assign( 'verify_status',tsconfig('verify_status') );  //JTee 2013-12-21 获取审核状态
            $this->display();
        }

        /**
         * transferEventTab 
         * 转移活动
         * @access public
         * @return void
         */
        public function transferEventTab(){
        	$type_list = D('EventType')->getType();
            $this->assign( 'type_list',$type_list );
            $this->assign( 'id',$_GET['id'] );
            $this->display();
        }

        /**
         * doDeleteEvent 
         * 执行转移活动
         * @access public
         * @return void
         */
        public function doTransferEvent(){
            $id['id']     = array('in',t($_POST['id']));
            $data['type'] = intval($_POST['type']);
            if(!$_POST['id'] || !$data['type']){
                echo -2;
                exit;
            }
            if($this->event->where($id)->save($data)){
                if ( !strpos($_REQUEST['id'],",") ){
                    echo 2;            //说明只操作一个
                }else{
                    echo 1;            //操作多个
                }
            }else{
                echo -1;               //操作失败            	
            }
        }

        /**
         * doDeleteEvent 
         * 删除活动
         * @access public
         * @return int
         */
        public function doDeleteEvent(){
            $eventid['id'] = array( 'in',explode(',',$_REQUEST['id']));    //要删除的id.
            $result       = $this->event->doDeleteEvent($eventid);
            if( false != $result){
                if ( !strpos($_REQUEST['id'],",") ){
                    echo 2;            //说明只是删除一个
                }else{
                    echo 1;            //删除多个
                }
            }else{
                echo -1;               //删除失败
            }
        }

        //推荐操作
        public function doChangeIsHot(){
            $event['id'] = array( 'in',$_REQUEST['id']);        //要推荐的id.
            $act  = $_REQUEST['type'];  //推荐动作
            $result  = $this->event->doIsHot($event,$act);

            if( false != $result){
                    echo 1;            //推荐成功
            }else{
                echo -1;               //推荐失败
            }
        }

        /**
         * eventtype 
         * 活动类型列表
         * @access public
         * @return void
         */
        public function eventtype(){
            $type  = D( 'EventType' );
            $type  = $type->order('id ASC')->findAll();
            $this->assign( 'type_list',$type );

            $count = D('Event')->field('type,count(type) as count')->group('type')->findAll();
            foreach($count as $k => $v){
            	// unset($count[$k]);
            	$count[$v['type']] = $v['count'];
            }
            $this->assign('count',$count);

            $this->display();
        }

        /**
         * editEventTab 
         * 添加分类
         * @access public
         * @return void
         */
        public function editEventTab(){
        	$id = intval($_GET['id']);
        	if($id){
        		$name = D( 'EventType' )->getField('name',"id={$id}");
                $this->assign('id',$id);
                $this->assign('name',$name);
        	}
        	$this->display();
        }
        /**
         * doAddType 
         * 添加分类
         * @access public
         * @return void
         */
        public function doAddType(){
        	$isnull = preg_replace("/[ ]+/si","", t($_POST['name']));
            $type = D( 'EventType' );
            $name = M('EventType')->where(array('name'=>$isnull))->getField('name');
            if (empty($isnull)){
            	echo -2;
            }
            if($name !== null){
            	echo 0;
            }else{
	            if( $result = $type->addType( $_POST ) ){
	                echo 1;
	            }else{
	                echo -1;
	            }
            }
        }
		
        /**
         * doEditType 
         * 修改分类
         * @access public
         * @return void
         */
        public function doEditType(){
            $_POST['id']   = intval($_POST['id']);
            $_POST['name'] = t($_POST['name']);
           	$_POST['name'] = preg_replace("/[ ]+/si","", $_POST['name'] );
            if(empty($_POST['name'])){
            	echo -2;
            }
            $type = D( 'EventType' );
            $name = M('EventType')->where(array('name'=>t($_POST['name'])))->getField('name');
            if ($name !== null){
            	echo 0; //分类名称重复
            }else{
	            if( $result = $type->editType( $_POST ) ){
	                echo 1; //更新成功
	            }else{
	                echo -1;
	            }
            }

        }

        /**
         * doEditType 
         * 删除分类
         * @access public
         * @return void
         */
        public function doDeleteType(){
            $id['id']      = array( "in",$_POST['id']);
            $type = D( 'EventType' );
            if( $result = $type->deleteType( $id ) ){
                if ( !strpos($_POST['id'],",") ){
                    echo 2;            //说明只是删除一个
                }else{
                    echo 1;            //删除多个
                }
            }else{
                echo $result;
            }
        }
        
        
        /**
         * 更新 / 创建信息
         */
        public function setinfo ()
        {
        	$id  = $_REQUEST['id'];
        	$event = D( 'Event','event' );
        	if ($id)
        	{
        		$info = $event->find($id);
		        $info['deadline']   = date('Y-m-d H:i:s',$info['deadline']);
		        $info['sTime']      = date('Y-m-d H:i:s',$info['sTime']);
		        $info['eTime']      = date('Y-m-d H:i:s',$info['eTime']);
		        $info = $event->appendContent( $info );
		        $info['opts'] = $info['opts']['allow'];
		        $info['area'] = $info['province'].' '.$info['city'].' '.$info['area'];
        	}
        	if ($_POST)
        	{
	        	$map['title']      = t($_POST['title']);
		        $map['address']    = t($_POST['address']);
		        $map['limitCount'] = intval(t($_POST['limitCount']));
		        $map['type']       = intval($_POST['type']);
		        $map['explain']    = h($_POST['explain']);
		        $map['contact']    = t($_POST['contact']);
		        $map['deadline']   = $this->paramDate($_POST['deadline']);
		        $map['sTime']      = $this->paramDate($_POST['sTime']);
		        $map['eTime']      = $this->paramDate($_POST['eTime']);
		        $map['uid']        = $this->mid;
		        $map['is_verify']  = $_POST['is_verify'];
		        $map['is_recommend']  = $_POST['is_recommend'];
		        $map['is_top']  = $_POST['is_top'];
		        $map['traffic']  = $_POST['traffic'];
		        $map['notice']  = $_POST['notice'];
		        $map['sponsor']  = $_POST['sponsor'];
		        $map['organizer_type']  = $_POST['organizer_type'];
		        $map['organizer']  = $_POST['organizer'];
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
		        if( $map['deadline'] < time() ) {
		            $this->error( "报名截止时间不得早于当前时间" );
		        }
		        if( $map['deadline'] > $map['eTime'] ) {
		        	$this->error('报名截止时间不能晚于结束时间');
		        }
		
				$string=iconv("UTF-8","GBK", t($map['explain']));
		        $length = strlen($string);
		        if($length < 20){
		        	$this->error('介绍不得小于20个字符');
		        }
		        	
		        //处理省份，市，区
		        list( $opts['province'],$opts['city'],$opts['area'] ) = explode(" ",$_POST['city']);
		
		        //得到上传的图片
		        $data['attach_type'] =  'event';
		        $data['upload_type'] =  'image';
		        $cover = model('attach')->upload($data);
		        //print_r($_FILES);die;
		
		        //处理选项
		        $opts['cost']        = intval( $_POST['cost'] );
		        $opts['costExplain'] = t( $_POST['costExplain'] );
		        $opts['costExplain'] = keyWordFilter(t($_POST['costExplain']));
		        $opts['opts']        = array('allow'=>$_POST['opts']);
		        //print_r($map);print_r($opts);die;
		        if($id){ //判断是添加还是修改
		        	$addId = $event->doEditEvent($map, $opts, $cover,$id);
		        }else{
		        	$addId = $event->doAddEvent($map, $opts, $cover);
		        }
		        if( $addId) {
		            $cover['status'] && $attachid = $cover['info'][0]['attach_id'];
		        	$this->assign('jumpUrl',U('event/Admin/eventlist'));
		            $this->success($this->appName.'添加成功');
		        }else{
					$this->error('添加失败');
				}
        	}
        	 
        	// 列表key值 DOACTION表示操作
        	$this->pageKeyList = array('title','sTime','eTime','area','address','limitCount',
        			'type','explain','sponsor','organizer_type','organizer',
        			'contact','deadline','cost','costExplain','opts',
        			'traffic','notice','coverId','is_verify',
        			'is_recommend','is_top');
        	 
        	//字段属性
        	$types = D('Event_type')->select();
        	$types_list = array();
        	foreach($types as $t){
        		$types_list[$t['id']] = $t['name'];
        	}
        	$this->opt['type'] = $types_list;
        	$this->opt['organizer_type'] = tsconfig('organizer_type');
        	$this->opt['cost'] = tsconfig('event_cost');
        	$this->opt['is_verify'] = tsconfig('yesorno');
        	$this->opt['is_recommend'] = tsconfig('yesorno');
        	$this->opt['is_top'] = tsconfig('yesorno');
        	$this->opt['opts'] = tsconfig('yesorno');
        	;
        	// 表单URL设置
        	$this->savePostUrl = U('event/Admin/setinfo',array('id' => $id));
        	$this->notEmpty = array
        	(
        			'title','sTime','eTime','area','address','limitCount',
        			'type','explain','sponsor','organizer_type','organizer',
        	);
        
        	$this->displayConfig($info);
        }
        
        
        /**
         * 添加嘉宾
         */
        public function guest ()
        {
        	$id  = intval($_REQUEST['id']);
        	if ($id)
        	{
        		$guests = D('Event')->findGuest($id);
        	}else{
        		$this->error('缺少活动ID');
        	}
        	if ($_POST)
        	{
        		$save = D('Event')->setGuest($id);
        		if ($save['ret'] == true)
        		{
        			$this->assign('jumpUrl',U('event/Admin/guest',array('id'=>$id)));
        			return $this->success( '成功保存信息');
        		}else
        		{
        			return $this->error( $save['msg'] );
        		}
        	}
        
        	$this->assign('guests',$guests);
        	$this->assign('id',$id);
        
        	$this->display('guest');
        }
        
        /**
         * 删除嘉宾
         */
        public function delGuest ()
        {
        	$id  = intval($_REQUEST['id']);
        	$event_id  = intval($_REQUEST['event_id']);
        	$ret = D('Event')->delGuest($id);
        	if ($ret)
        	{
        		$this->assign('jumpUrl',U('event/Admin/guest',array('id'=>$event_id)));
        		return $this->success( '删除成功');
        	}else
        	{
        		return $this->error( $save['msg'] );
        	}
        }
        



        /**
         * 添加议程
         */
        public function times ()
        {
        	$id  = intval($_REQUEST['id']);
        	if ($id)
        	{
        		$times = D('Event')->findTimes($id);
        	}else{
        		$this->error('缺少活动ID');
        	}
        	if ($_POST)
        	{
        		$save = D('Event')->setTimes($id);
        		if ($save['ret'] == true)
        		{
        			$this->assign('jumpUrl',U('event/Admin/times',array('id'=>$id)));
        			return $this->success( '成功保存信息');
        		}else
        		{
        			return $this->error( $save['msg'] );
        		}
        	}
        
        	$this->assign('times',$times);
        	$this->assign('id',$id);
        
        	$this->display('times');
        }
        
        /**
         * 删除议程
         */
        public function delTimes ()
        {
        	$id  = intval($_REQUEST['id']);
        	$event_id  = intval($_REQUEST['event_id']);
        	$ret = D('Event')->delTimes($id);
        	if ($ret)
        	{
        		$this->assign('jumpUrl',U('event/Admin/times',array('id'=>$event_id)));
        		return $this->success( '删除成功');
        	}else
        	{
        		return $this->error( $save['msg'] );
        	}
        }
        
        


        /**
         * 待审核页面列表
         */
        public function waitverify ()
        {
        	$map = array('is_verify'=>1);
            $order = isset( $_POST['sorder'] )?t( $_POST['sorder'] )." ".t( $_POST['eorder'] ):"cTime DESC";
	        $_POST['limit']     && $limit         =   intval( t( $_POST['limit'] ) );
            
            $order && $list  = $this->event->getList($map,$order,$limit);
            $type_list = D('EventType')->getType();
            $this->assign( $_POST );
            $this->assign( $list );
            $this->assign( 'type_list',$type_list );
            $this->assign( 'verify_status',tsconfig('verify_status') );
            $this->display();
        }
    
    /**
     * 审核活动
     */
    public function doverify ()
    {
    	$id  = intval($_REQUEST['id']);
    	$ret = D('Event')->verify($id);
    	if ($ret)
    	{
    		$this->assign('jumpUrl',U('event/Admin/waitverify'));
    		return $this->success( '审核成功');
    	}else
    	{
    		return $this->error( $save['msg'] );
    	}
    }
    }
