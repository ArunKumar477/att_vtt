<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$currDate = date("Y-m-d");
	$year = date("Y");
	$month = date("m");
	if($con)
	{
		if(isset($_POST['getShpWiseMarketStats']))
		{
			$shpId = $_POST['shpId'];
			$app_user = $_POST['app_user'];
			$SrchShopTxt = $_POST['SrchShopTxt'];
			if (strpos($SrchShopTxt, '!!') !== false)
				$SrchShopTxt = str_replace("!!","&",$SrchShopTxt);
			if (strpos($SrchShopTxt, '@@') !== false)
				$SrchShopTxt = str_replace("@@","#",$SrchShopTxt);
			$getRights = "select rights from app_users where id='$app_user' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if(isset($_POST['req']))
				$req = $_POST['req'];
			$marketStatsArr = array();
			if($req=='shopSelect' || $req=='All')
			{
				if($rights['rights']=='2')
					//$sql = "select m.id,m.product_name,m.competitive_model,m.mop from (select sales.product_model from sales left outer join shops on sales.particulars=shops.name  where shops.name='$SrchShopTxt' and shops.Deleted='0' group by sales.product_model) a left outer join market_statistics m on a.product_model=m.product_name where (m.product_name is not null or m.competitive_model is not null or m.mop is not null) and YEAR(m.created)='$year' and MONTH(m.created)='$month' and m.id not in (select stats_id from market_stats_app where userid='$app_user' and shop_id='$shpId' and YEAR(created)='$year' and MONTH(created)='$month')";
					$sql = "select m.id,m.product_name,m.competitive_model,m.mop from  market_statistics m 
	where (m.product_name is not null or m.competitive_model is not null or m.mop is not null) and YEAR(m.created)='$year' and MONTH(m.created)='$month' and m.id not in 
	(select stats_id from market_stats_app where shop_id='$shpId' and YEAR(created)='$year' and MONTH(created)='$month') group by product_name";
				else	
					//$sql = "select m.id,m.product_name,m.competitive_model,m.mop from (select sales.product_model from sales left outer join shops on sales.particulars=shops.name  where shops.name='$SrchShopTxt' and shops.fos='$app_user' and shops.Deleted='0' group by sales.product_model) a left outer join market_statistics m on a.product_model=m.product_name where (m.product_name is not null or m.competitive_model is not null or m.mop is not null) and YEAR(m.created)='$year' and MONTH(m.created)='$month' and m.id not in (select stats_id from market_stats_app where userid='$app_user' and shop_id='$shpId' and YEAR(created)='$year' and MONTH(created)='$month')";
					if($req!='All')
						$sql = "select m.id,m.product_name,m.competitive_model,m.mop from  market_statistics m 
	where (m.product_name is not null or m.competitive_model is not null or m.mop is not null) and YEAR(m.created)='$year' and MONTH(m.created)='$month' and m.id not in 
	(select stats_id from market_stats_app where userid='$app_user' and shop_id='$shpId' and YEAR(created)='$year' and MONTH(created)='$month') group by product_name";
					else
						$sql = "select m.id,m.product_name,m.competitive_model,m.mop from  market_statistics m 
	where (m.product_name is not null or m.competitive_model is not null or m.mop is not null) and YEAR(m.created)='$year' and MONTH(m.created)='$month' and m.id not in 
	(select stats_id from market_stats_app where userid='$app_user' and shop_id='$shpId' and YEAR(created)='$year' and MONTH(created)='$month')";
			}
			else
			{
				$mdlVal = $req;
				$sql = "select m.id,m.product_name,m.competitive_model,m.mop from market_statistics m where m.id not in 
						(select stats_id from market_stats_app where userid='$app_user' and shop_id='$shpId' and YEAR(created)='$year' and MONTH(created)='$month') and 
						m.product_name='$mdlVal' and YEAR(m.created)='$year' and MONTH(m.created)='$month'";
			}
			$ex = mysqli_query($con,$sql);
			$cnt = mysqli_num_rows($ex);
			if($cnt>0)
			{
				$dataExistsStatus = 'notavailable';
				/*$ssql = "select ms.id,ms.product_name,ms.competitive_model,ms.mop,m.nlm from market_stats_app m,market_statistics ms 
						where m.shop_id='$shpId' and m.stats_id=ms.id and YEAR(m.created)='$year' and MONTH(m.created)='$month'";
				$run = mysqli_query($con,$ssql);
				if($run)
				{
					if(mysqli_num_rows($run)>0)
						$dataExistsStatus = 'available';	
				}*/
				if($dataExistsStatus == 'notavailable')
				{
					while($rs = mysqli_fetch_array($ex))
					{
						array_push($marketStatsArr,array('status'=>'success','id'=>$rs['id'],'product_name'=>$rs['product_name'],'competitive_model'=>$rs['competitive_model'],
						'mop'=>$rs['mop'],'dataExistsStatus'=>$dataExistsStatus));
					}
				}
				else
				{
					while($rs1 = mysqli_fetch_array($run))
					{
						array_push($marketStatsArr,array('status'=>'success','id'=>$rs1['id'],'product_name'=>$rs1['product_name'],
						'competitive_model'=>$rs1['competitive_model'],'mop'=>$rs1['mop'],'nlm'=>$rs1['nlm'],'dataExistsStatus'=>$dataExistsStatus));
					}
				}
				echo '{"Result":'.json_encode($marketStatsArr,JSON_UNESCAPED_SLASHES).'}';
			}
			if($cnt=='0')
			{
				$ssql = "select id from market_stats_app where userid='$app_user' and shop_id='$shpId' and YEAR(created)='$year' and MONTH(created)='$month'";
				$eex  = mysqli_query($con,$ssql);
				$MdlsAvailStatus = 'no';
				if($eex)
				{
					$ccnt = mysqli_num_rows($eex);
					if($ccnt>0)
						$MdlsAvailStatus = 'yes';
				} 
				array_push($marketStatsArr,array('status'=>'norows','MdlsAvailStatus'=>$MdlsAvailStatus));
				echo '{"Result":'.json_encode($marketStatsArr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		if(isset($_POST['putShpWiseMarketData']))
		{
			$app_user = $_POST['app_user'];
			$SrchShopTxt = $_POST['SrchShopTxt'];
			if (strpos($SrchShopTxt, '!!') !== false)
				$SrchShopTxt = str_replace("!!","&",$SrchShopTxt);
			if (strpos($SrchShopTxt, '@@') !== false)
				$SrchShopTxt = str_replace("@@","#",$SrchShopTxt);
			$shpId = $_POST['shpId'];
			$allData = $_POST['allData'];
			$splitData = explode(",",$allData);
			$dataSz = sizeof($splitData);
			$runStatus = '1';
			for($i=0;$i<$dataSz;$i++)
			{
				$singleValSplit = $splitData[$i];
				$splitVal = explode("@",$singleValSplit);
				$dbId = $splitVal[0];
				$competitive_model = $splitVal[2];
				$mop = $splitVal[3];
				$nlm = $splitVal[4];
				//echo $dbId.','.$competitive_model.','.$mop.','.$nlm.'<br>';
				$query = "insert into market_stats_app (userid,shop_id,stats_id,nlm) values ('$app_user','$shpId','$dbId','$nlm')";
				$exe = mysqli_query($con,$query);
				if(!$exe)
					$runStatus = '0';	
			}	
			if($runStatus=='1')
			{
				$marketStatsArr1 = array('status'=>'success');
				echo '{"Result":'.json_encode($marketStatsArr1,JSON_UNESCAPED_SLASHES).'}';
			}
			else
			{
				$marketStatsArr1 = array('status'=>'failed');
				echo '{"Result":'.json_encode($marketStatsArr1,JSON_UNESCAPED_SLASHES).'}';
			}
		}
	}
?>