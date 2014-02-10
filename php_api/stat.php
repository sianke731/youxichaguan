<?php
/*--------------------------------
程序版权：上海创明信息技有限公司
服务热线：4008885262
技术  QQ：2355373292
修改时间：2013-08-18
程序功能：创明网PHP接口示例 通过接口进行取状态；
修改日期:	2013-08-12
说明:	http://smsapi.c123.cn/OpenPlatform/OpenApi?action=getSendState&ac=用户账号&authkey=认证密钥
状态:
	1 操作成功
	0 帐户格式不正确(正确的格式为:员工编号@企业编号)
	-1 服务器拒绝(速度过快、限时或绑定IP不对等)如遇速度过快可延时再发
	-2 密钥不正确
	-3 密钥已锁定
*/
$url='http://smsapi.c123.cn/DataPlatform/DataApi';
$ac='1001@500100080001';	//用户账号
$authkey = '9FFC0C71280583642CC8ACCF73788888';		//认证密钥
getStat($url,$ac,$authkey);
function getStat($url,$ac,$authkey)
{
	$data = array
		(
		'action'=>'getSendState',  //取状态
		'ac'=>$ac,					  //用户账号
		'authkey'=>$authkey,	     //认证密钥
		);
	$re= postSMS($url,$data);			//POST方式提交
    preg_match_all('/result="(.*?)"/',$re,$res);
	if(trim($res[1][0]) == '1' )  //成功返回  顺序编号 ，发送编号，发送号码，发送编号，发送状态，
	{
	    preg_match_all('/\<Item\s+(.*?)\s+\/\>/',$re,$item);
	    for($i=0;$i<count($item[1]);$i++){
		preg_match_all('/msgid="(.*?)"/',$item[1][$i],$msgid);
		preg_match_all('/mobile="(.*?)"/',$item[1][$i],$mobile);
		preg_match_all('/result="(.*?)"/',$item[1][$i],$result);
		preg_match_all('/return="(.*?)"/',$item[1][$i],$return);
		if($return[1][0]=='DELIVRD'){
		  $stat['msgid']=trim($msgid[1][0]);  //发送编号
		  $stat['mobile']=trim($mobile[1][0]); //发送号码
		  $stat['result']=trim($result[1][0]);    //发送状态
		  $stat['return']=trim($return[1][0]);  //运营商返回 
		  $stat_arr[]=$stat;   //$stat_arr数组存储短信状态的相关信息
		  }
		}
	 if(is_array($stat_arr))
	 {
	    
	     echo "取状态成功";
	 }
		
	}
	else  //发送失败的返回值
	{
	     switch(trim($res[1][0])){
			case  0: echo "帐户格式不正确(正确的格式为:员工编号@企业编号)";break; 
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