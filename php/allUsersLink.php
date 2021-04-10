<?php
	require_once("config.php");
	//require_once("config_s.php");
	if($con)
	{
		if(isset($_POST['app_userId']))
		{
			$app_userId = $_POST['app_userId'];
			$query = "select id,fos_name,user_name,un_fos,app_admin from app_users where Active='1' and un_fos='2'";
			$exe   = mysqli_query($con,$query);
			if($exe)
			{
				$cnt = mysqli_num_rows($exe);
				$usersArr = array();
				if($cnt>0)
				{
					while($res = mysqli_fetch_array($exe))
					{
						array_push($usersArr,array('status'=>'success','id'=>$res['id'],'fos_name'=>$res['fos_name'],'user_name'=>$res['user_name'],
						'un_fos'=>$res['un_fos'],'app_admin'=>$res['app_admin']));
					}
					echo '{"Result":'.json_encode($usersArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					array_push($usersArr,array('status'=>'norows'));
					echo '{"Result":'.json_encode($usersArr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
	}
?>