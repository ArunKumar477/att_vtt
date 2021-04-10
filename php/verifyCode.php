<?php

	//require_once('config_s.php');
	require_once('config.php');
	include('notify.php');
	date_default_timezone_set('Asia/Kolkata');
	$currentDateTime = date("Y-m-d H:i:s");
	if($con)
	{
		if(isset($_GET['otpPut']))
		{
			$app_user     = $_GET['app_user'];
			$app_userId     = $_GET['app_userId'];
			
			$sql = "select * from app_users where user_name='$app_user' and id='$app_userId' and Active=1";
			$ex1 = mysqli_query($con,$sql);
			$cont = mysqli_num_rows($ex1);
			if($cont==1)
			{
				$str1 = "0123456789";
				$str2 = "9876543210";
				//$str2 = "abcdefghijklmnopqrstuvwxyz";
				$str = str_shuffle($str1.$str2);
				$pwd = substr($str,3,4);
				$que = "update app_users set otp='$pwd' where user_name='$app_user' and id='$app_userId' and Active=1";
				$ex  = mysqli_query($con,$que);
				if($ex) 
				{
					$arr = array('status'=>'success','otp'=>$pwd);
					echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$arr = array('status'=>'error');
					echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				$arr = array('status'=>'norows');
				echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		
		if(isset($_GET['cnfrmCode']))
		{
			$app_user     = $_GET['app_user'];
			$app_userId     = $_GET['app_userId'];
			$cnfrmCode = $_GET['cnfrmCode'];
			$query = "select * from app_users where otp='$cnfrmCode' and user_name='$app_user' and id='$app_userId' and Active=1";
			$exe = mysqli_query($con,$query);
			$cnt = mysqli_num_rows($exe);
			if($exe)
			{
				if($cnt==1)
				{
					$res = array('status'=> 'success');
					echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$res= array('status'=> 'failed');
					echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
		
		/*if(isset($_GET['pymntPopupOtp']))
		{
			$app_userId     = $_GET['app_userId'];
			$sql = "select * from app_users where id='$app_userId'";
			$ex1 = mysqli_query($con,$sql);
			$cont = mysqli_num_rows($ex1);
			if($app_userId=='7' || $app_userId=='23' || $app_userId=='25')
				$Mbl = '9500027453';
			else if($app_userId=='9' || $app_userId=='10' || $app_userId=='20' || $app_userId=='3')
				$Mbl = '9500026659';
			else
				$Mbl = '9841055925';
			if($cont==1)
			{
				$str1 = "0123456789";
				$str2 = "9876543210";
				//$str2 = "abcdefghijklmnopqrstuvwxyz";
				$str = str_shuffle($str1.$str2);
				$pwd = substr($str,3,4);
				$que = "update app_users set popup_otp='$pwd' where id='$app_userId'";
				$ex  = mysqli_query($con,$que);
				if($ex) 
				{
					$arr = array('status'=>'success','popup_otp'=>$pwd,'otpMbl'=>$Mbl);
					echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$arr = array('status'=>'error');
					echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				$arr = array('status'=>'norows');
				echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
		}*/
		if(isset($_POST['pymntPopupOtp']))
		{
			$app_userId     = $_POST['app_userId'];
			$fos_name     = mysqli_real_escape_string($_POST['fos_name']);
			$currentShpName     = $_POST['currentShpName'];
			$shpId     = $_POST['shpId'];
			if(!isset($_POST['pymntOtpApprvl']))
			{
				$invPeriod     = $_POST['invPeriod'];
				$sql = mysqli_query($con,"insert into otp_details (message_id,sent_time,message,receiver_shopId,message_type,sender_id,response_code,purpose,created)
				values('$fos_name','$currentDateTime','$invPeriod','$shpId','OTP','$app_userId','Request','Order Approval','$currentDateTime')");	
				/* notification Block */
					// $user = explode($fos_name,"!!");
					// $to = 'dOZeXKOqAas:APA91bHi3UnlvxdqtrWjq7xxaYSKnE-TLXi2iFtF2WDEIAGh0C3NeTxIxzguXnsbjswd6JAvYJcS2H3yJTrErxJliiMJm1mPfSa0eWgEBh4-JDKR2VKO7yZ73U-PgkkK0_DFs0mGO3Sw';
					// $data = array(
					// 	'title' => 'Order Approval from' .$user[0].'.',
					// 	'body'  => 'Retailer - '.$user[1].'.'
					// );
					// $res = $cls->sendPushNotification($to, $data, $con);	
				/* notification end */	
			}
			else
			{
				$msg = $_POST['msg'];
				$sql = mysqli_query($con,"insert into otp_details (message_id,sent_time,message,receiver_shopId,message_type,sender_id,response_code,purpose,created)
				values('$fos_name','$currentDateTime','$msg','$shpId','OTP','$app_userId','Request','Payment Approval','$currentDateTime')");	
				/* notification Block */
					// $user = explode($fos_name,"!!");
					// $to = 'dOZeXKOqAas:APA91bHi3UnlvxdqtrWjq7xxaYSKnE-TLXi2iFtF2WDEIAGh0C3NeTxIxzguXnsbjswd6JAvYJcS2H3yJTrErxJliiMJm1mPfSa0eWgEBh4-JDKR2VKO7yZ73U-PgkkK0_DFs0mGO3Sw';
					// $data = array(
					// 	'title' => 'Payment Approval from' .$user[0].'.',
					// 	'body'  => 'Retailer - '.$user[1].'.'
					// );
					// $res = $cls->sendPushNotification($to, $data, $con);	
				/* notification end */		
			}

			if($sql) 
			{
				$ssql = mysqli_query($con,"SELECT sender_id, max(reference_number) as reference_number FROM `otp_details` where sender_id='$app_userId'");
				if($ssql)
				{
					if(mysqli_num_rows($ssql))
					{
						$refNo = mysqli_fetch_array($ssql);
						$reference_number = $refNo['reference_number'];
						$arr = array('status'=>'success','currentDateTime'=>$reference_number);
						echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
					}
				}
			}
			else
			{
				$arr = array('status'=>'error');
				echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		if(isset($_GET['getOTPResponse']))
		{
			$app_userId     = $_GET['app_userId'];
			$reference_number     = $_GET['currentDateTime'];
			$shpId     = $_GET['shpId'];
			$sql = mysqli_query($con,"select response_code from otp_details where sender_id='$app_userId' and receiver_shopId='$shpId' and 
			reference_number='$reference_number'");
			if($sql) 
			{
				if(mysqli_num_rows($sql)>0)
				{
					$response_code = mysqli_fetch_array($sql);
					$response_code = $response_code['response_code'];
					$arr = array('status'=>'success','response_code'=>$response_code);
					echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$arr = array('status'=>'norows');
					echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}	
			}
			else
			{
				$arr = array('status'=>'error');
				echo '{"result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		if(isset($_GET['cnfrmCodePopupOtp']))
		{
			$app_userId     = $_GET['app_userId'];
			$cnfrmOtpPopupCode = $_GET['cnfrmOtpPopupCode'];
			$query = "select * from app_users where popup_otp='$cnfrmOtpPopupCode' and id='$app_userId' and Active=1";
			$exe = mysqli_query($con,$query);
			$cnt = mysqli_num_rows($exe);
			if($exe)
			{
				if($cnt==1)
				{
					$res = array('status'=> 'success');
					echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$res= array('status'=> 'failed');
					echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
	}

?>