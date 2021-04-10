<?php
	class sendsms
	{
		public static function sendme($smsurl) 
		{
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$smsurl);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_HEADER,false);
			$result = curl_exec($curl);
			curl_close($curl);
			$response = $result;
			return $response;
		}
	
		public static function sendmessage($sender,$message,$number,$sms_for,$baseUrl_sms,$token)
		{
			// $url = 'http://hpsms.dial4sms.com/api/web2sms.php';
			// $token = 'Ae8554404d937925084d1c28b14bfaea7';
			$url = $baseUrl_sms;
			$token = $token;
			//$credit = $credit;
			$message = urlencode($message);
			$numbe = $number;
			$sender = $sender;
	 
			$smsurl = $url.'?workingkey='.$token.'&sender='.$sender.'&to='.$number.'&message='.$message;	
			//echo $smsurl;
			$result = self::sendme($smsurl);
			if($sms_for=='OTP')
			{
				if($result=='Invalid Mobile Numbers')
				{	
					//echo '{"result":'.$response."}";
					$otpArr = array('ErrorCode'=>'013');
					echo '{"result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
					exit;
				}
				else
				{
					$msg = strstr($result," ID=");
					$msg = explode("=",$msg);
					$msgId = $msg[1];
					$otpArr = array('ErrorCode'=>'000','msgId'=>$msgId,'message'=>$message);
					echo '{"result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
					exit;
				}
			}
			else	
			{
				$msg = strstr($result," ID=");
				$msg = explode("=",$msg);
				$msgId = $msg[1];
				return $msgId;
			}
			//echo '{"result":'.$result."}";
		}
		public static function sendme1($smsurl) 
		{
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$smsurl);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_HEADER,false);
			$result = curl_exec($curl);
			curl_close($curl);
			return $result;
		}
	}
  ?>