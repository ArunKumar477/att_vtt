<?php

	require_once('config.php');
	//require_once('config_s.php');
	date_default_timezone_set("Asia/Kolkata");
	$todayDate = date("Y-m-d");
	if($con)
	{
        if(isset($_GET['getvalTrget']))
		{ 	 	
			$req = $_GET['req'];
			if($req=='all') 
				$query = "SELECT a.fos,Name,Target,ifnull(round(Achieved),0) as Achieved,ifnull(round(Target-Achieved),0) as Pending, ifnull(round(Achieved/Target *100),0) as AchievedPerc from (SELECT shops.fos,shops.Name as Name,shops.target_b as Target from shops LEFT OUTER JOIN sales on shops.Name=sales.particulars where shops.Deleted='0' and shops.target_b<>0 group by shops.Name) a left outer join (SELECT sales.particulars,sum(debit_amount) as Achieved FROM sales WHERE month(sales.sales_date)=month(curdate()) group by sales.particulars) b on a.Name=b.particulars  order by a.Target DESC";
			else
			{
				$id=$_GET['id'];
				$query = "SELECT a.fos,Name,Target,ifnull(round(Achieved),0) as Achieved,ifnull(round(Target-Achieved),0) as Pending, ifnull(round(Achieved/Target *100),0) as AchievedPerc from (SELECT shops.fos,shops.Name as Name,shops.target_b as Target from shops LEFT OUTER JOIN sales on shops.Name=sales.particulars where shops.Deleted='0' and shops.target_b<>0 group by shops.Name) a left outer join (SELECT sales.particulars,sum(debit_amount) as Achieved FROM sales WHERE month(sales.sales_date)=month(curdate()) group by sales.particulars) b on a.Name=b.particulars where a.fos=$id  order by a.Target DESC";
			}
			$exe   = mysqli_query($con,$query);
			if($exe)
			{
				$cnt = mysqli_num_rows($exe);
				$valtarArr = array();
				if($cnt>0)
				{
					while($res = mysqli_fetch_array($exe))
					{
						array_push($valtarArr,array('status'=>'success','fos'=>$res['fos'],'Name'=>$res['Name'],'Target'=>$res['Target'],'Pending'=>$res['Pending'],'Achieved'=>$res['Achieved'],'AchievedPerc'=>$res['AchievedPerc']));
					}
					echo '{"Result":'.json_encode($valtarArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					array_push($valtarArr,array('status'=>'norows'));
					echo '{"Result":'.json_encode($valtarArr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
	}
	

?>