<?php
	$number = $_GET['number'];
	$pwd   = $_GET['pwd'];
	$Pymnt_Amnt = $_GET['Pymnt_Amnt'];
	$today = date("Y-m-d");
	$currTime = date("H:i:s");
	$time1 = strtotime($currTime);
	$time2 = strtotime('06:30:00');
	$finalTime = $time1+$time2;
	$todayTime = date("H:i:s", $finalTime);
	$todayTime = date("g:i a", strtotime($todayTime));
	
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
	
	if($pwd!='smsonly' && $pwd!='orderCnfrm' && $pwd!='deliveryCnfrm')
	{
		$data = array(
		'user' => "admin@vttech.in",
		'password' => "india321",
		'msisdn' => $number,
		'sid' => "VEETEE",
		//'msg' => "Dear Sir/Madam, Your One Time Password (OTP) is ".$pwd.". Please use this code to complete your contact verification. Thanks.",
		'msg' => "Dear Customer, Your One Time Password (OTP) is ".$pwd.". To the receipt of Rs. ".$Pymnt_Amnt." . Thanks.",
		'fl' =>"0",
		'gwid'=>"2",
		);
	}
	if($Pymnt_Amnt=='popupOtpMsg')
	{
		$shpNameOtp = explode(",",$pwd);
		$otp = $shpNameOtp[0];
		$shpName = $shpNameOtp[1];
		$data = array(
		'user' => "admin@vttech.in",
		'password' => "india321",
		'msisdn' => $number,
		'sid' => "VEETEE",
		//'msg' => "Dear Customer, Your One Time Password (OTP) is ".$otp.". To the receipt of Rs. ".$shpName." . Thanks.",
		'msg' => "Delayed Cheque payment approval OTP ".$otp." for ".$shpName.".",
		'fl' =>"0",
		'gwid'=>"2",
		);
	}
	if($Pymnt_Amnt=='popupOtpMsg_order')
	{
		$shpNameOtp = explode(",",$pwd);
		$otp = $shpNameOtp[0];
		$shpName = $shpNameOtp[1];
		$data = array(
		'user' => "admin@vttech.in",
		'password' => "india321",
		'msisdn' => $number,
		'sid' => "VEETEE",
		//'msg' => "Dear Customer, Your One Time Password (OTP) is ".$otp.". To the receipt of Rs. ".$shpName." . Thanks.",
		'msg' => "Order Approval OTP ".$otp." for ".$shpName.".",
		'fl' =>"0",
		'gwid'=>"2",
		);
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
		'user' => "venkat@vttech.in",
		'password' => "india123#",
		'msisdn' => $number,
		'sid' => "VEETEE",
		//'msg' => "Dear Partner, ".$cashCheque." Payment of ".$Pymnt_Amnt." has been received by ".$fos_name." on ".$today." . Ref : ".$refno." Check your email for E-Receipt. Thank you for your support.",
		'msg' => "Dear Partner, ".$cashCheque." Payment of Rs. ".$Pymnt_Amnt." has been received by ".$fos_name." on ".$today." ".$todayTime." . Ref : ".$refno." Check your email for E-Receipt. Thank you for your support.",
		'fl' =>"0",
		'gwid'=>"2",
		);
	}
	if($pwd=='orderCnfrm')
	{
		$data = array(
		'user' => "venkat@vttech.in",
		'password' => "india123#",
		'msisdn' => $number,
		'sid' => "VEETEE",
		'msg' => "Dear Partner, Your order has been received successfully. Order ID : ".$Pymnt_Amnt.". Thank you for your support.",
		'fl' =>"0",
		'gwid'=>"2",
		);
	}
	if($pwd=='deliveryCnfrm')
	{
		$data = array(
		'user' => "venkat@vttech.in",
		'password' => "india123#",
		'msisdn' => $number,
		'sid' => "VEETEE",
		'msg' => "Dear Partner, Your order has been delivered successfully. Order ID : ".$Pymnt_Amnt.". Thank you for your support.",
		'fl' =>"0",
		'gwid'=>"2",
		);
	}
	list($header, $content) = PostRequest(
	"http://cloud.smsindiahub.in/vendorsms/pushsms.aspx","http://tmc.vttech.in/php/smsScript.php", $data);
	
	 echo '{"result":'.$content."}";

?>
