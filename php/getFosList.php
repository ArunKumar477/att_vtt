<?php 
	require_once('config.php');
	//require_once('config_s.php');
	if($con)
	{
		if(isset($_GET['getFosName']))
		{
			$usersArr = array();
			$qry = mysqli_query($con,"select id,fos_name,user_name from app_users where Active='1' and un_fos!='0'");
			if($qry)
			{
				if(mysqli_num_rows($qry)>0)
				{
					while($r = mysqli_fetch_array($qry))
					{
						array_push($usersArr,array('status'=>'success','id'=>$r['id'],'fos_name'=>$r['fos_name'],'user_name'=>$r['user_name']));
					}
					echo '{"Result":'.json_encode($usersArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					array_push($usersArr,array('status'=>'empty'));
					echo '{"Result":'.json_encode($usersArr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
	}
?>