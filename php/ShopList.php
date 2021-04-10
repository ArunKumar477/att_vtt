<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$currDate = date("Y-m-d");
	if($con)
	{
		$query = $con->prepare("select Name from shops where DATE(addedShop_date)='$currDate' and Deleted='0' order by id desc limit 5");
		$query->execute();
		$query->bind_result($Name);
		$query->store_result();
		$count = $query->num_rows();
		$shops = array();
		if($count>0)
		{
			while($query->fetch())
			{
				array_push($shops,array('Status'=>'success','shopName'=>$Name));
			}
			echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
		}
		else
		{
			array_push($shops,array('shopName'=>'emptySet'));
			echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
		}
	$query->close();
	}
	$con->close();
?>