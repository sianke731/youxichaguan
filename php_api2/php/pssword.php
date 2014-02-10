<?php
/*--------------------------------
程序版权：上海创明信息技有限公司
服务热线：4008885262
技术  QQ：2355373292
修改时间：2013-08-18
程序功能：创明网PHP接口示例 通过接口进行 取短信余额
说明:		http://dxhttp.c123.cn/up/?uid=用户账号&pwd=MD5位32密码&npwd=MD5位32密码
状态:
	100			修改成功
    101			验证失败
    107			频率过快
    108			新密不为空
    109			帐号已已冻结
    114			帐号被锁
    115			操作失败
    116			禁止接口发送
    117		    绑定IP不正确

--------------------------------*/
$url='http://dxhttp.c123.cn/up/';
$uid = '500100860001';		//用户账号
$pwd = 'adc';		//密码
$npwd='abcabc123';  //新用户的密码
$res = up($url,$uid,$pwd,$npwd);  
function up($url,$uid,$pwd,$npwd)
{
	$data = array
		(
		'uid'=>$uid,					//用户账号
		'pwd'=>strtolower(md5($pwd)),   //MD5位32密码
		'npwd'=>strtolower(md5($npwd)) 
		);
	$re= postSMS($url,$data);			//POST方式提交
	if(trim($re) == '100' )
	{
		echo  "修改密码成功";
	}
	else 
	{
		echo  "修改密码失败! 状态:".$re;
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