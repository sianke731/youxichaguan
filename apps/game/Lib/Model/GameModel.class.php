<?php
/**
 * 游戏业务逻辑 
 * 
 * @version TS3.0 
 * @name GameModel
 * @author JTee<sianke731@126.com>
 *
 */
class GameModel extends Model
{
    /**
     * 使用的表名称
     *
     * @var string
     */
	protected	$tableName	=	'game';
	
	/**
	 * 字段列表
	 *
	 * @var unknown_type
	 */
	protected	$fields		=	array 
	(
		0 => 'id', 
	    1 => 'name', 
	    2 => 'targets', 
	    3 => 'areas', 
	    4 => 'tags',
	    5 => "schedule",
	    6 => "stage",
	    7 => "stage_date",
	    8 => "download",
	    9 => 'platform',
	    10 => 'is_online',
	    11 => 'logo',
	    12 => 'program',
	    13 => 'introduce',
	    14 => 'company_id',
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
        array('name','require','游戏名称不能为空'),
        array('targets','require','需求目标不能为空'),
        array('areas','require','合作区域不能为空'),
        array('schedule','require','游戏完成度不能为空'),
        array('stage','require','游戏阶段不能为空'),
        array('stage_date','require','阶段日期不能为空'),
        array('platform','require','平台不能为空'),
        array('logo','require','游戏LOGO不能为空'),
        array('program','require','程序平台不能为空'),
        array('introduce','require','游戏介绍不能为空'),
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
	public function setGame($uid)
	{
		//dump($uid);die;
	    $ret = array( 'ret' => false, 'msg' => '更新信息失败' );
	    $game   =   D('Game');
        if($game->create()) 
        {
        	//dump($game->id);die;
            if ($game->id)
            {
                $result = $game->save();
                //echo('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');
                $ret = array( 'ret' => true, 'msg' => '成功更新信息','id'=>$_POST['id']  );
                //die;
            }else 
            {
                $result = $game->add();
	            if($result) 
	            {
	                $ret = array( 'ret' => true, 'msg' => '成功更新信息','id'=>$result );
	            }else 
	            {
	                $ret['msg'] = $game->getError();
	            }
            }
        }else
        {
            $ret['msg'] = $game->getError(); 
        }
        return $ret ;
	}
	
	


	/**
	 * 查找代理商信息
	 *
	 * @param int $id 字段值
	 * @return mixed
	 */
	public function findAgent($id)
	{
		$model   =   D('Game_agent');
		$ret = $model->where('game_id='.$id)->select();
		return $ret ;
	}
	
	/**
	 * 删除游戏信息
	 *
	 * @param int $id 字段值
	 * @return mixed
	 */
	public function delGame($id)
	{
		$model   =   D('Game');
		$ret = $model->where('id='.$id)->delete();
		D('Game')->where('game_id='.$id)->delete(); //删除代理公司
		return $ret ;
	}
	
	/**
	 * 审核游戏信息
	 *
	 * @param int $id 字段值
	 * @return mixed
	 */
	public function verify($id)
	{
		$model   =   D('Game');
		$ret = $model->where('id='.$id)->save(array('is_verify'=>1));
		return $ret ;
	}
	
	/**
	 * 删除代理商信息
	 *
	 * @param int $id 字段值
	 * @return mixed
	 */
	public function delAgent($id)
	{
		$model   =   D('Game_agent');
		$ret = $model->where('id='.$id)->delete();
		return $ret ;
	}
	
	/**
	 * 更新代理商信息
	 *
	 * @param array $data 字段值
	 * @return mixed
	 */
	public function setAgent($game_id)
	{
		$ret = array( 'ret' => false, 'msg' => '更新信息失败' );
		$model   =   D('Game_agent');
		if($model->create())
		{
			$model->game_id = $game_id ;
			if ($model->id)
			{
				$result = $model->save();
			}else
			{
				$result = $model->add();
			}
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