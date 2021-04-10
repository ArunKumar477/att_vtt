<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$app_user = $_GET['app_user'];
	$currDate = date("Y-m-d");
	if($con)
	{
		$getRights = "select rights from app_users where id='$app_user' and Active=1";
		$rightsExe = mysqli_query($con,$getRights);
		$rights = mysqli_fetch_array($rightsExe);
		if($rights['rights']=='2')
			$sql = "select a.shop_id,s.Name,a.attendance_date,a.purpose from attendance a,shops s where DATE(a.attendance_date) = '$currDate' and a.shop_id=s.id and s.Deleted='0'";
		else
			$sql = "select a.shop_id,s.Name,a.attendance_date,a.purpose from attendance a,shops s where a.fos='$app_user' and DATE(a.attendance_date) = '$currDate' and a.shop_id=s.id and s.Deleted='0'";
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		$shops = array();
		if($cnt>0)
		{
			while($rs = mysqli_fetch_array($ex))
			{
				array_push($shops,array('Status'=>'success','shopId'=>$rs['shop_id'],'shopName'=>$rs['Name'],'created'=>$rs['attendance_date'],'purpose'=>$rs['purpose']));
			}
			echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
		}
		else
		{
			array_push($shops,array('shopName'=>'emptySet'));
			echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
		}
	}
?>