<?php
/**
 * 标签选择
 * @example W('Tag', array('width'=>'500px', 'appname'=>'support','apptable'=>'support','row_id'=>0,'tpl'=>'show','tag_url'=>'aaa','name'=>'public'))
 * @author zhishisoft
 * @version TS3.0
 */
class CompanyWidget extends Widget{

	/**
	 * @param string width wigdet显示宽度
     * @param string appname 资源对应的应用名称
     * @param string apptable 资源对应的表
     * @param integer row_id 资源在资源所在表中的主键ID,为0表示该资源为新加资源，需要在资源添加,往sociax_app_tag表添加相关数据
     * @param string tpl 显示模版，tag/show 默认是tag，如果为show表示只显示标签
     * @param string tag_url 标签上的链接前缀，为空表示标签没有链接，只针对tpl=show有效
     * @param string name 输入框的input名称,标签的ID存储的隐藏域名称为 {name}_tags
	 */
	public function render($data)
    {
        if(!empty($data['company_id'])){
            $company = M("company")->where("id=".$data['company_id'])->find();
            //dump(M()->getLastSql());die;
            
            if(strpos($company["type"], ",")){
            	$company["type"] = explode(",", $company["type"]);
            }
            //dump($company);die;
            $var["company"] = $company;
            $city_arr = M("area")->where("pid=".$company['province'])->order("sort asc")->select();
            $var["city_arr"] = $city_arr;
        }
        //dump($data);die;
        $company_type = M("option")->where("type=2")->order("sort asc")->field("id,title")->select();
        $province_arr = M("area")->where("pid=0")->order("sort asc")->select();
        $var["company_size"] = C("company_size");
        //dump($company);
        //$this->assign("company_type",$company_type);
        $var["company_type"] = $company_type;
        $var["province_arr"] = $province_arr;
    	$content = $this->renderFile(dirname(__FILE__)."/".$data['tpl'].".html", $var);
		return $content;
	}
    public function get_company_name(){
        $word  = t($_REQUEST['word']);
        $list = M("company")->where("name like '%".$word."%'")->order("is_verify desc,ctime desc")->field("id,name,is_verify")->limit(10)->select();
        //dump(M()->getLastSql());die;
        foreach ($list as $key => $value) {
            # code...
            if ($value['is_verify']==1) {
                # code...
                $list[$key]['is_verify'] = 1;
            }else{
                $list[$key]['is_verify'] = 0;
            }
        }
        if(!empty($list)){
            echo(json_encode(array("status"=>1,"data"=>$list)));die;
        }else{
            echo(json_encode(array("status"=>0)));die;
        }
    }
    public function get_company_info(){
        $id = t($_REQUEST['id']);
        $id = intval($id);
        if (empty($id)) {
            echo(json_encode(array("status"=>0,"msg"=>"id错误")));die;
        }
        $info = M("company")->where("id=$id")->find();
        if(!empty($info)){
            echo(json_encode(array("status"=>1,"info"=>$info)));die;
        }else{
            echo(json_encode(array("status"=>0)));die;
        }
    }
    public function get_city_arr(){
        $id = t($_REQUEST['id']);
        $id = intval($id);
        if (empty($id)) {
            echo(json_encode(array("status"=>0,"msg"=>"id错误")));die;
        }
        $city_arr = M("area")->where("pid=".$id)->select();
        if(!empty($city_arr)){
            echo(json_encode(array("status"=>1,"data"=>$city_arr)));die;
        }else{
            echo(json_encode(array("status"=>0)));die;
        }
    }
}