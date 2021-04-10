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

	$sms_for = 'OTP';
	
	/* Get SMS API Info start */
	$user_sms = '';$pass_sms = '';$sid_sms = '';$fl_sms = '';$gwid_sms = '';$baseUrl_sms = '';$referenceUrl_sms = '';$token = '';
	if($con)
	{
		$getSmsUsrDetails = mysqli_query($con,"select * from sms_users where Active=1 and purpose='normal' and type='dial4sms'");
		if($getSmsUsrDetails)
		{
			if(mysqli_num_rows($getSmsUsrDetails)>0)
			{
				$res = mysqli_fetch_array($getSmsUsrDetails);
				$user_sms = $res['user'];$pass_sms = $res['password'];$sid_sms = $res['sid'];$fl_sms = $res['fl'];$gwid_sms = $res['gwid'];$baseUrl_sms = $res['base_url'];$token = $res['token'];
			}
		}
	}
	/* SMS API end */
		$getotpDetails = mysqli_query($con,"SELECT * FROM `app_users` WHERE `Active` = 1 AND `un_fos` = 2 AND `rights` = 0 AND app_admin = 0 AND user_name = '".$number."'");
		if($getotpDetails)
		{   
		    //fos not allowed for this service
			if(mysqli_num_rows($getotpDetails)>0)
			{ 
			    $otpArr = array('ErrorCode'=>'018','message'=>'Unable to Send OTP For this number');
				echo '{"result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
                exit;
			} else {
				$Spli_uId_ShopId = (explode(",",$otpDetails));
				$uid = $Spli_uId_ShopId[0];
				$sid = $Spli_uId_ShopId[1];
			$check_duplicate = mysqli_query($con,"SELECT * FROM `otp_track` WHERE otp_mobile = '".$number."'");
			if($check_duplicate)
			{
				if(mysqli_num_rows($check_duplicate)>0)
				{
                    $getshopDetails = mysqli_query($con,"SELECT * FROM `otp_track` WHERE otp_mobile = '".$number."' AND shop_id = '".$sid."'");
                    if($getshopDetails)
                    {   
                        //fos not allowed for this service
                        if(mysqli_num_rows($getshopDetails)>0)
                        {  
                            $update_otp_query = mysqli_query($con,"update otp_track set created_at= '".$currentDateTime."' WHERE otp_mobile = '".$number."'");    
                        } else {
                            $otpArr = array('ErrorCode'=>'017','message'=>'This number is alredy exist in another Shop');
				            echo '{"result":'.json_encode($otpArr,JSON_UNESCAPED_SLASHES).'}';
                            exit;
                        }
                    }
				} else {
					$otp_query = "insert into otp_track (otp_mobile,shop_id,fos_id,created_at) values ('".$number."','".$sid."','".$uid."','".$currentDateTime."')";
					$otp_exe = mysqli_query($con, $otp_query);
				}
			}
				/* SMS API end */
				if($pwd!='smsonly' && $pwd!='orderCnfrm' && $pwd!='deliveryCnfrm' && $Pymnt_Amnt!='popupOtpMsg' && $Pymnt_Amnt!='popupOtpMsg_order')
				{
					$sms_for = 'OTP';
					$sender = $sid_sms;
					$message = "Dear Customer, Your One Time Password (OTP) is ".$pwd.". To the receipt of Rs. ".$Pymnt_Amnt." . Thanks.";
					$number = $number;
					$newObj  = new sendsms;
					$response1 = $newObj->sendmessage($sender,$message,$number,$sms_for,$baseUrl_sms,$token);
					if($response1)
					{
						echo $response1;
					}
				}				
			}
		}	
	if($pwd=='smsonly')
	{
		$sms_for = 'TRANSACTIONAL';
		$numberVal = explode("@",$number);
		$number = $numberVal[0];
		$refno = $numberVal[1];
		$cashCheque = $numberVal[2];
		
		$Pymnt_AmntVal = explode("@",$Pymnt_Amnt);
		$Pymnt_Amnt = $Pymnt_AmntVal[0];
		$fos_name = $Pymnt_AmntVal[1];

		$sender = $sid_sms;
		$message = "Dear Partner, ".$cashCheque." Payment of Rs. ".$Pymnt_Amnt." has been received by ".$fos_name." on ".$today." ".$todayTime." . Ref : ".$refno." Check your email for E-Receipt. Thank you for your support.";
	}
	if($pwd=='orderCnfrm')
	{
		$sms_for = 'TRANSACTIONAL';
		$sender = $sid_sms;
		$message = "Dear Partner, Your order has been received successfully. Order ID : ".$Pymnt_Amnt.". Thank you for your support.";
	}
	if($pwd=='deliveryCnfrm')
	{
		$sms_for = 'TRANSACTIONAL';
		$sender = $sid_sms;
		$message = "Dear Partner, Your order has been delivered successfully. Order ID : ".$Pymnt_Amnt.". Thank you for your support.";
	}
	
	if($sms_for == 'TRANSACTIONAL')
	{
		$newObj  = new sendsms;
		$response1 = $newObj->sendmessage($sender,$message,$number,$sms_for,$baseUrl_sms,$token);
		if($response1)
		{
			//echo $response1;
			if($response1!='Invalid Mobile Numbers')
			{
				$msgId = $response1;
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
				
				$query = "insert into otp_details (message_id,sent_time,message,receiver_number,receiver_shopId,sender_id,message_type,purpose,
				created) values ('$msgId','$currentDateTime','$message','$number','$shpId','$userId','TRANSACTIONAL','$purpose','$currentDateTime')";
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
		}

	}
?>
