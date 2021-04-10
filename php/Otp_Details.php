<?php
	//require_once('config_s.php');
	require_once('config.php');
	date_default_timezone_set('Asia/Kolkata');
	$current_date = date("Y-m-d");
	$current_time = date("H:i:s");
	//echo $current_time.'<br>';
	$time = strtotime($current_time);
	$time = $time - (15 * 60);
	$Minus15m_time = date("H:i:s", $time);	
	//echo $Minus15m_time;
	if($con)
	{
		if(isset($_GET['getOtpInfo']))
		{
			$app_userId = $_GET['app_userId'];
			$control = '';
			$query = mysqli_query($con,"select * from users_approval where admin_id='$app_userId' limit 1");
			if($query)
			{
				if(mysqli_num_rows($query)>0)
				{
					$rs = mysqli_fetch_array($query);
					$control = $rs['control'];
				}
			}
			$sql = '';
			if($control=='1')
			{
				$sql = mysqli_query($con,"select message_id,message,reference_number,purpose,Time(created) as sent_time from otp_details 
				where Date(created)='$current_date' and message_type='OTP' and receiver_userId=0 and response_code='Request' and 
				Time(created) between '$Minus15m_time' and '$current_time' order by Time(created) desc");
			}
			else if($control=='2')
			{
				$sql = mysqli_query($con,"select a.message_id,a.message,a.reference_number,a.purpose,a.sent_time,a.sender_id from (select message_id,message,
				reference_number,purpose,Time(created) as sent_time,sender_id from otp_details where Date(created)='$current_date' and message_type='OTP' and 
				receiver_userId=0 and response_code='Request' and Time(created) between '$Minus15m_time' and '$current_time' order by Time(created) desc) a 
				left outer join users_approval u on a.sender_id=u.fos_id where u.admin_id='$app_userId' and control=2");
			}
			$otpArr = array();
			if($sql)
			{
				if(mysqli_num_rows($sql)>0)
				{
					while($res = mysqli_fetch_array($sql))
					{
						$message = $res['message'];
						/*$message = preg_replace('~[+]~',' ', $message);
						$message = str_replace('%28',' ',$message);
						$message = str_replace('%29',' ',$message);*/
						array_push($otpArr,array('status'=>'success','message_id'=>$res['message_id'],'purpose'=>$res['purpose'],'message'=>$res['message'],
						'sent_time'=>$res['sent_time'],'reference_number'=>$res['reference_number']));
					}
					echo '{"Result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					array_push($otpArr,array('status'=>'norows'));
					echo '{"Result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
				}
			}//$sql
			else
			{
				array_push($otpArr,array('status'=>'failed'));
				echo '{"Result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
			}
		}//if GE
		
		if(isset($_GET['setOtpApproval']))
		{
			$ref_no = $_GET['ref_no'];
			$req = $_GET['req'];
			$app_userId = $_GET['app_userId'];
			$sql = mysqli_query($con,"update otp_details set response_code='$req',response_user='$app_userId' where reference_number='$ref_no'");
			if($sql)
			{
				$otpArr = array('status'=>'success');
				echo '{"Result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
			}
			else
			{
				$otpArr = array('status'=>'failed');
				echo '{"Result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
			}	
		}
	}//$con
?>