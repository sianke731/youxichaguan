<?php
/*--------------------------------
�����Ȩ���Ϻ�������Ϣ�����޹�˾
�������ߣ�4008885262
����  QQ��2355373292
�޸�ʱ�䣺2013-08-18
�����ܣ�������PHP�ӿ�ʾ�� ͨ���ӿڽ��е�����Ⱥ����
�ӿ�˵��: http://smsapi.c123.cn/OpenPlatform/OpenApi?action=sendOnce&ac=�û��˺�&authkey=��֤��Կ&cgid=ͨ������&c=��������&m=���ͺ���
״̬:
	1 �����ɹ�
	0 �ʻ���ʽ����ȷ(��ȷ�ĸ�ʽΪ:Ա�����@��ҵ���)
	-1 �������ܾ�(�ٶȹ��졢��ʱ���IP���Ե�)�����ٶȹ������ʱ�ٷ�
	-2 ��Կ����ȷ
	-3 ��Կ������
	-4 ��������ȷ(���ݺͺ��벻��Ϊ�գ��ֻ����������࣬����ʱ������)
	-5 �޴��ʻ�
	-6 �ʻ����������ѹ���
	-7 �ʻ�δ�����ӿڷ���
	-8 ����ʹ�ø�ͨ����
	-9 �ʻ�����
	-10 �ڲ�����
	-11 �۷�ʧ��

--------------------------------*/
$url='http://smsapi.c123.cn/OpenPlatform/OpenApi';           //�ӿڵ�ַ
$ac='1001@500860720001';		                             //�û��˺�
$authkey = 'A9526D1320E5FDB3F8E8E4E861546D53';		         //��֤��Կ
$cgid='11';                                                  //ͨ������
$c = '�û����ã���ӭ���봴�����ӿڲ��ԣ�����������';		 //����
$m= '18980899944';	                                         //����
$csid='3';                                                   //ǩ����� ,����Ϊ��ʱ��ʹ��ϵͳĬ�ϵı��
$t='';                                                       //����ʱ��,����Ϊ�ձ�ʾ��������,yyyyMMddHHmmss ��:20130721182038

sendSMS($url,$ac,$authkey,$cgid,$m,$c,$csid,$t);
                                                             //���ŷ��ͽӿ�
function sendSMS($url,$ac,$authkey,$cgid,$m,$c,$csid,$t)
{
	$data = array
		(
		'action'=>'sendOnce',                                //�������� ��������sendOnce���ŷ��ͣ�sendBatchһ��һ���ͣ�sendParam	��̬�������Žӿ�
		'ac'=>$ac,					                         //�û��˺�
		'authkey'=>$authkey,	                             //��֤��Կ
		'cgid'=>$cgid,                                       //ͨ������
		'm'=>$m,		                                     //����,��������ö��Ÿ���
		'c'=>iconv('gbk','utf-8',$c),		                 //���ҳ����gbk���룬��ת��utf-8���룬�����ҳ����utf-8���룬����Ҫת��
		'csid'=>$csid,                                       //ǩ����� ������Ϊ�գ�Ϊ��ʱʹ��ϵͳĬ�ϵ�ǩ�����
		't'=>$t                                              //��ʱ���ͣ�Ϊ��ʱ��ʾ��������
		);
	$re= postSMS($url,$data);			                     //POST��ʽ�ύ
    preg_match_all('/result="(.*?)"/',$re,$res);
	if(trim($res[1][0]) == '1' )  //���ͳɹ� ��������ҵ��ţ�Ա����ţ����ͱ�ţ��������������ۣ����
	{
	
	    preg_match_all('/\<Item\s+(.*?)\s+\/\>/',$re,$item);
		for($i=0;$i<count($item[1]);$i++){
	    preg_match_all('/cid="(.*?)"/',$item[1][$i],$cid);
	    preg_match_all('/sid="(.*?)"/',$item[1][$i],$sid);
		preg_match_all('/msgid="(.*?)"/',$item[1][$i],$msgid);
		preg_match_all('/total="(.*?)"/',$item[1][$i],$total);
		preg_match_all('/price="(.*?)"/',$item[1][$i],$price);
		preg_match_all('/remain="(.*?)"/',$item[1][$i],$remain);
		
		$send['cid']=$cid[1][0];             //��ҵ���
	    $send['sid']=$sid[1][0];             //Ա�����
		$send['msgid']=$msgid[1][0];         //���ͱ��
		$send['total']=$total[1][0];         //�Ʒ�����
		$send['price']=$price[1][0];         //���ŵ���
		$send['remain']=$remain[1][0];       //���
		$send_arr[]=$send;                   //����send_arr �洢�˷��ͷ��غ�������Ϣ
		}
		echo "���ͳɹ�,״̬Ϊ".$res[1][0];   //���ͳɹ����ص�ֵ
		
	}
	else  //����ʧ�ܵķ���ֵ
	{
	     switch(trim($res[1][0])){
			case  0: echo "�ʻ���ʽ����ȷ(��ȷ�ĸ�ʽΪ:Ա�����@��ҵ���)";break; 
			case  -1: echo "�������ܾ�(�ٶȹ��졢��ʱ���IP���Ե�)�����ٶȹ������ʱ�ٷ�";break;
			case  -2: echo " ��Կ����ȷ";break;
			case  -3: echo "��Կ������";break;
			case  -4: echo "��������ȷ(���ݺͺ��벻��Ϊ�գ��ֻ����������࣬����ʱ������)";break;
			case  -5: echo "�޴��ʻ�";break;
			case  -6: echo "�ʻ����������ѹ���";break;
			case  -7:echo "�ʻ�δ�����ӿڷ���";break;
			case  -8: echo "����ʹ�ø�ͨ����";break;
			case  -9: echo "�ʻ�����";break;
			case  -10: echo "�ڲ�����";break;
			case  -11: echo "�۷�ʧ��";break;
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