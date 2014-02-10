<?php
/*--------------------------------
程序版权：上海创明信息技有限公司
服务热线：4008885262
技术  QQ：2355373292
修改时间：2013-08-18
程序功能：创明网PHP接口示例 通过接口进行取回复；
修改日期: 2013-08-12
接口说明:  	http://smsapi.c123.cn/DataPlatform/DataApi?action=getReply&ac=用户账号&authkey=认证密钥
状态:
	 1 操作成功
	 0 帐户格式不正确(正确的格式为:员工编号@企业编号)
	-1 服务器拒绝(速度过快、限时或绑定IP不对等)如遇速度过快可延时再发
	-2 密钥不正确
	-3 密钥已锁定
--------------------------------*/
$url='http://smsapi.c123.cn/DataPlatform/DataApi';   
$ac='1001@500100080001';	                           	 //用户账号
$authkey = '9FFC0C71280583642CC8ACCF73788888';		     //认证密钥

 getReply($url,$ac,$authkey);
//一对一发送
function getReply($url,$ac,$authkey)
{
	$data = array
		(
		'action'=>'getReply',                            //取回复
		'ac'=>$ac,					                     //用户账号
		'authkey'=>$authkey,	                         //认证密钥
		); 
	$re=postSMS($url,$data);		                     //POST方式提交
	echo $re;
    preg_match_all('/result="(.*?)"/',$re,$res); 
	if(trim($res[1][0]) == '1' )                         //成功返回  顺序编号 回复时间 回复号码 回复内容
	{
	   preg_match_all('/\<Item\s+(.*?)\s+\/\>/',$re,$item);
	    for($i=0;$i<count($item[1]);$i++){
		preg_match_all('/id="(.*?)"/',$item[1][$i],$id);
		preg_match_all('/time="(.*?)"/',$item[1][$i],$time);
		preg_match_all('/mobile="(.*?)"/',$item[1][$i],$mobile);
		preg_match_all('/content="(.*?)"/',$item[1][$i],$content);
		
		$rec['id']=trim($id[1][0]);                       //顺序编号
	    $rec['time']=trim($time[1][0]);                   //回复时间
	    $rec['mobile']=trim($mobile[1][0]);               //回复号码
		$rec['content']=trim($content[1][0]);             //回复内容
		$reply_arr[]=$rec;                                //$reply_arr数组存储了短信回复的相关信息
	  }	
	  echo "获取回复成功";                                //发送成功返回的值
	}
	else                                                  //发送失败的返回值
	{
	     switch(trim($res[1][0])){
			case   0: echo "帐户格式不正确(正确的格式为:员工编号@企业编号)";break; 
			case  -1: echo "服务器拒绝(速度过快、限时或绑定IP不对等)如遇速度过快可延时再发";break;
			case  -2: echo " 密钥不正确";break;
			case  -3: echo "密钥已锁定";break;
			default:break;
		}
	}
}

function postSMS($url,$data='')
{
	$row = parse_url($url);
	$host = $row['host'];
	$port = $row['port'] ? $row['port']:80;
	$file = $row['path'];
	while (list($k,$v) = each($data)) 
	{
		$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
	}
	$post = substr( $post , 0 , -1 );
	$len = strlen($post);
	$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
	if (!$fp) {
		return "$errstr ($errno)\n";
	} else {
		$receive = '';
		$out = "POST $file HTTP/1.0\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Content-type: application/x-www-form-urlencoded\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Content-Length: $len\r\n\r\n";
		$out .= $post;		
		fwrite($fp, $out);
		while (!feof($fp)) {
			$receive .= fgets($fp, 128);
		}
		fclose($fp);
		$receive = explode("\r\n\r\n",$receive);
		unset($receive[0]);
		return implode("",$receive);
	}
}
?>