<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$currentDate = date("Y-m-d");
	if(isset($_GET['SrchShopTxt']))
	{
		$userId = $_GET['appUsrIdLcl'];
		$SrchShopTxt  = $_GET['SrchShopTxt'];
		if (strpos($SrchShopTxt, '!!') !== false)
			$SrchShopTxt = str_replace("!!","&",$SrchShopTxt);
		if (strpos($SrchShopTxt, '@@') !== false)
			$SrchShopTxt = str_replace("@@","#",$SrchShopTxt);
	}
	if(isset($_GET['shpTxt']))
	{
		$shpTxt = $_GET['shpTxt'];
		if (strpos($shpTxt, '!!') !== false)
			$shpTxt = str_replace("!!","&",$shpTxt);
		if (strpos($shpTxt, '@@') !== false)
			$shpTxt = str_replace("@@","#",$shpTxt);
		$userId = $_GET['userId'];
		$shpTxt = mysqli_real_escape_string($con,$shpTxt);
	}
	//echo $shpTxt;
	if($con)
	{
			$shopsArr = array();
			$getRights = "select rights from app_users where id='$userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if(isset($_GET['SrchShopTxt']))
			{
				if($rights['rights']=='2')
					$sql = "SELECT id,Name FROM shops WHERE Deleted='0' && Name LIKE '%$SrchShopTxt%'";
				else
					$sql = "SELECT id,Name FROM shops WHERE Deleted='0' && Name LIKE '%$SrchShopTxt%' and fos='$userId'";
			}
			if(isset($_GET['shpTxt']))
			{
				if($rights['rights']=='2')
					$sql = "SELECT o.outstanding_date,o.ref_no,o.pending_amount,s.credit_period,o.overdue FROM outstandings o,shops s WHERE o.party_name='$shpTxt' and o.pending_amount>0 and s.Name=o.party_name and s.Deleted='0'";
				else
					$sql = "SELECT o.outstanding_date,o.ref_no,o.pending_amount,s.credit_period,o.overdue FROM outstandings o,shops s WHERE o.party_name='$shpTxt' and o.pending_amount>0 and s.fos='$userId' and s.Name=o.party_name and s.Deleted='0'";
			}
			$ex  = mysqli_query($con,$sql);
			$cnt = mysqli_num_rows($ex);
			if($ex)
			{
				if($cnt>0)
				{
					while($shopsRes = mysqli_fetch_array($ex))
					{ 
						if(isset($_GET['SrchShopTxt']))
							array_push($shopsArr,array('Status'=>'Success','id'=>$shopsRes['id'],'shopName'=>$shopsRes['Name']));
						if(isset($_GET['shpTxt']))
						{
							$credit_period = $shopsRes['credit_period'];
							$overdue = $shopsRes['overdue'];
							if($overdue<$credit_period)
							{
								$days = $credit_period-$overdue;
								$dueDate = date('Y-m-d', strtotime('+'.$days.' days'));
								$dueDate = DateTime::createFromFormat('Y-m-d', $dueDate)->format('d-m');
							}
							else if($overdue==$credit_period)
							{
								$dueDate = $currentDate;
								$dueDate = DateTime::createFromFormat('Y-m-d', $dueDate)->format('d-m');
							}
							else
								$dueDate = 'Exceeded';
							array_push($shopsArr,array('Status'=>'Success','outstanding_date'=>$shopsRes['outstanding_date'],'ref_no'=>$shopsRes['ref_no'],'pending_amount'=>$shopsRes['pending_amount'],'dueDate'=>$dueDate,'overdue'=>$overdue));
						}
					}
					echo '{"Result":'.json_encode($shopsArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					array_push($shopsArr,array("Status"=>'NoRows'));
					echo '{"Result":'.json_encode($shopsArr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				array_push($shopsArr,array("Status"=>'Failed'));
				echo '{"Result":'.json_encode($shopsArr,JSON_UNESCAPED_SLASHES).'}';
			}
	}
?>