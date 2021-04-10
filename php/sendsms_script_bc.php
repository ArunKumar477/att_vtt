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
			//echo '{"result":'.$result."}";
			/*if($response=='Invalid Mobile Numbers')
			{	
				//echo '{"result":'.$response."}";
				$otpArr = array('ErrorCode'=>'013');
				echo '{"result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
				exit;
			}
			else
			{
				$msg = strstr($response," ID=");
				$msg = explode("=",$msg);
				$msgId = $msg[1];
				$otpArr = array('ErrorCode'=>'000','msgId'=>$msgId);
				echo '{"result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
				exit;
				//$msg = 'Message GID=8555746328 ID=8555746328-1';
				$msg = strstr($response," ID=");
				$msg = explode("=",$msg);
				$msgId = $msg[1];
				$url = 'http://hpsms.dial4sms.com/api/v3/?method=sms.status&api_key=Ae8554404d937925084d1c28b14bfaea7&format=json&id='.$msgId.'&numberinfo=1';
				$result = self::sendme1($url);
				return $result;
				$resultArr = json_decode($result,true);
				if($resultArr['status']=='OK')
				{
					$i = 1;
					foreach($resultArr as $k)
					{
						if($i==2)
						{
							$response_id  = $k[0]['id']; 
							$response_Mbl = $k[0]['mobile'];
							$response_senttime = $k[0]['senttime'];
							$response_dlrtime = $k[0]['dlrtime'];
							$response_status = $k[0]['status'];
							return $response_status;
							//return $response_id.','.$response_Mbl.','.$response_senttime.','.$response_dlrtime.','.$response_status;
							$query = "insert into otp_details (message_id,sent_time,delivery_time,receiver_number,response_code) 
							values ('$response_id','$response_senttime','$response_dlrtime','$response_Mbl','$response_status')";
							$exe = mysqli_query($con,$query);
							if($exe)
							{
								echo 'success';
							}
							else
								echo 'failed';
						}
						$i++;
					}//foreach
				}//if
			}//else*/
		}
		public static function sendmessage($sender,$message,$number)	
		{
			$url = 'http://hpsms.dial4sms.com/api/web2sms.php';
			$token = 'Ad3f03c60a229e0041b1693de9da38f6d';
			//$credit = $credit;
			$message = urlencode($message);
			$numbe = $number;
			$sender = $sender;
	 
			$smsurl = $url.'?workingkey='.$token.'&sender='.$sender.'&to='.$number.'&message='.$message;	
			//echo $smsurl;
			$result = self::sendme($smsurl);
			//return $result;
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