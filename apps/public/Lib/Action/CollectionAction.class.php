<?php
/**
 * 收藏控制器
 * @author jason <yangjs17@yeah.net> 
 * @version TS3.0
 */
class CollectionAction extends Action {
	
	private $conlist = array('member','game','company');

	/**
	 * 我的收藏页面
	 */
	/*public function index() {
		$map['uid'] = $GLOBALS['ts']['uid'];
		$weiboSet = model('Xdata')->get('admin_Config:feed');
		$this->assign($weiboSet);
		// TODO:后续可能由表中获取语言KEY
		$d['tabHash'] = array(
							'feed'	=> L('PUBLIC_WEIBO')				// 微博
						);

		$d['tab'] = model('Collection')->getCollTab($map);
		$this->assign($d);
		
		// 安全过滤
		$t = t($_GET['t']);
		!empty($t) && $map['source_table_name'] = $t;
		$key = t($_POST['collection_key']);
		if ($key === '') {
			$list = model('Collection')->getCollectionList($map, 20);
		} else {
			$list = model('Collection')->searchCollections($key, 20);	
			$this->assign('collection_key', $key);		
			$this->assign('jsonKey', json_encode($key));		
		}
		$this->assign($list);
		$this->setTitle(L('PUBLIC_COLLECTION_INDEX'));					// 我的收藏
		// 是否有返回按钮
		$this->assign('isReturn', 1);
		// 获取用户统计信息
		$userData = model('UserData')->getUserData($GLOBALS['ts']['mid']);
		$this->assign('favoriteCount', $userData['favorite_count']);

		$userInfo = model('User')->getUserInfo($this->mid);
		$this->setTitle('我的收藏');
        $this->setKeywords($userInfo['uname'].'的收藏');
		$this->display();
	}*/
	
	//JTee 2014-1-5 收藏的名片、产品、公司
	public function index() {
		$map['a.uid'] = $GLOBALS['ts']['uid'];
		$weiboSet = model('Xdata')->get('admin_Config:feed');
		$this->assign($weiboSet);
		$conlist = $this->conlist;
		$type = t($_GET['type']);
		if(!in_array($type,$conlist)){
			$type = 'member';
		}
		$map['a.source_table_name'] = $type;
		switch($type){
			case 'member':
				$list = M('Collection a')->JOIN(C('DB_PREFIX').'member b ON a.source_id=b.id')
					->field('a.collection_id,a.ctime,b.id,b.name,b.company_id,b.position')->where($map)->order('a.ctime DESC')->findPage(20);
				break;
			case 'game':
				$list = M('Collection a')->JOIN(C('DB_PREFIX').'game b ON a.source_id=b.id')
					->field('a.collection_id,a.ctime,b.id,b.name,b.company_id,b.is_verify')->where($map)->order('a.ctime DESC')->findPage(20);
				break;
			case 'company':
				$list = M('Collection a')->JOIN(C('DB_PREFIX').'company b ON a.source_id=b.id')
					->field('a.collection_id,a.ctime,b.id,b.name,b.uid,b.is_verify')->where($map)->order('a.ctime DESC')->findPage(20);
				break;
			default:
				break;
		}
		$this->assign($list);
		
		$this->assign('type', $type);
		$this->assign('verify_list', C('verify_status'));
		// 获取用户统计信息
		$userData = model('UserData')->getUserData($GLOBALS['ts']['mid']);
		$this->assign('favoriteCount', $userData['favorite_count']);
	
		$userInfo = model('User')->getUserInfo($this->mid);
		$this->setTitle('我的收藏');
		$this->setKeywords($userInfo['uname'].'的收藏');
		$this->display();
	}
	
	
	/**
	 * JTee 2014-1-5 取消收藏
	 */
	public function del() {
		$type = t($_GET['type']);
		$map['a.uid'] = $GLOBALS['ts']['uid'];
		$map['a.collection_id'] = intval($_GET['collection_id']);
		$map['a.source_table_name'] = $type;
		$conlist = $this->conlist;
		if(!in_array($type,$conlist)){
			$this->error('传入的参数有误');
		}
		$ret = M('Collection a')->where($map)->find();
		if($ret){
			$result = M('Collection')->where('collection_id='.$map['a.collection_id'])->delete();
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
}