<?php
	require_once('sendsms_script.php');
	require_once('config.php');
	//require_once('config_s.php');
	date_default_timezone_set('Asia/Kolkata');
	$currentDateTime = date("Y-m-d H:i:s");
	$number = $_GET['number'];
	$pwd   = $_GET['pwd'];
	$Pymnt_Amnt = $_GET['Pymnt_Amnt'];
	$otpDetails = $_GET['otpDetails'];
	$today = date("Y-m-d");
	$currTime = date("H:i:s");
	$time1 = strtotime($currTime);
	$time2 = strtotime('06:30:00');
	$finalTime = $time1+$time2;
	$todayTime = date("H:i:s", $finalTime);
	$todayTime = date("g:i a", strtotime($todayTime));
	
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
	//echo $user_sms.','.$pass_sms.','.$sid_sms.','.$fl_sms.','.$gwid_sms.','.$baseUrl_sms.','.$referenceUrl_sms;
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
	
	if($pwd!='smsonly' && $pwd!='orderCnfrm' && $pwd!='deliveryCnfrm' && $Pymnt_Amnt!='popupOtpMsg' && $Pymnt_Amnt!='popupOtpMsg_order')
	{
		$sender = $sid_sms;
		$message = "Dear Customer, Your One Time Password (OTP) is ".$pwd.". To the receipt of Rs. ".$Pymnt_Amnt." . Thanks.";
		$number = $number;
		$newObj  = new sendsms;
		$response1 = $newObj->sendmessage($sender,$message,$number);
		if($response1)
		{
			echo $response1;
		}
	}
	if($Pymnt_Amnt=='popupOtpMsg')
	{
		$sender = $sid_sms;
		$shpNameOtp = explode(",",$pwd);
		$otp = $shpNameOtp[0];
		$shpName = $shpNameOtp[1];
		$number = $number;
		$message = "Delayed Cheque payment approval OTP ".$otp." for ".$shpName.".";
		$newObj  = new sendsms;
		$response = $newObj->sendmessage($sender,$message,$number);
		if($response)
		{
			//echo '{"result":'.$response."}";
			$otpArr = array('ErrorCode'=>'000');
			echo '{"result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
			exit;
		}
	}
	if($Pymnt_Amnt=='popupOtpMsg_order')
	{
		$sender = $sid_sms;
		$shpNameOtp = explode(",",$pwd);
		$otp = $shpNameOtp[0];
		$shpName = $shpNameOtp[1];
		$message = "Order Approval OTP ".$otp." for ".$shpName.".";
		$number = $number;
		$newObj  = new sendsms;
		$response = $newObj->sendmessage($sender,$message,$number);
		if($response)
		{
			//echo '{"result":'.$response."}";
			$otpArr = array('ErrorCode'=>'000');
			echo '{"result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
			exit;
		}
	}
	if($pwd=='smsonly')
	{
		$numberVal = explode("@",$number);
		$number = $numberVal[0];
		$refno = $numberVal[1];
		$cashCheque = $numberVal[2];
		
		$Pymnt_AmntVal = explode("@",$Pymnt_Amnt);
		$Pymnt_Amnt = $Pymnt_AmntVal[0];
		$fos_name = $Pymnt_AmntVal[1];
		$data = array(
		'user' => $user_sms,
		'password' => $pass_sms,
		'msisdn' => $number,
		//'msisdn' => '9585878170',
		'sid' => $sid_sms,
		'msg' => "Dear Partner, ".$cashCheque." Payment of Rs. ".$Pymnt_Amnt." has been received by ".$fos_name." on ".$today." ".$todayTime." . Ref : ".$refno." Check your email for E-Receipt. Thank you for your support.",
		'fl' =>$fl_sms,
		'gwid'=>$gwid_sms,
		);
	}
	if($pwd=='orderCnfrm')
	{
		$data = array(
		'user' => $user_sms,
		'password' => $pass_sms,
		'msisdn' => $number,
		//'msisdn' => '9585878170',
		'sid' => $sid_sms,
		'msg' => "Dear Partner, Your order has been received successfully. Order ID : ".$Pymnt_Amnt.". Thank you for your support.",
		'fl' =>$fl_sms,
		'gwid'=>$gwid_sms,
		);
	}
	if($pwd=='deliveryCnfrm')
	{
		$data = array(
		'user' => $user_sms,
		'password' => $pass_sms,
		'msisdn' => $number,
		//'msisdn' => '9585878170',
		'sid' => $sid_sms,
		'msg' => "Dear Partner, Your order has been delivered successfully. Order ID : ".$Pymnt_Amnt.". Thank you for your support.",
		'fl' =>$fl_sms,
		'gwid'=>$gwid_sms,
		);
	}
	if($pwd=='smsonly' || $pwd=='orderCnfrm' || $pwd=='deliveryCnfrm')
	{
		list($header, $content) = PostRequest($baseUrl_sms,$referenceUrl_sms, $data);
		$response = json_decode($content,true);
		$i = 1;
		$splitVal = explode(",",$otpDetails);
		$userId = $splitVal[0];
		$shpId = $splitVal[1];
		$splitIds = explode("@",$otpDetails);
		$Ids = explode("!",$splitIds[1]);	
		
		$purpose = '';
		if($pwd=='smsonly')
			$purpose = 'Payment';
		if($pwd=='orderCnfrm')
			$purpose = 'Order';
		if($pwd=='deliveryCnfrm')
			$purpose = 'Delivery';		
				
		foreach($response as $res)
		{	
			if($i==4)
			{	
				$mobile = $res[0]['Number'];
				$msgId  = $res[0]['MessageParts'][0]['MsgId'];
				$msgTxt = $res[0]['MessageParts'][0]['Text'];
				//echo $mobile.','.$msgId.','.$msgTxt;
				$query = "insert into otp_details (message_id,sent_time,message,receiver_number,receiver_shopId,sender_id,message_type,purpose,created) values 
				('$msgId','$currentDateTime','$msgTxt','$mobile','$shpId','$userId','TRANSACTIONAL','$purpose','$currentDateTime')";
				$exe   = mysqli_query($con,$query);
				if(sizeof($Ids)!=0)
				{
					for($j=0;$j<sizeof($Ids);$j++)
					{
						$inserte_id = $Ids[$j];
						if($pwd=='smsonly')
							$updtMsgId = mysqli_query($con,"update invoice_payment set message_id='$msgId' where id='$inserte_id'");			
						if($pwd=='orderCnfrm')
							$updtMsgId = mysqli_query($con,"update billed_orders set message_id='$msgId' where id='$inserte_id'");			
					}
				}
			}
			$i++;
		}
		echo '{"result":'.$content."}";
	}
?>
