<?php
/*--------------------------------
�����Ȩ���Ϻ�������Ϣ�����޹�˾
�������ߣ�4008885262
����  QQ��2355373292
�޸�ʱ�䣺2013-08-18
�����ܣ�������PHP�ӿ�ʾ�� ͨ���ӿڽ��� ȡ�������
˵��:		http://dxhttp.c123.cn/mm/?uid=�û��˺�&pwd=MD5λ32����
״̬:
	100 ���ͳɹ�
	101 ��֤ʧ��
--------------------------------*/
$url='http://dxhttp.c123.cn/mm/';
$uid = '500100860001';		//�û��˺�
$pwd = 'abcabc123';		//����
$res = balance($url,$uid,$pwd);  
function balance($url,$uid,$pwd)
{
	$data = array
		(
		'uid'=>$uid,					//�û��˺�
		'pwd'=>strtolower(md5($pwd))   //MD5λ32����
		);
	$re= postSMS($url,$data);			//POST��ʽ�ύ
	$balance=explode("||",trim($re));
	if(trim($balance[0]) == '100' )
	{
		echo  "�������Ϊ:".$balance[1];
	}
	else 
	{
		echo  "�������ʧ��! ״̬:".$balance[0];
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
		$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//תURL��׼��
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