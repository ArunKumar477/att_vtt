<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$lat = $_GET['lat'];
	$lng = $_GET['long'];
	$appUsrIdLcl = $_GET['appUsrIdLcl'];
	if($con)
	{


		if(isset($_GET['getAllShp']))
		{
			// for Delivery person
			$sql = "SELECT a.id,a.Name, a.distance from (SELECT shops.id,shops.Name, (6371000 * acos (cos ( radians($lat) )* cos( radians( Latitude ) )* cos( radians( Longitude ) - radians($lng) )+ sin ( radians($lat) )* sin( radians( Latitude ) ))) AS distance FROM shops WHERE shops.Deleted=0) a where a.distance < 100 ORDER BY a.distance LIMIT 0 , 1";
		}
		else
		{
			$query = mysqli_query($con,"select id from app_users where id='$appUsrIdLcl' and rights='2' and Active='1' and un_fos='1'");
			if(mysqli_num_rows($query)>0)
			{
				// fro unfos
				$sql = "SELECT a.id,a.Name, a.distance from (SELECT shops.id,shops.Name, (6371000 * acos (cos ( radians($lat) )* cos( radians( Latitude ) )* cos( radians( Longitude ) - radians($lng) )+ sin ( radians($lat) )* sin( radians( Latitude ) ))) AS distance FROM shops WHERE shops.Deleted=0) a where a.distance < 100 ORDER BY a.distance LIMIT 0 , 1";
			}
			else
			{
				$sql = mysqli_query($con,"select id from app_users where id='$appUsrIdLcl' and rights='2' and Active='1' and app_admin='1' and un_fos='0'");
				if($sql)
				{
					if(mysqli_num_rows($sql)>0)
					{
						// for admin
						$sql = "SELECT a.id,a.Name, a.distance from (SELECT shops.id,shops.Name, (6371000 * acos (cos ( radians($lat) )* cos( radians( Latitude ) )* cos( radians( Longitude ) - radians($lng) )+ sin ( radians($lat) )* sin( radians( Latitude ) ))) AS distance FROM shops WHERE shops.Deleted=0) a where a.distance < 100 ORDER BY a.distance LIMIT 0 , 1";
					}
					else
					{
						// for fos
						$sql = "SELECT a.id,a.Name, a.distance from (SELECT shops.id,shops.Name, (6371000 * acos (cos ( radians($lat) )* cos( radians( Latitude ) )* cos( radians( Longitude ) - radians($lng) )+ sin ( radians($lat) )* sin( radians( Latitude ) ))) AS distance FROM shops WHERE shops.Deleted=0 and shops.fos in ('$appUsrIdLcl',1)) a where a.distance < 100 ORDER BY a.distance LIMIT 0 , 1";
					}
				}
			}
		}
		$ex = mysqli_query($con,$sql);
		if($ex)
		{
			$shops = array();
			if(mysqli_num_rows($ex)>0)
			{
				while($rs = mysqli_fetch_array($ex))
					array_push($shops,array('Status'=>'success','shopId'=>$rs['id'],'shopName'=>$rs['Name']));
				echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
			}
			else
			{
				array_push($shops,array('shopName'=>'emptySet'));
				echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
			}
		}//ex
	}
?>