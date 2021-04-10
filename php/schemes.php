<?php
	require_once('config.php');
	//require_once('config_s.php');
	date_default_timezone_set('Asia/Kolkata');
	$currentDateTime = date("Y-m-d H:i:s");
	$st_date = date("Y-m-01");
	$end_date = date("Y-m-t");
	if($con)
	{
		if(isset($_GET['getSchemes']))
		{
			$query = "select id,file_name,file_url from retailer_schemes where DATE(created) between '$st_date' and '$end_date'";
			$exe   = mysqli_query($con,$query);
			if($exe)
			{
				$cnt = mysqli_num_rows($exe);
				$chemesArr = array();
				if($cnt>0)
				{
					while($res = mysqli_fetch_array($exe))
					{
						array_push($chemesArr,array('status'=>'success','id'=>$res['id'],'file_name'=>$res['file_name'],'file_url'=>$res['file_url']));
					}
					echo '{"Result":'.json_encode($chemesArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					array_push($chemesArr,array('status'=>'norows'));
					echo '{"Result":'.json_encode($chemesArr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
		if(isset($_GET['getCmpyName']))
		{
			if(isset($_GET['version']))
			{
				$version = $_GET['version'];
				$ssql = "";
				if(isset($_GET['app_userId']))
				{
					$app_userId = $_GET['app_userId'];
					$ssql = "select version from app_users where id='$app_userId' and Active='1'";
				}
				else
					$ssql = "select version from app_users where Active='1' limit 1";
				$ssql_ex = mysqli_query($con,$ssql);
				$vrsn = '';
				if($ssql_ex)
				{
					if(mysqli_num_rows($ssql_ex)>0)
					{	
						$vrsn = mysqli_fetch_array($ssql_ex);	
						$vrsn = $vrsn['version'];
					}
				}
				if($vrsn==$version)
				{
					$getCmpnyName = "select name,logo_url from company_profile";
					$getCmpnyName_ex = mysqli_query($con,$getCmpnyName);
					if(mysqli_num_rows($getCmpnyName_ex)==1)
					{
						$res = mysqli_fetch_array($getCmpnyName_ex);
						$cmpnyNameArr = array('status'=>'success','name'=>$res['name'],'logo_url'=>$res['logo_url']);
						echo '{"Result":'.json_encode($cmpnyNameArr,JSON_UNESCAPED_SLASHES).'}';
					}
					else
					{
						$cmpnyNameArr = array('status'=>'norows');
						echo '{"Result":'.json_encode($cmpnyNameArr,JSON_UNESCAPED_SLASHES).'}';
					}
				}
				else
				{
					$cmpnyNameArr = array('status'=>'expired','current_version'=>$vrsn);
					echo '{"Result":'.json_encode($cmpnyNameArr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				$cmpnyNameArr = array('status'=>'expired');
				echo '{"Result":'.json_encode($cmpnyNameArr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		if(isset($_GET['tracking']))
		{
			$app_userId = $_GET['app_userId'];
			$lat = $_GET['lat'];
			$long = $_GET['long'];
			$sql = "insert into gps_tracking (user_id,latitude,longitude,created) values ('$app_userId','$lat','$long','$currentDateTime')";
			$sql_ex = mysqli_query($con,$sql);
			if($sql_ex)
			{
				$trackingArr = array('status'=>'success');
				echo '{"Result":'.json_encode($trackingArr,JSON_UNESCAPED_SLASHES).'}';
			}
			else
			{
				$trackingArr = array('status'=>'failed');
				echo '{"Result":'.json_encode($trackingArr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		if(isset($_POST['putOtpMsgRefId']))
		{
			$msgId = $_POST['msgId'];
			$message = $_POST['message'];
			$senderId = $_POST['userId'];
			$msgType = $_POST['msgType'];
			$ref_no = 'T000001';
			$shpId = $_POST['shpId'];
			if($_POST['req']=='pymntOTP')
			{
				$setQuery = "insert into otp_details (message_id,message,reference_number,receiver_shopId,message_type,sender_id,purpose,created) 
				values ('$msgId','$message','$ref_no','$shpId','$msgType','$senderId','Payment','$currentDateTime')";
			}
			if($_POST['req']=='pymntPopupOTP' || $_POST['req']=='orderPopupOTP')
			{
				$purpose = '';
				if($_POST['req']=='pymntPopupOTP')
					$purpose = 'Payment Approval';
				if($_POST['req']=='orderPopupOTP')
					$purpose = 'Order Approval';
				$recvrMbl = $_POST['recvrMbl'];
				$getRcvrId = "select id from app_users where user_name='$recvrMbl' and Active='1'";
				$getRcvrId_ex = mysqli_query($con,$getRcvrId);
				if($getRcvrId_ex)
				{
					if(mysqli_num_rows($getRcvrId_ex)>0)
					{
						$res = mysqli_fetch_array($getRcvrId_ex);
						$recvrId = $res['id'];
					}
				}
				$setQuery = "insert into otp_details (message_id,message,reference_number,receiver_userId,message_type,sender_id,purpose,created) 
				values ('$msgId','$message','$ref_no','$recvrId','$msgType','$senderId','$purpose','$currentDateTime')";
			}
			$setExe = mysqli_query($con,$setQuery);
		}
	}
?>