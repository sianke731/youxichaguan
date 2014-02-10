<?php
/**
 * 公司业务逻辑 
 * 
 * @version TS3.0 
 * @name PagesModel
 * @author JTee<sianke731@126.com>
 *
 */
class CompanyModel extends Model
{
    /**
     * 使用的表名称
     *
     * @var string
     */
	protected	$tableName	=	'company';
	
	/**
	 * 字段列表
	 *
	 * @var unknown_type
	 */
	protected	$fields		=	array 
	(
		0 => 'id', 
	    1 => 'name', 
	    2 => 'type', 
		3 => 'teams',
	    4 => 'introduce',
	    5 => 'province',
	    6 => 'city',
	    7 => 'address',
	    8 => 'site',
		9 => 'linkman', 
	    10 => 'phone', 
	    11 => 'email', 
		12 => 'qq',
	    13 => 'wechat',
	    14 => 'logo',
	    15 => 'is_verify',
	    16 => 'is_auth',
	    17 => 'is_recommend',
	    18 => 'is_top',
	    19 => 'views',
	    20 => 'collects',
	    21 => 'uid',
	    22 => 'ctime',
	    '_autoinc' => true, 
	    '_pk' => 'id'
	);
	
	/**
	 * 定义自动验证
	 * 
	 * @var array
	 */
    protected $_validate    =   array
    (
        array('name','require','公司名称不能为空'),
        array('type','require','公司类型不能为空'),
    );
	
    /**
	 * 自动填充
	 *
	 * @var array
	 */
	protected $_auto = array 
	( 
	    array('ctime','time',1,'function'),
    );
    
