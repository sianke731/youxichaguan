<?php
/**
 * 导入WORDPRESS文章管理
 * @author  JTee<sianke731@126.com>
 * @version TS3.0
 */
// 加载后台控制器
tsload(APPS_PATH.'/admin/Lib/Action/AdministratorAction.class.php');
tsdefine('DB_CONFIG_YOUXI','mysql://root:root@localhost:3306/youxi');
class ImportAction extends AdministratorAction
{
    function _initialize ()
    {
        parent::_initialize();
    }
    
    /**
     * 文章导入
     */
    function import ()
    {
    	set_time_limit(0);
    	$logfile = DATA_PATH.'/'.'wp2thinksns.php';

    	/*$model = M('')->table('youxi.wp_posts a')->join('youxi.wp_postmeta b ON a.ID=b.post_id')->join('youxi.wp_postmeta c ON a.ID=c.post_id')
    	->join('youxi.wp_posts d ON a.ID=d.post_parent');
    	$list = $model->field("a.ID,a.post_date,a.post_content,a.post_title,GROUP_CONCAT(b.meta_value SEPARATOR ',') as type_id,c.meta_value as views,d.guid")->
    	where("a.post_type='post' AND b.meta_key='_edit_last' AND c.meta_key='views' AND a.post_status='publish' AND d.post_type='attachment' AND a.ID=".$all['ID'])->select();*/
    	
    	$listall = M('')->table('youxi.wp_posts a')->field("a.ID,a.post_date,a.post_content,a.post_title")->where("a.post_type='post' AND a.post_status='publish'")->select();
    	$logstr = '开始置换'.date('Y-m-d H:i:s').'<br>';
	    foreach($listall as $all){
	        $list = M('')->table('youxi.wp_postmeta b')->where("b.post_id=".$all['ID'])->select();
	        $type_ids = array();
	        $views = 0;
	        $attach_id = 0;
	        foreach($list as $val){
	        	if($val['meta_key'] == '_thumbnail_id'){//判断是否有图片
	        		$ret = M('')->table('youxi.wp_posts')->where('ID='.$val['meta_value'])->find();
	        		$val['guid'] = $ret['guid'];
	        		//echo $val['guid'].'<br>';die;
	        		$files = file_get_contents($val['guid']);
	        		$temp = explode('.',$val['guid']);
	        		$file['savepath'] = UPLOAD_PATH.'/'.date('Y/md/H/');
	        		$file['extension'] = $temp[count($temp)-1];
	        		$file['savename'] = uniqid().'.'.$file['extension'];
	        		mkdir($file['savepath'], 0777, true);
	        		//echo $file['savepath'].$file['savename'].'<br>';
	        		@file_put_contents($file['savepath'].$file['savename'],$files);
	        		$fdata['app_name'] = 'news';
	        		$fdata['table'] = 'news';
	        		$fdata['uid'] = '1';
	        		$fdata['ctime'] = strtotime($all['post_date']);
	        		$fdata['name'] = $ret['post_title'].'.'.$file['extension'];
	        		$fdata['type'] = $ret['post_mime_type'];
	        		$fdata['size'] = abs(filesize($file['savepath'].$file['savename']));
	        		$fdata['extension'] = $file['extension'];
	        		$fdata['hash']   =  md5_file(auto_charset($file['savepath'].$file['savename'],'utf-8','gbk'));
	        		$fdata['save_path']   =  date('Y/md/H/');
	        		$fdata['save_name']   =  $file['savename'];
	        		$attach_id = M('Attach')->add($fdata);  //保存图片
	        		//print_r($attach_id);die;
	        	}
	        	if($val['meta_key'] == '_edit_last'){//判断是否是分类
	        		$type_ids[] = $val['meta_value'];
	        	}
	        	if($val['meta_key'] == 'views'){//判断是否是点击数
	        		$views = $val['meta_value'];
	        	}
	        }
        	$ndata['type_id'] = implode(',',$type_ids);
        	$ndata['uid'] = '1';
        	$ndata['news_title'] = $all['post_title'];
        	$ndata['news_content'] = $all['post_content'];
        	$ndata['image'] = $attach_id;
        	$ndata['state'] = '1';
        	$ndata['hits'] = $views;
        	$ndata['created'] = strtotime($all['post_date']);
        	$ndata['updated'] = strtotime($all['post_date']);
        	$news_id = M('News')->add($ndata);  //保存文章
        	$logstr .= $all['ID'].'转换文件成功'.$news_id.'<br>';
        }
	    $logstr .= '结束置换'.date('Y-m-d H:i:s');
        @file_put_contents($logfile,$logstr);
        echo 'do all';
        die;
    }
}
?>