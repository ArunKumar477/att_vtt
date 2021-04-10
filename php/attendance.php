<?php
	//require_once('config_s.php');
	require_once('config.php');
	//if(isset($_GET['$version']))
	//	$version = $_GET['version'];
	date_default_timezone_set('Asia/Kolkata');
	$setDateTime = date("Y-m-d H:i:s");
	if(isset($_GET['functionParse']))
	{
		$shp_id = $_GET['shp_id'];
		$fos = $_GET['fos'];
		if(isset($_GET['deviceUUID']))
			$deviceUUID = $_GET['deviceUUID'];
		else
			$deviceUUID = 'Empty';
		$currDate = date("Y-m-d");
		$functionParse = $_GET['functionParse']; 
		$shpLat = $_GET['shpLat'];
		$shpLong = $_GET['shpLong'];
		$attndsRdVal = $_GET['attndsRdVal'];
	}
	if(isset($_GET['getOutstnds']))
	{
		$shpName = $_GET['shpName'];
		if (strpos($shpName, '!!') !== false)
			$shpName = str_replace("!!","&",$shpName);
		if (strpos($shpName, '@@') !== false)
			$shpName = str_replace("@@","#",$shpName);
		$app_userId = $_GET['app_userId'];
		$functionParse = 3; 
	}
	
	if($con)
	{
		if(isset($_GET['functionParse']))
		{
			if($functionParse=='1')
			{
				$sql = "select shop_id from attendance where shop_id='$shp_id' and fos='$fos' and DATE(attendance_date) = '$currDate'";
				$ex  = mysqli_query($con,$sql);
				$cnt = mysqli_num_rows($ex);
				if($cnt==0)
				{
					$query = "insert into attendance(shop_id,fos,deviceUUID,latitude,longitude,purpose,attendance_date)
								values('$shp_id','$fos','$deviceUUID','$shpLat','$shpLong','$attndsRdVal','$setDateTime')";
					$exe = mysqli_query($con,$query);
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
				if($cnt>0)
				{
					$arr = array('status'=>'error');
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			if($functionParse=='2')
			{
					$query = "insert into attendance(shop_id,fos,deviceUUID,latitude,longitude,purpose,attendance_date)
					values('$shp_id','$fos','$deviceUUID','$shpLat','$shpLong','$attndsRdVal','$setDateTime')";
					$exe = mysqli_query($con,$query);
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
		}

		if(isset($_GET['getOutstnds']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$query = "select o.outstanding_date,o.ref_no,o.pending_amount,o.due_on,o.overdue from outstandings o,shops s where o.party_name='$shpName' and o.party_name=s.Name and s.Deleted='0' group by o.pending_amount";
			else
				$query = "select o.outstanding_date,o.ref_no,o.pending_amount,o.due_on,o.overdue from outstandings o,shops s where o.party_name='$shpName' and o.party_name=s.Name and s.fos='$app_userId' and s.Deleted='0' group by o.pending_amount";
			$exe = mysqli_query($con,$query);
			$cnt = mysqli_num_rows($exe);
			$arr = array();
			if($exe)
			{
				if($cnt>0)
				{
					while($res = mysqli_fetch_array($exe))
					{	
						array_push($arr,array('status'=>'success','outstanding_date'=>$res['outstanding_date'],'ref_no'=>$res['ref_no'],
							'pending_amount'=>$res['pending_amount'],'due_on'=>$res['due_on'],'overdue'=>$res['overdue']));
					}
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$arr = array('status'=>'emptySet');
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}	
			} 
		}
	}
?>