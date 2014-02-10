<?php
/*--------------------------------
程序版权：上海创明信息技有限公司
服务热线：4008885262
技术  QQ：2355373292
修改时间：2013-08-18
程序功能：创明网PHP接口示例 通过接口进行单发、群发；
接口说明: http://smsapi.c123.cn/OpenPlatform/OpenApi?action=sendOnce&ac=用户账号&authkey=认证密钥&cgid=通道组编号&c=短信内容&m=发送号码
状态:
	1 操作成功
	0 帐户格式不正确(正确的格式为:员工编号@企业编号)
	-1 服务器拒绝(速度过快、限时或绑定IP不对等)如遇速度过快可延时再发
	-2 密钥不正确
	-3 密钥已锁定
	-4 参数不正确(内容和号码不能为空，手机号码数过多，发送时间错误等)
	-5 无此帐户
	-6 帐户已锁定或已过期
	-7 帐户未开启接口发送
	-8 不可使用该通道组
	-9 帐户余额不足
	-10 内部错误
	-11 扣费失败

--------------------------------*/
$url='http://smsapi.c123.cn/OpenPlatform/OpenApi';           //接口地址
$ac='1001@500860720001';		                             //用户账号
$authkey = 'A9526D1320E5FDB3F8E8E4E861546D53';		         //认证密钥
$cgid='11';                                                  //通道组编号
$c = '用户您好，欢迎接入创明网接口测试！【创明网】';		 //内容
$m= '18980899944';	                                         //号码
$csid='3';                                                   //签名编号 ,可以为空时，使用系统默认的编号
$t='';                                                       //发送时间,可以为空表示立即发送,yyyyMMddHHmmss 如:20130721182038

sendSMS($url,$ac,$authkey,$cgid,$m,$c,$csid,$t);
                                                             //短信发送接口
function sendSMS($url,$ac,$authkey,$cgid,$m,$c,$csid,$t)
{
	$data = array
		(
		'action'=>'sendOnce',                                //发送类型 ，可以有sendOnce短信发送，sendBatch一对一发送，sendParam	动态参数短信接口
		'ac'=>$ac,					                         //用户账号
		'authkey'=>$authkey,	                             //认证密钥
		'cgid'=>$cgid,                                       //通道组编号
		'm'=>$m,		                                     //号码,多个号码用逗号隔开
		'c'=>iconv('gbk','utf-8',$c),		                 //如果页面是gbk编码，则转成utf-8编码，如果是页面是utf-8编码，则不需要转码
		'csid'=>$csid,                                       //签名编号 ，可以为空，为空时使用系统默认的签名编号
		't'=>$t                                              //定时发送，为空时表示立即发送
		);
	$re= postSMS($url,$data);			                     //POST方式提交
    preg_match_all('/result="(.*?)"/',$re,$res);
	if(trim($res[1][0]) == '1' )  //发送成功 ，返回企业编号，员工编号，发送编号，短信条数，单价，余额
	{
	
	    preg_match_all('/\<Item\s+(.*?)\s+\/\>/',$re,$item);
		for($i=0;$i<count($item[1]);$i++){
	    preg_match_all('/cid="(.*?)"/',$item[1][$i],$cid);
	    preg_match_all('/sid="(.*?)"/',$item[1][$i],$sid);
		preg_match_all('/msgid="(.*?)"/',$item[1][$i],$msgid);
		preg_match_all('/total="(.*?)"/',$item[1][$i],$total);
		preg_match_all('/price="(.*?)"/',$item[1][$i],$price);
		preg_match_all('/remain="(.*?)"/',$item[1][$i],$remain);
		
		$send['cid']=$cid[1][0];             //企业编号
	    $send['sid']=$sid[1][0];             //员工编号
		$send['msgid']=$msgid[1][0];         //发送编号
		$send['total']=$total[1][0];         //计费条数
		$send['price']=$price[1][0];         //短信单价
		$send['remain']=$remain[1][0];       //余额
		$send_arr[]=$send;                   //数组send_arr 存储了发送返回后的相关信息
		}
		echo "发送成功,状态为".$res[1][0];   //发送成功返回的值
		
	}
	else  //发送失败的返回值
	{
	     switch(trim($res[1][0])){
			case  0: echo "帐户格式不正确(正确的格式为:员工编号@企业编号)";break; 
			case  -1: echo "服务器拒绝(速度过快、限时或绑定IP不对等)如遇速度过快可延时再发";break;
			case  -2: echo " 密钥不正确";break;
			case  -3: echo "密钥已锁定";break;
			case  -4: echo "参数不正确(内容和号码不能为空，手机号码数过多，发送时间错误等)";break;
			case  -5: echo "无此帐户";break;
			case  -6: echo "帐户已锁定或已过期";break;
			case  -7:echo "帐户未开启接口发送";break;
			case  -8: echo "不可使用该通道组";break;
			case  -9: echo "帐户余额不足";break;
			case  -10: echo "内部错误";break;
			case  -11: echo "扣费失败";break;
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