<?php
/*--------------------------------
�����Ȩ���Ϻ�������Ϣ�����޹�˾
�������ߣ�4008885262
����  QQ��2355373292
�޸�ʱ�䣺2013-08-18
�����ܣ�������PHP�ӿ�ʾ�� ͨ���ӿڽ��е�����Ⱥ����
˵��:		http://dxhttp.c123.cn/tx/?uid=�û��˺�&pwd=MD5λ32����&mobile=����&content=����
״̬:
	100 ���ͳɹ�
	101 ��֤ʧ��
	102 ���Ų���
	103 ����ʧ��
	104 �Ƿ��ַ�
	105 ���ݹ���
	106 �������
	107 Ƶ�ʹ���
	108 �������ݿ�
	109 �˺Ŷ���
	110 ��ֹƵ����������
	111 ϵͳ�ݶ�����
	112 ���벻��ȷ
	113 ��ʱʱ���ʽ����
	114 �˺ű�����10���Ӻ��¼
	115 ����ʧ��
	116 ��ֹ�ӿڷ���
	117 ��IP����ȷ
	120 ϵͳ����
--------------------------------*/
$url='http://dxhttp.c123.cn/tx/';
$uid = '500860720001';		//�û��˺�
$pwd = 'youxi12345';		//����
$mobile	 = '15208205236,15196622809,15982359408,18683930709,18628306432,13438318050';	//����,��������ö��Ÿ���
$content = '�û����ã���ӭ���봴�����ӿڲ��ԣ�����Ϸ��ݡ�';		//����
$time=''; //����ʱ��
$mid=''; //��ѡ������û��˺��Ƿ�֧����չ
//��ʱ����
$res = sendSMS($url,$uid,$pwd,$mobile,$content);
function sendSMS($url,$uid,$pwd,$mobile,$content,$time='',$mid='')
{
	$data = array
		(
		'uid'=>$uid,					//�û��˺�
		'pwd'=>strtolower(md5($pwd)),	//MD5λ32����
		'mobile'=>$mobile,				//����
		'content'=>iconv('gbk','utf-8',$content),		    //���ҳ����gbk���룬��ת��utf-8���룬�����ҳ����utf-8���룬����Ҫת��
		'time'=>$time,		//��ʱ����
		'mid'=>$mid						//����չ��
		);
	$re= postSMS($url,$data);			//POST��ʽ�ύ
	if(trim($re) == '100' )
	{
		echo  "���ͳɹ�!";
	}
	else 
	{
		echo  "����ʧ��! ״̬��".$re;
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