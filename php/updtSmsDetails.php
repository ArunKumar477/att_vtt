<?php 
	require_once('config.php');
	//require_once('config_s.php');
	$user_sms = '';$pass_sms = '';$sid_sms = '';$fl_sms = '';$gwid_sms = '';$baseUrl_sms = '';$referenceUrl_sms = '';
	if($con)
	{
		$getSmsUsrDetails = mysqli_query($con,"select * from sms_users where Active=1 and purpose='normal' and type='smsindia'");
		if($getSmsUsrDetails)
		{
			if(mysqli_num_rows($getSmsUsrDetails)>0)
			{
				$res = mysqli_fetch_array($getSmsUsrDetails);
				$getSmsUsrDetails1 = mysqli_query($con,"select reference_url from sms_users where Active=1 and purpose='reference' and type='smsindia'");
				$res1 = mysqli_fetch_array($getSmsUsrDetails1);
				$user_sms = $res['user'];$pass_sms = $res['password'];$sid_sms = $res['sid'];$fl_sms = $res['fl'];$gwid_sms = $res['gwid'];$baseUrl_sms = $res['base_url'];
				$referenceUrl_sms = $res1['reference_url'];
			}
		}
	}
	function PostRequest($url, $referer, $_data)
	{
		// convert variables array to string:
		$data = array();
		while(list($n,$v) = each($_data)){
		$data[] = "$n=$v";
		}
		$data = implode('&', $data);
		// format --> test1=a&test2=b etc.
		// parse the given URL
		
		$url = parse_url($url);
		if ($url['scheme'] != 'http') {
		die('Only HTTP request are supported !');
		}
		// extract host and path:
		$host = $url['host'];
		$path = $url['path'];
		// open a socket connection on port 80
		$fp = fsockopen($host, 80);
		// send the request headers:
		
		fputs($fp, "POST $path HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Referer: $referer\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ". strlen($data) ."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data);
		$result = '';
		while(!feof($fp)) {
		// receive the results of the request
		$result .= fgets($fp, 128);
		}
		// close the socket connection:
		fclose($fp);
		// split the result header from the content
		$result = explode("\r\n\r\n", $result, 2);
		$header = isset($result[0]) ? $result[0] : '';
		$content = isset($result[1]) ? $result[1] : '';
		// return as array:
		return array($header, $content);
	}
	
	if($con)
	{
		$query = "select sent_time,message_id,message_type from otp_details where message_id<>'' and Date(created) = CURDATE() and message_type='TRANSACTIONAL' and (response_code='' or response_code='Submitted' or response_code='AWAITED-DLR' or response_code is null)";
		$exe   = mysqli_query($con,$query);
		if($exe)
		{
			if(mysqli_num_rows($exe)>0)
			{
				while($res = mysqli_fetch_array($exe))
				{
					$message_type = $res['message_type'];
					$msg_id = $res['message_id'];
					//echo $message_type.','.$msg_id;
					if($message_type=='TRANSACTIONAL' && $msg_id!='')
					{
						$data = array("user"=>$user_sms,"password"=>$pass_sms,"messageid"=>$msg_id);
						list($header, $content) = PostRequest("http://cloud.smsindiahub.in/vendorsms/checkdelivery.aspx",$referenceUrl_sms, $data);
						//$res = str_replace("#","",$content);
						$resp = json_decode($content, true);
						$Status = $resp['Status'];
						if($Status==null || $Status=='' || $Status=='null')
							$Status = null;	
						if($resp['SubmitDate']!=null && $resp['SubmitDate']!='' && $resp['SubmitDate']!='null')
						{
							$SubmitDate = $resp['SubmitDate'];
								$splitVal= explode(" ",$SubmitDate);
								$dateVal = $splitVal[0];
								$timeVal = $splitVal[1].' '.$splitVal[2];
								$dt = date_create($dateVal);
								$dateVal_final = date_format($dt,"Y-m-d");
								$timeVal_final = date("H:i:s", strtotime($timeVal));
								$dateTime_final = $dateVal_final.' '.$timeVal_final;
						}
						else
							$dateTime_final = '0000-00-00 00:00:00';
						if($resp['DoneDate']!=null && $resp['DoneDate']!='' && $resp['DoneDate']!='null')
						{
							$DoneDate = $resp['DoneDate'];
								$splitVal1= explode(" ",$DoneDate);
								$dateVal1 = $splitVal1[0];
								$timeVal1 = $splitVal1[1].' '.$splitVal1[2];
								$dt1 = date_create($dateVal1);
								$dateVal_final1 = date_format($dt1,"Y-m-d");
								$timeVal_final1 = date("H:i:s", strtotime($timeVal1));
								$dateTime_final1 = $dateVal_final1.' '.$timeVal_final1;
						}
						else
							$dateTime_final1 = '0000-00-00 00:00:00';
						//echo $Status.','.$dateTime_final.','.$dateTime_final1.'<br>';
						$query1 = "update otp_details set sent_time2='$dateTime_final',delivery_time2='$dateTime_final1',response_code2='$Status' where message_id='$msg_id'";
						$exe1   = mysqli_query($con,$query1);
					}
					if($message_type=='OTP')
					{
						$msg_id = $res['message_id'];
						$newObj  = new SmsDetails;
						$smsDetails = $newObj->updtSmsDetails($msg_id);
						$resultArr = json_decode($smsDetails,true);
						
						if($resultArr['status']=='OK')
						{
							$i = 1;
							foreach($resultArr as $k)
							{
								if($i==2)
								{
									if($k)
									{
										$mobile = $k[0]['mobile'];
										$status = $k[0]['status'];
										$senttime = $k[0]['senttime'];
										$dlrtime = $k[0]['dlrtime'];
										$status = $k[0]['status'];
										$query1 = "update otp_details set sent_time='$senttime',delivery_time='$dlrtime',receiver_number='$mobile',response_code='$status' 
										where message_id='$msg_id'";
										$exe1   = mysqli_query($con,$query1);
									}
								}
								$i++;
							}
						}
					}
				}
			}
		} 
	}
	class SmsDetails
	{
		public static function sendme1($smsurl) {
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$smsurl);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_HEADER,false);
			$result = curl_exec($curl);
			curl_close($curl);
			return $result;
		}
	
		public static function updtSmsDetails($msg_id)	
		{
			$url = 'http://hpsms.dial4sms.com/api/v3/?method=sms.status&api_key=Ad3f03c60a229e0041b1693de9da38f6d&format=json&id='.$msg_id.'&numberinfo=1';
			$result = self::sendme1($url);
			return $result;
		}
	}
?>