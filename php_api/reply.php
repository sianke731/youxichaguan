<?php
/*--------------------------------
�����Ȩ���Ϻ�������Ϣ�����޹�˾
�������ߣ�4008885262
����  QQ��2355373292
�޸�ʱ�䣺2013-08-18
�����ܣ�������PHP�ӿ�ʾ�� ͨ���ӿڽ���ȡ�ظ���
�޸�����: 2013-08-12
�ӿ�˵��:  	http://smsapi.c123.cn/DataPlatform/DataApi?action=getReply&ac=�û��˺�&authkey=��֤��Կ
״̬:
	 1 �����ɹ�
	 0 �ʻ���ʽ����ȷ(��ȷ�ĸ�ʽΪ:Ա�����@��ҵ���)
	-1 �������ܾ�(�ٶȹ��졢��ʱ���IP���Ե�)�����ٶȹ������ʱ�ٷ�
	-2 ��Կ����ȷ
	-3 ��Կ������
--------------------------------*/
$url='http://smsapi.c123.cn/DataPlatform/DataApi';   
$ac='1001@500100080001';	                           	 //�û��˺�
$authkey = '9FFC0C71280583642CC8ACCF73788888';		     //��֤��Կ

 getReply($url,$ac,$authkey);
//һ��һ����
function getReply($url,$ac,$authkey)
{
	$data = array
		(
		'action'=>'getReply',                            //ȡ�ظ�
		'ac'=>$ac,					                     //�û��˺�
		'authkey'=>$authkey,	                         //��֤��Կ
		); 
	$re=postSMS($url,$data);		                     //POST��ʽ�ύ
	echo $re;
    preg_match_all('/result="(.*?)"/',$re,$res); 
	if(trim($res[1][0]) == '1' )                         //�ɹ�����  ˳���� �ظ�ʱ�� �ظ����� �ظ�����
	{
	   preg_match_all('/\<Item\s+(.*?)\s+\/\>/',$re,$item);
	    for($i=0;$i<count($item[1]);$i++){
		preg_match_all('/id="(.*?)"/',$item[1][$i],$id);
		preg_match_all('/time="(.*?)"/',$item[1][$i],$time);
		preg_match_all('/mobile="(.*?)"/',$item[1][$i],$mobile);
		preg_match_all('/content="(.*?)"/',$item[1][$i],$content);
		
		$rec['id']=trim($id[1][0]);                       //˳����
	    $rec['time']=trim($time[1][0]);                   //�ظ�ʱ��
	    $rec['mobile']=trim($mobile[1][0]);               //�ظ�����
		$rec['content']=trim($content[1][0]);             //�ظ�����
		$reply_arr[]=$rec;                                //$reply_arr����洢�˶��Żظ��������Ϣ
	  }	
	  echo "��ȡ�ظ��ɹ�";                                //���ͳɹ����ص�ֵ
	}
	else                                                  //����ʧ�ܵķ���ֵ
	{
	     switch(trim($res[1][0])){
			case   0: echo "�ʻ���ʽ����ȷ(��ȷ�ĸ�ʽΪ:Ա�����@��ҵ���)";break; 
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