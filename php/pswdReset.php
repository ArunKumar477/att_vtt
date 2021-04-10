<?php
	//require_once('config_s.php');
	require_once('config.php');
	if($con)
	{
		if(isset($_POST['existPswdRstTxt']))
		{
			$existPswdRstTxt = $_POST['existPswdRstTxt'];
			$newPswdRstTxt = $_POST['newPswdRstTxt'];
			$userIdPswdRst = $_POST['userIdPswdRst'];
			$userMblPswdRst = $_POST['userMblPswdRst'];
			$sql = "select id from app_users where id='$userIdPswdRst' and user_name='$userMblPswdRst' and user_password='$existPswdRstTxt' and Active=1";
			$ex = mysqli_query($con,$sql);
			$cnt = mysqli_num_rows($ex);
			if($cnt==1)
			{
				$query = "update app_users set user_password='$newPswdRstTxt' where id='$userIdPswdRst' and user_name='$userMblPswdRst' and user_password='$existPswdRstTxt' and Active=1";
				$exe = mysqli_query($con,$query);
				if($exe)
				{
					$arr = array('status'=>'success');
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$arr = array('status'=>'error');
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				$arr = array('status'=>'failed');
				echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}	
		}
		if(isset($_GET['app_user']))
		{
			$app_user = $_GET['app_user'];	
			$sql = "select id from app_users where user_name='$app_user' and Active=1";
			$exe = mysqli_query($con,$sql);
			if($exe)
			{
				$cnt = mysqli_num_rows($exe);
				if($cnt=='1')
				{
					$arr = array('status'=>'success');
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$arr = array('status'=>'error');
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				$arr = array('status'=>'failed');
				echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		
		/* Get User Profile Info */
		if(isset($_GET['getUserPrflInfo']))
		{
			$app_userId = $_GET['app_userId'];	
			$exe = mysqli_query($con,"select fos_name,user_name,email,Latitude,Longitude,Address,Edit_status from app_users where id='$app_userId' and Active='1'");
			if($exe)
			{
				if(mysqli_num_rows($exe)>0)
				{
					$res = mysqli_fetch_array($exe);
					$arr = array('status'=>'success','fos_name'=>$res['fos_name'],'user_name'=>$res['user_name'],'email'=>$res['email'],'Latitude'=>$res['Latitude']
					,'Longitude'=>$res['Longitude'],'Address'=>$res['Address'],'Edit_status'=>$res['Edit_status']);
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$arr = array('status'=>'norows');
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				$arr = array('status'=>'failed');
				echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
	
		/* Get User Profile Info */
		if(isset($_POST['setUserPrflInfo']))
		{
			$app_userId = $_POST['app_userId'];	
			$lat_info = $_POST['lat_info'];
			$lng_info = $_POST['lng_info'];
			$prflAddrsTxt = $_POST['prflAddrsTxt'];
			$exe = mysqli_query($con,"update app_users set Latitude='$lat_info',Longitude='$lng_info',Address='$prflAddrsTxt',Edit_status='0' where id='$app_userId' and Active='1'");
			if($exe)
			{
				$arr = array('status'=>'success');
				echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
			else
			{
				$arr = array('status'=>'failed');
				echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
}//$con
?>