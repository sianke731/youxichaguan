<?php
/*--------------------------------
�����Ȩ���Ϻ�������Ϣ�����޹�˾
�������ߣ�4008885262
����  QQ��2355373292
�޸�ʱ�䣺2013-08-18
�����ܣ�������PHP�ӿ�ʾ�� ͨ���ӿڽ��� ȡ�������
˵��:		http://dxhttp.c123.cn/up/?uid=�û��˺�&pwd=MD5λ32����&npwd=MD5λ32����
״̬:
	100			�޸ĳɹ�
    101			��֤ʧ��
    107			Ƶ�ʹ���
    108			���ܲ�Ϊ��
    109			�ʺ����Ѷ���
    114			�ʺű���
    115			����ʧ��
    116			��ֹ�ӿڷ���
    117		    ��IP����ȷ

--------------------------------*/
$url='http://dxhttp.c123.cn/up/';
$uid = '500100860001';		//�û��˺�
$pwd = 'adc';		//����
$npwd='abcabc123';  //���û�������
$res = up($url,$uid,$pwd,$npwd);  
function up($url,$uid,$pwd,$npwd)
{
	$data = array
		(
		'uid'=>$uid,					//�û��˺�
		'pwd'=>strtolower(md5($pwd)),   //MD5λ32����
		'npwd'=>strtolower(md5($npwd)) 
		);
	$re= postSMS($url,$data);			//POST��ʽ�ύ
	if(trim($re) == '100' )
	{
		echo  "�޸�����ɹ�";
	}
	else 
	{
		echo  "�޸�����ʧ��! ״̬:".$re;
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