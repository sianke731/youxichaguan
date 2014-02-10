<?php
/**
 * 单网页后台管理
 * @author  JTee<sianke731@126.com>
 * @version TS3.0
 */
// 加载后台控制器
tsload(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');
class AdminAction extends AdministratorAction
{
    function _initialize ()
    {
    	$this->pageTitle['index'] = '单网页管理';
        $this->pageTitle['setinfo'] = '创建/修改页面';
        
        // tab选项
        $this->pageTab[] = array('title' => '单网页管理' , 'tabHash' => 'index' , 'url' => U('pages/Admin/index'));
        $this->pageTab[] = array('title' => '创建/修改页面' , 'tabHash' => 'setinfo' , 'url' => U('pages/Admin/setinfo'));
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
        $this->pageButton[] = array('uid','title'=>'添加页面', 'onclick'=>"location.href='".U('pages/admin/setinfo',array('tabHash'=>'setinfo'))."';");
       
        //构造搜索条件
        //列表key值 DOACTION表示操作
		$this->pageKeyList = array('id','title','date','DOACTION');
        $listData = M('Pages')->order('id desc')->findPage(15);
        foreach ($listData['data'] as $key => $val)
        {
            $listData['data'][$key]['id'] = $val['id'];
            $listData['data'][$key]['title'] = msubstr($val['title'],0,20);
            $listData['data'][$key]['date'] = date('Y-m-d',$val['ctime']);
            $listData['data'][$key]['DOACTION'] = '<a href="'.U('pages/admin/setinfo',array('id' => $val['id'],'tabHash'=>'setinfo')).'">编辑</a> | 
            		<a href="#" onclick="if(confirm(\'确定要删除吗？\')){document.location.href=\''.U('pages/admin/delPages',array('id' => $val['id'],'tabHash'=>'index')).'\';}">删除</a>';
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
            $info = D('Pages')->find($id);
        } 
        if ($_POST)
	    {
    		$uid = $this->mid;
	        $save = D('Pages')->setPages($uid);
            if ($save['ret'] == true)
            {
                $this->assign('jumpUrl',U('pages/Admin/index'));
                return $this->success( '保存信息');
            }else 
            {
                return $this->error( $save['msg'] );
            }
	    }
	    
        // 列表key值 DOACTION表示操作
		$this->pageKeyList = array('id','title','keywords','description','content');
         ;
		// 表单URL设置
		$this->savePostUrl = U('pages/Admin/setinfo',array('id' => $id));
        $this->notEmpty = array
        (
        	'title','content'
        );
 
		$this->displayConfig($info);
    }
    
    /**
     * 删除页面
     */
    public function delPages ()
    {
    	$id  = intval($_REQUEST['id']);
    	$ret = D('Pages')->delPages($id);
    	if ($ret)
    	{
    		$this->assign('jumpUrl',U('pages/Admin/index'));
    		return $this->success( '删除成功');
    	}else
    	{
    		return $this->error( $save['msg'] );
    	}
    }
    
    /**
     * 批量删除
     */
    public function delAllPages()
    {
    	$ret = array('status' => 0, 'data' => '请选择要删除的条目');
    	$id =  (array) $_POST['id'];
    	if ($id)
    	{
    		$ids = implode(',',$id);
    		if (D('Pages')->delete($ids))
    		{
    			$ret = array('status' => 1, 'data' => '成功删除信息');
    		}else
    		{
    			$ret = array('status' => 1, 'data' => '删除失败');
    		}
    	}
    	echo json_encode($ret);
    }
}
?>