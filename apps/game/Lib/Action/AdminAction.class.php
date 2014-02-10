<?php
/**
 * 游戏后台管理
 * @author  JTee<sianke731@126.com>
 * @version TS3.0
 */
// 加载后台控制器
tsload(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');
class AdminAction extends AdministratorAction
{
    function _initialize ()
    {
    	$this->pageTitle['index'] = '游戏管理';
        $this->pageTitle['setinfo'] = '创建/修改信息';
        $this->pageTitle['waitverify'] = '待审核游戏';
        
        // tab选项
        $this->pageTab[] = array('title' => '游戏管理' , 'tabHash' => 'index' , 'url' => U('game/Admin/index'));
        $this->pageTab[] = array('title' => '创建/修改信息' , 'tabHash' => 'setinfo' , 'url' => U('game/Admin/setinfo'));
        $this->pageTab[] = array('title' => '待审核游戏' , 'tabHash' => 'waitverify' , 'url' => U('game/Admin/waitverify'));
        parent::_initialize();
    }
    
    /**
     * 页面列表
     */
    public function index ()
    {
        $_REQUEST['tabHash'] = 'index';
        //按钮
        //$this->pageButton[] = array('uid','title'=>'搜索', 'onclick'=>"admin.fold('search_form')");
        $this->pageButton[] = array('uid','title'=>'删除', 'onclick'=>"admin.deleteInfo();");
        $this->pageButton[] = array('uid','title'=>'添加游戏', 'onclick'=>"location.href='".U('game/admin/setinfo',array('tabHash'=>'setinfo'))."';");
       
        //构造搜索条件
        //列表key值 DOACTION表示操作
		$this->pageKeyList = array('id','name','company','state','is_top','views','date','DOACTION');
        //$listData = M('Game g')->join(''.C('DB_PREFIX').'company c ON g.company_id=c.id')->field('g.*,c.name as company_name')->where('g.is_verify=1')->order('g.id desc')->findPage(15);
        $listData = M('Game g')->join(''.C('DB_PREFIX').'company c ON g.company_id=c.id')->field('g.*,c.name as company_name')->order('g.id desc')->findPage(15);
        
        $verifyStatus = tsconfig('verify_status');
        $authStatus = tsconfig('auth_status');
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
            $listData['data'][$key]['name'] = msubstr($val['name'],0,20);
            $listData['data'][$key]['company'] = msubstr($val['company_name'],0,20);;
            $listData['data'][$key]['state'] = $verifyStatus[$val['is_verify']].'<br>'.$authStatus[$val['is_auth']];
            $listData['data'][$key]['date'] = date('Y-m-d',$val['ctime']);
            $listData['data'][$key]['is_top'] = ($val['is_top'])?'<font color="red">置顶</font>':'否';
            $listData['data'][$key]['views'] = $val['views'];
            $listData['data'][$key]['DOACTION'] = '<a href="'.U('game/admin/setinfo',array('id' => $val['id'],'tabHash'=>'setinfo')).'">编辑</a> | 
            		<a href="'.U('game/admin/setinfo_step2',array('id' => $val['id'],'tabHash'=>'setinfo')).'">代理商</a> | 
            				<a href="#" onclick="if(confirm(\'确定要删除吗？\')){document.location.href=\''.U('game/admin/delGame',array('id' => $val['id'],'tabHash'=>'index')).'\';}">删除</a>';
        }
        $this->displayList($listData);
    }
    
    /**
     * 待审核页面列表
     */
    public function waitverify ()
    {
    	$_REQUEST['tabHash'] = 'waitverify';
    	//按钮
    	//$this->pageButton[] = array('uid','title'=>'搜索', 'onclick'=>"admin.fold('search_form')");
    	$this->pageButton[] = array('uid','title'=>'删除', 'onclick'=>"admin.deleteInfo();");
    	 
    	//构造搜索条件
    	//列表key值 DOACTION表示操作
    	$this->pageKeyList = array('id','name','company','date','DOACTION');
    	$listData = M('Game')->where('is_verify=0')->order('id desc')->findPage(15);
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
    		$listData['data'][$key]['name'] = msubstr($val['name'],0,20);
    		$listData['data'][$key]['company'] = msubstr($val['company'],0,20);
    		$listData['data'][$key]['date'] = date('Y-m-d',$val['ctime']);
    		$listData['data'][$key]['DOACTION'] = '<a href="'.U('game/admin/setinfo',array('id' => $val['id'],'tabHash'=>'setinfo')).'">查看</a> |
    						<a href="'.U('game/admin/doverify',array('id' => $val['id'],'tabHash'=>'setinfo')).'">审核</a> |
            				<a href="#" onclick="if(confirm(\'确定要删除吗？\')){document.location.href=\''.U('game/admin/delGame',array('id' => $val['id'],'tabHash'=>'index')).'\';}">删除</a>';
    	}
    	$this->displayList($listData);
    }
    
    /**
     * 更新 / 创建信息
     */
    public function setinfo ()
    {
    	$id  = $_REQUEST['id'];
        if ($id)
        {
            $info = D('Game')->find($id);
        } 
        if ($_POST)
	    {
	        $_POST['targets'] = implode(',',$_POST['targets']);
	        $_POST['areas'] = implode(',',$_POST['areas']);
	        $_POST['platform'] = implode(',',$_POST['platform']);
	        $_POST['program'] = implode(',',$_POST['program']);
    		$uid = $this->mid;
    		
    		//公司信息
			$company_ret = model('Company')->setCompany($uid);
    		$company_id = $company_ret['id'];
            //dump($company_id);die;
    		$_POST['company_id'] = $company_id;
    		//dump($_POST);
	        $save = D('Game')->setGame($uid);
            if ($save['ret'] == true)
            {
                $this->assign('jumpUrl',U('game/Admin/setinfo_step2',array('id'=>$save['id'])));
                return $this->success( '成功保存信息，下一步设置代理信息');
            }else 
            {
                return $this->error( $save['msg'] );
            }
	    }
	    
        // 列表key值 DOACTION表示操作
		$this->pageKeyList = array('id','name','targets','areas','tags','schedule','stage','stage_date',
				'download','platform','is_online','logo','program','introduce','is_verify','is_auth','is_recommend','is_top','company_id');
    	
    	//字段属性
    	$this->opt['targets'] = tsconfig('need_targets');
    	$this->opt['areas'] = tsconfig('cooperation_area');
    	$this->opt['schedule'] = tsconfig('game_schedule');
    	$this->opt['stage'] = tsconfig('game_stage');
    	$this->opt['platform'] = tsconfig('game_platform');
    	$this->opt['is_online'] = tsconfig('yesorno');
    	$this->opt['program'] = tsconfig('program_platform');
    	$this->opt['is_verify'] = tsconfig('yesorno_status');
    	$this->opt['is_auth'] = tsconfig('yesorno_status');
    	$this->opt['is_recommend'] = tsconfig('yesorno');
    	$this->opt['is_top'] = tsconfig('yesorno');
         ;
		// 表单URL设置
		$this->savePostUrl = U('game/Admin/setinfo',array('id' => $id));
        $this->notEmpty = array
        (
        	'name','targets','areas','schedule','stage','stage_date','download','platform',
        		'is_online','logo','program','introduce','company_id'
        );
 
		$this->displayConfig($info);
    }


    /**
     * 添加代理商信息
     */
    public function setinfo_step2 ()
    {
    	$id  = intval($_REQUEST['id']);
    	if ($id)
    	{
    		$agents = D('Game')->findAgent($id);
    	}else{
    		$this->error('缺少游戏ID');
    	}
    	if ($_POST)
    	{
    		$_POST['area'] = implode(',',$_POST['area']);
    		$_POST['platform'] = implode(',',$_POST['platform']);
    		$save = D('Game')->setAgent($id);
    		if ($save['ret'] == true)
    		{
    			$this->assign('jumpUrl',U('game/Admin/index',array('id'=>$id)));
    			return $this->success( '成功保存信息');
    		}else
    		{
    			return $this->error( $save['msg'] );
    		}
    	}
    
    	//字段属性
    	$area = tsconfig('cooperation_area');
    	$platform = tsconfig('game_platform');
    	$price = tsconfig('has_cost');
		
    	$this->assign('id',$id);
        $this->assign('area',$area);
        $this->assign('platform',$platform);
        $this->assign('price',$price);
        $this->assign('agents',$agents);

        $this->display('agent');
    }
    
    /**
     * 删除代理商信息
     */
    public function delAgent ()
    {
    	$id  = intval($_REQUEST['id']);
    	$game_id  = intval($_REQUEST['game_id']);
    	$ret = D('Game')->delAgent($id);
    	if ($ret)
    	{
    		$this->assign('jumpUrl',U('game/Admin/setinfo_step2',array('id'=>$game_id)));
    		return $this->success( '删除成功');
    	}else
    	{
    		return $this->error( $save['msg'] );
    	}
    }
    
    /**
     * 删除游戏信息
     */
    public function delGame ()
    {
    	$id  = intval($_REQUEST['id']);
    	$ret = D('Game')->delGame($id);
    	if ($ret)
    	{
    		$this->assign('jumpUrl',U('game/Admin/index'));
    		return $this->success( '删除成功');
    	}else
    	{
    		return $this->error( $save['msg'] );
    	}
    }
    
    /**
     * 审核游戏
     */
    public function doverify ()
    {
    	$id  = intval($_REQUEST['id']);
    	$ret = D('Game')->verify($id);
    	if ($ret)
    	{
    		$this->assign('jumpUrl',U('game/Admin/waitverify'));
    		return $this->success( '审核成功');
    	}else
    	{
    		return $this->error( $save['msg'] );
    	}
    }
}
?>