<?php
/*--------------------------------
�����Ȩ���Ϻ�������Ϣ�����޹�˾
�������ߣ�4008885262
����  QQ��2355373292
�޸�ʱ�䣺2013-08-18
�����ܣ�������PHP�ӿ�ʾ�� ͨ���ӿڽ���ȡ״̬��
�޸�����:	2013-08-12
˵��:	http://smsapi.c123.cn/OpenPlatform/OpenApi?action=getSendState&ac=�û��˺�&authkey=��֤��Կ
״̬:
	1 �����ɹ�
	0 �ʻ���ʽ����ȷ(��ȷ�ĸ�ʽΪ:Ա�����@��ҵ���)
	-1 �������ܾ�(�ٶȹ��졢��ʱ���IP���Ե�)�����ٶȹ������ʱ�ٷ�
	-2 ��Կ����ȷ
	-3 ��Կ������
*/
$url='http://smsapi.c123.cn/DataPlatform/DataApi';
$ac='1001@500100080001';	//�û��˺�
$authkey = '9FFC0C71280583642CC8ACCF73788888';		//��֤��Կ
getStat($url,$ac,$authkey);
function getStat($url,$ac,$authkey)
{
	$data = array
		(
		'action'=>'getSendState',  //ȡ״̬
		'ac'=>$ac,					  //�û��˺�
		'authkey'=>$authkey,	     //��֤��Կ
		);
	$re= postSMS($url,$data);			//POST��ʽ�ύ
    preg_match_all('/result="(.*?)"/',$re,$res);
	if(trim($res[1][0]) == '1' )  //�ɹ�����  ˳���� �����ͱ�ţ����ͺ��룬���ͱ�ţ�����״̬��
	{
	    preg_match_all('/\<Item\s+(.*?)\s+\/\>/',$re,$item);
	    for($i=0;$i<count($item[1]);$i++){
		preg_match_all('/msgid="(.*?)"/',$item[1][$i],$msgid);
		preg_match_all('/mobile="(.*?)"/',$item[1][$i],$mobile);
		preg_match_all('/result="(.*?)"/',$item[1][$i],$result);
		preg_match_all('/return="(.*?)"/',$item[1][$i],$return);
		if($return[1][0]=='DELIVRD'){
		  $stat['msgid']=trim($msgid[1][0]);  //���ͱ��
		  $stat['mobile']=trim($mobile[1][0]); //���ͺ���
		  $stat['result']=trim($result[1][0]);    //����״̬
		  $stat['return']=trim($return[1][0]);  //��Ӫ�̷��� 
		  $stat_arr[]=$stat;   //$stat_arr����洢����״̬�������Ϣ
		  }
		}
	 if(is_array($stat_arr))
	 {
	    
	     echo "ȡ״̬�ɹ�";
	 }
		
	}
	else  //����ʧ�ܵķ���ֵ
	{
	     switch(trim($res[1][0])){
			case  0: echo "�ʻ���ʽ����ȷ(��ȷ�ĸ�ʽΪ:Ա�����@��ҵ���)";break; 
			case  -1: echo "�������ܾ�(�ٶȹ��졢��ʱ���IP���Ե�)�����ٶȹ������ʱ�ٷ�";break;
			case  -2: echo " ��Կ����ȷ";break;
			case  -3: echo "��Կ������";break;
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