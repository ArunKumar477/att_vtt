<?php
	//require_once('config_s.php');
	require_once('config.php');
	date_default_timezone_set('Asia/Kolkata');
	$todayDate = date("Y-m-d");
	$currentTime = date("h:i:s");
	$date_time = date("Y-m-d H:i:s");
	if($con)
	{
		if(isset($_GET['stckInput']))
		{
			$stckInput  = $_GET['stckInput'];
			$userId = $_GET['userId'];
			$shpId = $_GET['shpId'];
			$stckInputVal = explode(",",$stckInput);
			$sql  = "select id from stocks_c where shopid='$shpId'";
			$ex   = mysqli_query($con,$sql);
			$cnt = mysqli_num_rows($ex);
			if($cnt=='0')
			{
				$query = "insert into stocks_c (shopid,userid,purpose,datetime)values('$shpId','$userId','CounterStock','$date_time')";
				$exe = mysqli_query($con,$query);
				if($exe)
				{
					for($i=0;$i<sizeof($stckInputVal);$i++)
					{
						$modelQty = $stckInputVal[$i];
						$res = explode("!",$modelQty);
						$model = $res[0];
						$qty = $res[1];
						$query1 = "insert into stocks_detail_c (stockid,model,quantity)values((select id from stocks_c where shopid='$shpId'),'$model','$qty')";
						$exe1 = mysqli_query($con,$query1);
						if($i+1==sizeof($stckInputVal))
						{
							if($exe1)
								$res= array('status'=>'success');
							else
								$res= array('status'=>'failed');
							echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
						}
					}//for
				}//$exe
				else
				{
					$res= array('status'=>'failed');
					echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				$ssql_c = "select id,shopid,userid,purpose,datetime from stocks_c where shopid='$shpId'";
				$execute_c = mysqli_query($con,$ssql_c);
				$stockid_c = '';
				if($execute_c)	
				{
					if(mysqli_num_rows($execute_c)>0)
					{
						while($dataRes_c = mysqli_fetch_array($execute_c))
						{
							$stockid_c = $dataRes_c['id'];
							$shopid = $dataRes_c['shopid'];
							$userid = $dataRes_c['userid'];
							$purpose = $dataRes_c['purpose'];
							$datetime = $dataRes_c['datetime'];
							$putBckSql = "insert into stocks_h(stockid,shopid,userid,purpose,datetime)values('$stockid_c','$shopid','$userid','$purpose','$datetime')";
							$putBckExe = mysqli_query($con,$putBckSql);
						}
					}
				}
				
				$ssql = "select stockid,model,quantity from stocks_detail_c where stockid=(select id from stocks_c where shopid='$shpId') and quantity!='0'";
				$execute = mysqli_query($con,$ssql);
				if($execute)
				{
					if(mysqli_num_rows($execute)>0)
					{
						while($dataRes = mysqli_fetch_array($execute))
						{
							$stockid = $dataRes['stockid'];
							$model = $dataRes['model'];
							$quantity = $dataRes['quantity'];
							$putBckSql = "insert into stocks_detail_h(stockid,model,quantity)values('$stockid','$model','$quantity')";
							$putBckExe = mysqli_query($con,$putBckSql);
						}
					}
				}
				
				if($execute)
				{
					$delQuery1 = "delete from stocks_detail_c where stockid=(select id from stocks_c where shopid='$shpId')";
					$delExe1   = mysqli_query($con,$delQuery1);
				}
				if($execute_c)
				{
					$delQuery = "delete from stocks_c where shopid='$shpId'";
					$delExe   = mysqli_query($con,$delQuery);
				}
				
				$query = "insert into stocks_c (shopid,userid,purpose,datetime)values('$shpId','$userId','CounterStock','$date_time')";
				//$query = "update stocks_c set shopid='$shpId',userid='$userId',datetime='$date_time' where id='$stockid_c'";
				$exe = mysqli_query($con,$query);
				if($exe)
				{
					for($i=0;$i<sizeof($stckInputVal);$i++)
					{
						$modelQty = $stckInputVal[$i];
						$res = explode("!",$modelQty);
						$model = $res[0];
						$qty = $res[1];
						$query1 = "insert into stocks_detail_c (stockid,model,quantity)values((select id from stocks_c where shopid='$shpId'),'$model','$qty')";
						//$query1 = "update stocks_detail_c set quantity='$qty',datetime='$date_time' where stockid='$stockid_c' and model='$model'";
						$exe1 = mysqli_query($con,$query1);
						if($i+1==sizeof($stckInputVal))
						{
							if($exe1)
								$res= array('status'=>'success');
							else
								$res= array('status'=>'failed');
						}
					}//for
					echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
				}//$exe
				else
				{
					$res= array('status'=>'failed');
					echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}//isset($_GET['stckInput'])
	}//$con
?>