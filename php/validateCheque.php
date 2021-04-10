<?php
	//require_once('config_s.php');
	require_once('config.php');
	$currentDate = date("Y-m-d");
	if($con)
	{
		if(isset($_GET['invoiceCheques']))
		{
			$app_userId = $_GET['app_userId'];
			$shpId = $_GET['shpId'];
			$invoiceCheques = $_GET['invoiceCheques'];
			$xpld = explode(",",$invoiceCheques);
			$arr = array();
			$resp = 1;
			for($j=0;$j<sizeof($xpld);$j++)
			{
				$splitVal = explode("!",$xpld[$j]);
				$dateVal = $splitVal[1];
				$inv_no = $splitVal[0];
				$amt = $splitVal[2];
				$chequeNo = $splitVal[3];
				$getRights = "select rights from app_users where id='$app_userId' and Active=1";
				$rightsExe = mysqli_query($con,$getRights);
				$rights = mysqli_fetch_array($rightsExe);
				if($rights['rights']=='2')
					$sql = "select s.credit_period,o.overdue from shops s,outstandings o where s.id='$shpId' and o.ref_no='$inv_no' and s.Name=o.party_name and s.Deleted='0'";
				else	
					$sql = "select s.credit_period,o.overdue from shops s,outstandings o where s.id='$shpId' and o.ref_no='$inv_no' and s.fos='$app_userId' and s.Name=o.party_name and s.Deleted='0'";
				if($sql)	
				{
					$ex = mysqli_query($con,$sql);
					$cp = mysqli_fetch_array($ex);
					$credit_period = $cp['credit_period'];
					$overdue = $cp['overdue'];
					//echo $credit_period.','.$overdue.'<br>';
				}
				if($credit_period+3<$overdue)
				{
					$day = $overdue-$credit_period;
					if($day==4)
					{
						if($currentDate==$dateVal)
						{
							array_push($arr,array('status'=>'AlertBox'));
							//echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
						}
						else
						{
							array_push($arr,array('status'=>'otpVerificationBox','inv_no'=>$inv_no,'dateVal'=>$dateVal,'amt'=>$amt,'chequeNo'=>$chequeNo));
							//echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
						}
					}
					else
					{
						array_push($arr,array('status'=>'otpVerificationBox','inv_no'=>$inv_no,'dateVal'=>$dateVal,'amt'=>$amt,'chequeNo'=>$chequeNo));
						//echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
					}
				}
				if($credit_period<$overdue && $credit_period+3>=$overdue)
				{
					array_push($arr,array('status'=>'AlertBox'));
					//echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
				if($overdue==$credit_period)
				{
					$now = time(); // or your date as well
					$your_date = strtotime($dateVal);
					$datediff = $your_date - $now;
					
					$chequeDays1 = floor($datediff / (60 * 60 * 24))+1;
					if(0<$chequeDays1)
					{
						array_push($arr,array('status'=>'AlertBox'));
						//echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
					}
				}
				if($overdue<$credit_period)
				{
					$days = $credit_period-$overdue;
					$now = time(); // or your date as well
					$your_date = strtotime($dateVal);
					$datediff = $your_date - $now;
					
					$chequeDays = floor($datediff / (60 * 60 * 24))+1;
					if($days<$chequeDays)
					{
						array_push($arr,array('status'=>'AlertBox'));
						//echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
					}
				}
				//$endDate = date('Y-m-d', strtotime('+'.$credit_period.' days'));
			}//for loop
			echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
		}//isset
	}//con
?>