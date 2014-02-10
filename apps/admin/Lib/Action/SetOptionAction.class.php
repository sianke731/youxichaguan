<?php
/**
 * 后台，选项配置控制器
 * @author dng <imdengshuang@qq.com> 
 */
// 加载后台控制器
tsload ( APPS_PATH . '/admin/Lib/Action/AdministratorAction.class.php' );
class SetOptionAction extends AdministratorAction {
	
	/**
	 * 初始化，页面标题，用于双语
	 */
	public function _initialize() {
		parent::_initialize ();
	}
	public $type;
	
	public function index(){		
		$name_array = array(
			"1"=>"城市",
			"2"=>"公司类型",
			"3"=>"职位类型",
			"4"=>"活动类型",
			"5"=>"需求目标",
			"6"=>"合作区域",
			"7"=>"游戏标签",
			"8"=>"程序平台",
		);

		$type = $_GET['type_id']?$_GET['type_id']:1;
		//var_dump($type);
		//$type=1;
		$this->type=$type;
		$list = M("option")->where("type=".$this->type)->select();
		//print_r($list);die;
		//var_dump($name_array);die;
		$operation_name = $name_array[$this->type];
		$this->assign("operation_name",$operation_name);
		$this->assign("list",$list);
		$this->assign("type_id",$type);
		$this->display();
	}
	public function doeditOption(){
		//dump($_POST);
		$post_data = $_POST;
		foreach ($post_data['title'] as $key => $value) {
			$data[$key] = array(
				"title" => $post_data['title'][$key],
				"sort" => $post_data['sort'][$key],
			); 
		}
		foreach ($data as $key => $value) {
			$data[$key] = array_filter($value);
			if(empty($data[$key])){
				unset($data[$key]);
			}
		}
		foreach ($data as $key => $value) {
			# code...
			M("option")->where("id=".$key)->data($value)->save();
		}
		//dump($data);
		$this->success();
	}
	public function addOption(){
		$name_array = array(
			"1"=>"城市",
			"2"=>"公司类型",
			"3"=>"职位类型",
			"4"=>"活动类型",
			"5"=>"需求目标",
			"6"=>"合作区域",
			"7"=>"游戏标签",
			"8"=>"程序平台",
		);
		
		$type = $_GET['type_id']?$_GET['type_id']:1;
		//var_dump($type);
		//$type=1;
		$this->type=$type;
		$operation_name = $name_array[$this->type];
		$this->assign("operation_name",$operation_name);
		$this->assign("type_id",$type);
		$this->display("add");
	}
	public function doaddOption(){
		$type = $_GET['type_id']?$_GET['type_id']:1;
		$data['type'] = intval($type);
		$data['addtime'] = time();
		$data['title'] = $_POST['title'];
		$data['sort'] = intval($_POST['sort']);
		M("option")->data($data)->add();
		$this->success();
	}
	public function dodeleteOption(){
		$ids = $_POST['ids'];
		M("option")->where("id in (".$ids.")")->delete();
		echo("1");
	}
	public function tj_tag(){
		$list = M("game_tag")->where("type=1")->field("id,title,sort")->select();
		$this->assign("list",$list);
		$this->assign("operation_name","游戏标签");
		$this->display();
	}
	public function addtj_tag(){
		$this->assign("operation_name","游戏标签");
		$this->display("add_tag");
	}
	public function doadd_tag(){
		$data['type'] = 1;
		$data['addtime'] = time();
		$data['title'] = $_POST['title'];
		$data['sort'] = intval($_POST['sort']);
		M("game_tag")->data($data)->add();
		$this->success();
	}
	public function doedittag(){
		$post_data = $_POST;
		foreach ($post_data['title'] as $key => $value) {
			$data[$key] = array(
				"title" => $post_data['title'][$key],
				"sort" => $post_data['sort'][$key],
			); 
		}
		foreach ($data as $key => $value) {
			$data[$key] = array_filter($value);
			if(empty($data[$key])){
				unset($data[$key]);
			}
		}
		foreach ($data as $key => $value) {
			# code...
			M("game_tag")->where("id=".$key)->data($value)->save();
		}
		//dump($data);
		$this->success();
	}
	public function dodeletetag(){
		$ids = $_POST['ids'];
		M("game_tag")->data(array("type"=>0))->where("id in (".$ids.")")->save();
		echo("1");
	}
}