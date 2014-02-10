<?php

tsload(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');

class IndexAction extends AdministratorAction {

	public function _initialize(){
		parent::_initialize();
	}

	public function index() {
		$nav = array();
		foreach($this->navList as $k=>$v){
			$nav[] = array('name'=>L('PUBLIC_APPNAME_'.strtoupper($k)),'appname'=>$k,'url'=>$v);
		}
		//print_r($nav);die;
		$this->setTitle( L('PUBLIC_SYSTEM_MANAGEMENT') );
		
		//JTee 2013-12-21 权限整理
		if(isset($_GET['is_admin'])){
			session('is_admin',$_GET['is_admin']);
		}
		if(session('is_admin') == 'true'){
			$this->assign('nav',$nav);
			$this->assign('channel', C('admin_channel'));
    		$this->assign('menu',    C('admin_menu'));
		}else{
			$this->assign('channel', C('admin_channel_small'));
			//$this->assign('menu',    array_replace(C('admin_menu'),C('admin_menu_small')));   //PHP5.3+实用
			//以下适用于PHP5.3以下版本
			$menuList = C('admin_menu');
			$menuListSmall = C('admin_menu_small');
			$menuList['index'] = $menuListSmall['index'];
			$this->assign('menu',    $menuList);
		}
		$this->display();
	}
	
	/**
	 * 意见反馈  JTee 2014-01-19
	 */
	public function suggest()
	{
		$_REQUEST['tabHash'] = 'index';
        //按钮
        //$this->pageButton[] = array('uid','title'=>'搜索', 'onclick'=>"admin.fold('search_form')");
        //$this->pageButton[] = array('uid','title'=>'删除', 'onclick'=>"admin.deleteInfo();");
       
        //构造搜索条件
        //列表key值 DOACTION表示操作
		$this->pageKeyList = array('id','name','email','message','ctime','DOACTION');
        $listData = D('Suggest')->order('id desc')->findPage(15);
        foreach ($listData['data'] as $key => $val)
        {
            $listData['data'][$key]['id'] = $val['id'];
            $listData['data'][$key]['name'] = msubstr($val['name'],0,20);
            $listData['data'][$key]['email'] = msubstr($val['email'],0,20);
            $listData['data'][$key]['message'] = msubstr($val['message'],0,70);
            $listData['data'][$key]['ctime'] = date('Y-m-d',$val['ctime']);
            $listData['data'][$key]['DOACTION'] = '<a href="'.U('admin/Index/showsuggest',array('id' => $val['id'],'tabHash'=>'setinfo')).'">查看</a>|<a href="#" onclick="if(confirm(\'确定要删除吗？\')){document.location.href=\''.U('admin/Index/delsuggest',array('id' => $val['id'],'tabHash'=>'index')).'\';}">删除</a>';
        }
        $this->displayList($listData);
	}
	
	/**
	 * 查看意见反馈  JTee 2014-01-19
	 */
	public function showsuggest()
	{
		$id  = $_REQUEST['id'];
		if ($id)
		{
			$info = D('Suggest')->find($id);
			$this->assign($info);
		}else{
			$this->error('参数错误');
		}
        $this->display();
	}
	
	/**
	 * 删除意见反馈  JTee 2014-01-19
	 */
	public function delsuggest()
	{
		$id  = intval($_REQUEST['id']);
    	$ret = D('Suggest')->where('id='.$id)->delete();
    	if ($ret)
    	{
    		return $this->success( '删除成功');
    	}else
    	{
    		return $this->error('删除失败');
    	}
	}
	
}
?>