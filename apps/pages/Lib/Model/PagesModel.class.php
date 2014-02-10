<?php
/**
 * 单页面业务逻辑 
 * 
 * @version TS3.0 
 * @name PagesModel
 * @author JTee<sianke731@126.com>
 *
 */
class PagesModel extends Model
{
    /**
     * 使用的表名称
     *
     * @var string
     */
	protected	$tableName	=	'pages';
	
	/**
	 * 字段列表
	 *
	 * @var unknown_type
	 */
	protected	$fields		=	array 
	(
		0 => 'id', 
	    1 => 'title', 
	    2 => 'keywords', 
		3 => 'description',
	    4 => 'content',
	    5 => 'uid',
	    6 => 'ctime',
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
        array('title','require','标题不能为空'),
        array('content','require','内容不能为空'),
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
	public function setPages($uid)
	{
	    $ret = array( 'ret' => false, 'msg' => '更新信息失败' );
	    $model   =   D('Pages');

        if($model->create()) 
        {
            $model->uid = ($uid)?$uid:0 ;
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
	
	/**
	 * 删除页面信息
	 *
	 * @param int $id 字段值
	 * @return mixed
	 */
	public function delPages($id)
	{
		$model   =   D('Pages');
		$ret = $model->where('id='.$id)->delete();
		return $ret ;
	}
}