	/**
	 * 更新信息
	 *
	 * @param array $data 字段值
	 * @return mixed
	 */
	public function setCompany($uid)
	{
	    $ret = array( 'ret' => false, 'msg' => '更新信息失败' );
	    $model   =   D('Company');
       // dump($_POST['company_name']);die;
        $has_company = $model->where("name like '".($_POST['company_name'])."'")->find();
        //dump(M()->getLastSql());//die;
        //dump($has_company);die;
        if($has_company){
            //dump($has_company);die;
            //公司存在，且已认证则直接返回公司 id，未认证进行修改
            if($has_company['is_verify']==1){
                return array( 'ret' => true, 'msg' => '成功更新信息','id'=>$has_company['id'] );
            }else{
                $company_id = $has_company['id'];
            }
        }
        //echo $company_id;die;
        //var_dump($model->create());die;
        if($model->create()) 
        {
            $model->id = ($company_id)?$company_id:0 ;
            //dump($model->id);die;
            $data = $_POST;
            $data_insert = array();
            $data_insert['name'] = htmlspecialchars($data['company_name']);
            $data_insert['type'] = join(",",$data['company_type']);
            $data_insert['teams'] = intval($data['company_size']);
            $data_insert['introduce'] = htmlspecialchars($data['company_introduce']);
            $data_insert['province'] = htmlspecialchars($data['company_province']);
            $data_insert['city'] = htmlspecialchars($data['company_city']);
            $data_insert['site'] = htmlspecialchars($data['company_site']);
            $data_insert['linkman'] = htmlspecialchars($data['company_linkman']);
            $data_insert['address'] = htmlspecialchars($data['company_address']);
            $data_insert['phone'] = htmlspecialchars($data['company_phone']);
            $data_insert['email'] = htmlspecialchars($data['company_email']);
            $data_insert['qq'] = htmlspecialchars($data['company_qq']);
            if(!empty($data['logo_company'])){
                $data_insert['logo'] = intval($data['logo_company']);
            }
            //dump($model->id);die;
            if ($model->id)
            {
                //dump($model->id);
            	$res = $model->where("id=".$model->id)->data($data_insert)->save();
                //dump($model->id);
                //dump(M()->getLastSql());die;
                //dump($res);die;
                //if($res){
                $result = $company_id;
                $new_id = $company_id;
                //dump($result);die;
                //}
            	M("company_cp")->where('company_id='.$company_id)->delete();
            	M("company_channel")->where('company_id='.$company_id)->delete();
                //dump(M()->getLastSql());die;
            	M("company_platform")->where('company_id='.$company_id)->delete();
            	M("company_publish")->where('company_id='.$company_id)->delete();
            	M("company_has_pub")->where('company_id='.$company_id)->delete();
            	M("company_outer")->where('company_id='.$company_id)->delete();
            	M("company_investment")->where('company_id='.$company_id)->delete();
            	M("company_service")->where('company_id='.$company_id)->delete();
            	
            }else{
                $data_insert['is_verify'] = 0;
                $data_insert['is_auth'] = 0;
                $data_insert['is_recommend'] = 0;
                $data_insert['is_top'] = 0;
//                 dump($_SESSION);die;
                $data_insert['uid'] = $_SESSION["mid"];
                $data_insert['ctime'] = time();
            	$result = $new_id = $model->add($data_insert);
            }
            //dump($data_insert['type']);die;
            foreach ($data['company_type'] as $key => $value){
	            switch ($value)
	            {
	            	case "5":
	            		M("company_cp")->data(array("company_id"=>$new_id,"tags"=>$data['tags']))->add();
	            		break;
	            	case "7":
	            		$data_7 = array();
	            		//dump($data);die;
	            		$data_7['company_id'] = $new_id;
	            		$data_7['types'] = implode(",",$data['channel_type']);
	                    //dump($data);die;
	            		$data_7['require'] = intval($data['platform_require_7']);
	            		$data_7['models'] = implode(",",$data['pricing_model']);
	            		$data_7['user_cover'] = implode(",",$data['user_cover']);
	                    //dump($data_7);die;
	            		M("company_channel")->data($data_7)->add();
	                    //dump(M()->getLastSql());die;
	            		break;
	            	case "8":
	            		$data_8 = array();
	            		$data_8['company_id'] = $new_id;
	            		$data_8['types'] = implode(",",$data['shop_type']);
	            		M("company_platform")->data($data_8)->add();
	                    //dump(M()->getLastSql());die;
	            		break;
	            	case "9":
	            		$data_9 = array();
	            		$data_9['company_id'] = $new_id;
	            		$data_9['areas'] = implode(",",$data['cooperation_area_7']);
	            		$data_9['cooperation_type'] = implode(",",$data['cooperation_type']);
	                    //dump($data['platform']);die;
	            		$data_9['platform_require'] = intval($data['platform_require']);
	            		$data_9['network_require'] = intval($data['networking']);
	            		$data_9['stage_require'] = intval($data['game_phase']);
	            		$data_9['team_require'] = htmlspecialchars($data['game_team_require']);
	//             		$data_9['is_experience'] = intval($data['experience']);
	//             		$data_9['games'] = intval($data['has_game_num']);
	//             		$data_9['price'] = intval($data['has_cost']);
	                    //dump($data_9);
	            		$res = M("company_publish")->data($data_9)->add();
	            		$company_widget_game_hidden = $_POST["company_widget_game_hidden"];
	            		foreach ($company_widget_game_hidden as $k => $v){
	            			$tmp_arr = explode("-", $v);
	            			$data_9_has_pub = array();
	            			$data_9_has_pub["company_id"] = $new_id;
	            			$data_9_has_pub["name"] = $tmp_arr["0"];
	            			$data_9_has_pub["icon"] = $tmp_arr["1"];
	            			$data_9_has_pub["area"] = $tmp_arr["2"];
	            			$data_9_has_pub["platform"] = $tmp_arr["3"];
	            			$data_9_has_pub["cost"] = $tmp_arr["4"];
	            			M("company_has_pub")->data($data_9_has_pub)->add();
	            		}
	            		
	                    //dump($res);
	                    //dump(M()->getLastSql());die;
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
	            		M("company_service")->data($data_12)->add();
	            		break;
	            	default:
	            }
            }
            //dump($result);die;
            if($result) 
            {
                $ret = array( 'ret' => true, 'msg' => '成功更新信息','id'=>$result );
            }else 
            {
                $ret['msg'] = $model->getError();
            }
        }else
        {
            $ret['msg'] = $model->getError(); 
        }
        return $ret ;
	}
}