<?php
require_once('config_s.php');
//$con = mysqli_connect('localhost','root','','veeteete_project_t');
$sender = 'VEETEE';
$message = 'Dear Customer, Your One Time Password (OTP) is 1114. To the receipt of Rs. 6500 . Thanks.';
$number = '9585878170';
$newObj  = new sendsms;
$newObj->sendmessage($sender,$message,$number);

	class sendsms
	{
		public static function sendme($smsurl) {
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$smsurl);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_HEADER,false);
			$result = curl_exec($curl);
			curl_close($curl);
			//return $result;
			//$resultArr = json_decode($result,true);
			echo $result;
			/*if($resultArr['status']=='OK')
			{
				$i = 1;
				foreach($resultArr as $k)
				{
					if($i==2)
						echo $k[0]['mobile'];
					$i++;
				}
			}*/
		}
	
		public static function sendmessage($sender,$message,$number)	{
			$url = 'http://hpsms.dial4sms.com/api/web2sms.php';
			//$token = 'Ae8554404d937925084d1c28b14bfaea7';
			$token = 'Ad3f03c60a229e0041b1693de9da38f6d';
			//$credit = $credit;
			$message = urlencode($message);
			$numbe = $number;
			$sender = $sender;
	 
			$smsurl = $url.'?workingkey='.$token.'&sender='.$sender.'&to='.$number.'&message='.$message;	
			$result = self::sendme($smsurl);
			//echo $result;
			return $result;
			/*$url = 'http://hpsms.dial4sms.com/api/v3/?method=sms.status&api_key=Ae8554404d937925084d1c28b14bfaea7&format=json&id=8603974605-1&numberinfo=1';
			$result = self::sendme($url);
			return $result;*/
		}
	}

?>