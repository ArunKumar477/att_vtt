<?php
	//require_once('config_s.php');
	require_once('config.php');
	date_default_timezone_set('Asia/Kolkata');
	$app_user = $_GET['app_user'];
	$app_pass = $_GET['app_pass'];
	$todayDate = date("Y-m-d");
	$currentTime = date("H:i:s");
	if($con)
	{
		if(isset($_GET['version']))
		{
			$version = $_GET['version'];
			$query = "select id,user_name,fos_name,version,un_fos,Login_date,Login_time,app_admin from app_users where user_name='$app_user' and user_password='$app_pass' and Active='1'";
			$exe = mysqli_query($con,$query);
			if($exe)
			{
				$cnt = mysqli_num_rows($exe);
				if($cnt==1)
				{
					$lgnId = mysqli_fetch_array($exe);
					if($lgnId['version']==$version)
					{
						$lgId = $lgnId['id'];
						$sql = "update app_users set Login_date='$todayDate',Login_time='$currentTime' where id=$lgId and Active=1";
						$ex = mysqli_query($con,$sql);	
						$ssql = "select id from app_users where app_admin='1' and Active='1' and un_fos='0' limit 1";
						$eex  = mysqli_query($con,$ssql);
						$admnId = 'NoAmin';
						if($eex)
						{
							if(mysqli_num_rows($eex)==1)
								$admnId = mysqli_fetch_array($eex);
						} 
						$arr = array('status'=>'success','lgnId'=>$lgnId['id'],'fos_name'=>$lgnId['fos_name'],'un_fos'=>$lgnId['un_fos'],
										'loginDate'=>$lgnId['Login_date'],'loginTime'=>$lgnId['Login_time'],'app_admin'=>$lgnId['app_admin'],'AdminId'=>$admnId['id']);
						echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
					}
					else
					{
						$arr = array('status'=>'expired');
						echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
					}
				}
				else
				{
					$arr = array('status'=>'failed');
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				$arr = array('status'=>'error');
				echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		if(!isset($_GET['version']))
		{
			$arr = array('status'=>'expired');
			echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
		}
	}
?>