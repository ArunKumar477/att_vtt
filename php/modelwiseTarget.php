<?php

	//require_once('config_s.php');
	require_once('config.php');
	//include('getUserRights.php');
	if(isset($_GET['app_user']))
	{
		$app_user = $_GET['app_user'];
		$app_userId = $_GET['app_userId'];
		$stDate = date('Y-m-01');
    	$endDate = date('Y-m-t');
		if($con)
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
			{
				/*$query = "select A.model,A.target,ifnull(sum(s.qty),0) as totalQty from 
						(select distinct model,sum(target) as target from modelwise_target group by model) A left outer join sales s
						on A.model=s.product_model and s.sales_date between '$stDate' and '$endDate'
						group by A.model";*/
				$query = "select p.product_category,b.model,target,totalQty from (select A.model,A.target,ifnull(sum(s.qty),0) as totalQty from 
						(select distinct model,sum(target) as target from modelwise_target group by model) A left outer join sales s
						on A.model=s.product_model and s.sales_date between '$stDate' and '$endDate'
						group by A.model) b left outer join product_master p on b.model=p.product_model group by p.product_model order by p.product_category desc,p.dp";
			}
			else
			{
				$con->query("SET SQL_BIG_SELECTS=1");
				/*$query = "select distinct t.model,t.target,'0' as totalQty from modelwise_target t where t.fos='$app_userId' and model not in
					(select s.product_model from sales s,shops sh where s.particulars=sh.Name and sh.fos='$app_userId' 
					and s.sales_date between '$stDate' and '$endDate' and sh.Deleted='0')
					union
					(select s.product_model as model,m.target,sum(s.qty) as totalQty from sales s,modelwise_target m,shops sh 
					where sh.Name=s.particulars and sh.fos='$app_userId' and sh.Deleted='0' and s.product_model=m.model and m.fos='$app_userId' and s.sales_date 	
					between '$stDate' and '$endDate' group by s.product_model order by m.target desc)";*/
				$query = "select p.product_category,b.model,b.target,b.totalQty from(select distinct t.model,t.target,'0' as totalQty from modelwise_target t where t.fos='$app_userId' and model not in(select s.product_model from sales s,shops sh where s.particulars=sh.Name and sh.fos='$app_userId' and s.sales_date between '$stDate' and '$endDate' and sh.Deleted='0')union(select s.product_model as model,m.target,sum(s.qty) as totalQty from sales s,modelwise_target m,shops sh where sh.Name=s.particulars and sh.fos='$app_userId' and sh.Deleted='0' and s.product_model=m.model and m.fos='$app_userId' and s.sales_date between '$stDate' and '$endDate' group by s.product_model order by m.target desc)) b left outer join product_master p on b.model=p.product_model group by p.product_model order by p.product_category desc,p.dp";
			}
			$exe = mysqli_query($con,$query);
			$cnt = mysqli_num_rows($exe);
			$resArr = array();
			if($exe)
			{
				if($cnt>0)
				{
					while($mdl = mysqli_fetch_array($exe))
					{
						$modelFullName = $mdl['model'];
						//echo $modelFullName.'<br>';
						array_push($resArr,array('status'=>'success','product_category'=>$mdl['product_category'],'modelFullName'=>$modelFullName,'target'=>$mdl['target'],'totalQty'=>$mdl['totalQty']));
					}
					echo '{"result":'.json_encode($resArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$res= array('status'=>'failed');
					echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
	}
	if(isset($_GET['shpWiseTrgt']))
	{
		$app_userId = $_GET['app_userId'];
		$modelName = $_GET['modelName'];
		$stDate = date('Y-m-01');
		$endDate = date('Y-m-t');
		if($con)
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
			{
				$query = "(select b.*,ifnull(m.TargetCount,0) as Target from (select sh.id id,a.* from  (SELECT particulars,ifnull(sum(`qty`),0) as Achievement FROM `sales` WHERE `product_model`='$modelName' and sales_date BETWEEN '$stDate' and '$endDate' group by particulars) a left outer join shops sh on a.particulars=sh.Name where sh.Deleted=0) b left outer join (select ifnull(sum(TargetCount),0) TargetCount,shopid,period from monthlyshoptargets where MONTH(Period)=MONTH(curdate()) and model='$modelName' and TargetCount!=0 group by shopid) m on b.id=m.ShopID group by b.ID)
union
(select ShopID id,(select Name from shops where id=`ShopID`) as Name,'0' as Achievement,ifnull(sum(TargetCount),0) as Target FROM `monthlyshoptargets` WHERE MONTH(`Period`)=MONTH(curdate()) and Model='$modelName' and TargetCount!=0 and (select Name from shops where id=`ShopID`) not in (select particulars from sales where sales_date between '$stDate' and '$endDate' and product_model='$modelName' group by particulars) group by ShopID)
union
(select sh.id,sh.Name,sum(qty) as Achievement,'0' as Target from sales s,shops sh where s.sales_date between '$stDate' and '$endDate' and s.product_model='$modelName' and s.particulars=sh.Name and sh.id not in (select ShopID from monthlyshoptargets where Model='$modelName' and Month(Period)=Month(curdate())) group by sh.id)";	
			}
			else
			{
				$query = "(select b.*,ifnull(m.TargetCount,0) as Target from (select sh.id id,sh.fos,a.* from  (SELECT particulars,ifnull(sum(`qty`),0) as Achievement FROM `sales` WHERE `product_model`='$modelName' and sales_date BETWEEN '$stDate' and '$endDate' group by particulars) a left outer join shops sh on a.particulars=sh.Name where sh.Deleted=0 and sh.fos='$app_userId') b left outer join (select ifnull(sum(TargetCount),0) TargetCount,shopid,period from monthlyshoptargets where MONTH(Period)=MONTH(curdate()) and model='$modelName' and TargetCount!=0 group by shopid) m on b.id=m.ShopID where b.fos='$app_userId' group by b.ID)
union
(select m.ShopID as id,shp.fos,shp.Name as Name,'0' as Achievement,ifnull(sum(m.TargetCount),0) as Target FROM `monthlyshoptargets` m,shops shp WHERE m.ShopID=shp.id and MONTH(m.Period)=MONTH(curdate()) and m.Model='$modelName' and m.TargetCount!=0 and shp.fos='$app_userId' and m.ShopID not in (SELECT sh.id FROM `sales` s,shops sh WHERE s.`particulars`=sh.Name and sh.fos='$app_userId' and s.product_model='$modelName' and s.sales_date between '$stDate' and '$endDate' group by s.particulars) group by m.ShopID)
union
(select sh.id,sh.fos,sh.Name,sum(qty) as Achievement,'0' as Target from sales s,shops sh where s.sales_date between '$stDate' and '$endDate' and s.product_model='$modelName' and s.particulars=sh.Name and sh.fos='$app_userId' and sh.id not in (select ShopID from monthlyshoptargets where Model='$modelName' and Month(Period)=Month(curdate()) and ShopID=sh.id and sh.fos='$app_userId') group by sh.id)";		
			}
			$exe = mysqli_query($con,$query);
			$cnt = mysqli_num_rows($exe);
			$resArr = array();
			if($exe)
			{
				if($cnt>0)
				{
					while($mdl = mysqli_fetch_array($exe))
					{
						$Name = $mdl['particulars'];
						//echo $modelFullName.'<br>';
						array_push($resArr,array('status'=>'success','Name'=>$Name,'target'=>$mdl['Target'],'TotalQty'=>$mdl['Achievement']));
					}
					echo '{"result":'.json_encode($resArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$res= array('status'=>'failed');
					echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}	
	}
	if(isset($_GET['user']))
	{
		$app_userId = $_GET['user'];
		$status = $_GET['status'];
		if($con)
		{
			if($status=='notification_outstnds')
			{
				$getRights = "select rights from app_users where id='$app_userId' and Active=1";
				$rightsExe = mysqli_query($con,$getRights);
				$rights = mysqli_fetch_array($rightsExe);
				if($rights['rights']=='2')
				{
					$query = "select distinct o.party_name,o.ref_no,o.pending_amount,o.outstanding_date,o.overdue from outstandings o, shops s 
						where o.party_name=s.Name and s.Deleted='0' and o.overdue>=10 order by o.outstanding_date limit 3";
				}
				else
				{
					$query = "select distinct o.party_name,o.ref_no,o.pending_amount,o.outstanding_date,o.overdue from outstandings o, shops s 
						where o.party_name=s.Name and s.Deleted='0' and s.fos='$app_userId' and o.overdue>=10 order by o.outstanding_date limit 3";
				}
			}
			if($status=='pageshow')
			{
				$getRights = "select rights from app_users where id='$app_userId' and Active=1";

				$rightsExe = mysqli_query($con,$getRights);
				$rights = mysqli_fetch_array($rightsExe);
				if($rights['rights']=='2')
				{
					$query = "select distinct o.party_name,o.ref_no,o.pending_amount,o.outstanding_date,o.overdue from outstandings o, shops s 
						where o.party_name=s.Name and s.Deleted='0' and o.overdue>=10 order by o.outstanding_date";
				}
				else
				{
					$query = "select distinct o.party_name,o.ref_no,o.pending_amount,o.outstanding_date,o.overdue from outstandings o, shops s 
						where o.party_name=s.Name and s.Deleted='0' and s.fos='$app_userId' and o.overdue>=10 order by o.outstanding_date";
				}	
			}
			$exe = mysqli_query($con,$query);
			 
			$cnt = mysqli_num_rows($exe);
			$resArr = array();
			if($exe)
			{
				if($cnt>0)
				{
					while($mdl = mysqli_fetch_array($exe))
					{

						array_push($resArr,array('status'=>'success','party_name'=>$mdl['party_name'],'ref_no'=>$mdl['ref_no'],'pending_amount'=>$mdl['pending_amount'],'outstanding_date'=>$mdl['outstanding_date'],'overdue'=>$mdl['overdue']));
					}
					echo '{"result":'.json_encode($resArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$res= array('status'=>'failed');
					echo '{"result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
	}
	
	if(isset($_GET['ChequeBounce_status']))
	{
		$fosId = $_GET['fosId'];
		if($con)
		{
			$getRights = "select rights from app_users where id='$fosId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
			{
				$sql = "select o.outstanding_date,o.ref_no,o.party_name,o.pending_amount from outstandings o,shops s where o.party_name=s.Name and s.Deleted='0' and 
			       		(SUBSTRING(o.ref_no,1,1)='C' or SUBSTRING(o.ref_no,1,1)='B') and SUBSTRING_INDEX(o.ref_no,'/','1')!='CN'";
			}
			else
			{
				$sql = "select o.outstanding_date,o.ref_no,o.party_name,o.pending_amount from outstandings o,shops s where o.party_name=s.Name and s.Deleted='0' and
			        s.fos='$fosId' and (SUBSTRING(o.ref_no,1,1)='C' or SUBSTRING(o.ref_no,1,1)='B') and SUBSTRING_INDEX(o.ref_no,'/','1')!='CN'";
			}
			$execute = mysqli_query($con,$sql);
			$cntVal  = mysqli_num_rows($execute);
			$chequeBounceArr = array();
			if($execute)
			{
				if($cntVal>0)
				{
					while($res = mysqli_fetch_array($execute))
					{
						array_push($chequeBounceArr,array('status'=>'success','outstanding_date'=>$res['outstanding_date'],'ref_no'=>$res['ref_no'],
									'party_name'=>$res['party_name'],'pending_amount'=>$res['pending_amount']));
					}
					echo '{"result":'.json_encode($chequeBounceArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$resArr = array('status'=>'failed');
					echo '{"result":'.json_encode($chequeBounceArr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}	
	}
	
	if(isset($_GET['app_stkUser']))
	{
		$app_stkUser = $_GET['app_stkUser'];
		if(isset($_GET['shpId']))
			$shpId = $_GET['shpId'];
			//$shpId = '861';
		if($con)
		{
			if(isset($_GET['shpId']))
				$sql = "select a.product_model as model, ifnull(b.quantity,0) as quantity from (select distinct sales.product_model,shops.id from sales right outer join shops on sales.particulars=shops.Name where shops.id='$shpId' group by shops.id,sales.product_model) a LEFT outer join (select sd.model,sd.quantity from stocks_detail_c sd,stocks_c s where sd.stockid=s.id and s.shopid='$shpId') b on a.product_model= b.model where a.product_model is not null";
				//$sql = "select sd.model,sd.quantity from stocks_detail_c sd,stocks_c s where sd.stockid=s.id and s.shopid='$shpId'";
			$execute = mysqli_query($con,$sql);
			$cntValStck  = mysqli_num_rows($execute);
			$stckArr = array();
			$arr = array();
			if($execute)
			{
				if($cntValStck>0)
				{
					while($res = mysqli_fetch_array($execute))
					{
						if(isset($_GET['shpId']) && $res['model']!='')
						{
							array_push($stckArr,array('status'=>'success','product_model'=>$res['model'],'quantity'=>$res['quantity']));
							array_push($arr,$res['model']);
						}
					}
					$getStckId = mysqli_query($con,"SELECT `product_model` FROM `product_master` WHERE product_name='Nokia' and product_model!='' group by `product_model`");
					if(mysqli_num_rows($getStckId)>0)
					{
						while($getStckId_data = mysqli_fetch_array($getStckId))
						{
							if (!in_array($getStckId_data['product_model'], $arr))
							{
								array_push($stckArr,array('status'=>'success','product_model'=>$getStckId_data['product_model'],'quantity'=>0));
							}
						}
					}
					echo '{"result":'.json_encode($stckArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					//$getStckId = "select sh.name,s.product_model from sales s left outer join shops sh on sh.name=s.particulars where sh.id='$shpId' group by s.product_model";
					$getStckId = "SELECT `product_model` FROM `product_master` WHERE product_name='Nokia' and product_model!='' group by `product_model`";
					$getStckId_ex = mysqli_query($con,$getStckId);
					if($getStckId_ex)
					{
						if(mysqli_num_rows($getStckId_ex)>0)
						{
							while($stckId = mysqli_fetch_array($getStckId_ex))
							{
								$product_model = $stckId['product_model'];
								array_push($stckArr,array('status'=>'success','product_model'=>$product_model,'quantity'=>0));
							}//while
							echo '{"result":'.json_encode($stckArr,JSON_UNESCAPED_SLASHES).'}';
						}
						else
						{
							$stckArr = array('status'=>'norows');
							echo '{"result":'.json_encode($stckArr,JSON_UNESCAPED_SLASHES).'}';
						}
					}
				}//else
			}//if
		}//$con
	}//isset
	
	if(isset($_GET['getMdlColors']))
	{
		$model = $_GET['model'];
		$Qty = $_GET['Qty'];
		if($con)
		{
			//$sql = mysqli_query($con,"select distinct color,round(dp+(dp*gst/100)) dp from product_master where product_model='$model' group by color");
			$sql = mysqli_query($con,"select a.color,a.product_model,a.dp,p.quantity from (select distinct color,product_model,round(dp) dp from product_master where product_model='$model' group by color) a left outer join products p on a.product_model=p.product_model and a.color=p.color order by p.id DESC");
			$colorArr = array();
			if($sql)
			{
				if(mysqli_num_rows($sql)>0)
				{
					$totalQty = 0;
					$sql1 = mysqli_query($con,"select a.color,a.product_model,a.dp,p.quantity from (select distinct color,product_model,round(dp) dp from product_master where product_model='$model' group by color) a left outer join products p on a.product_model=p.product_model and a.color=p.color order by p.id DESC");
					$resLen = 0;
					foreach($sql1 as $a)
					{
						$totalQty = (int)$totalQty+(int)$a['quantity'];
						$resLen++;
					}
					$i = 1;
					$ttlPerQty = 0;
					while($res = mysqli_fetch_array($sql))
					{
						$eachPercent = round(sprintf( '%.2f', ($res['quantity']/$totalQty)*(100/1)));
						$eachQty = round(sprintf( '%.2f', ($Qty*$eachPercent)/100));
						$ttlPerQty = $ttlPerQty+$eachQty;
						//echo $ttlPerQty;
						//echo $res['product_model'].'-'.$res['quantity'].'='.$eachPercent.'%------->'.$eachQty.'<br>';
						if($resLen==$i)
						{
							if($Qty!=$ttlPerQty && $Qty<$ttlPerQty)	
								$eachQty = $eachQty+($Qty-$ttlPerQty);
							if($Qty!=$ttlPerQty && $Qty>$ttlPerQty)	
								$eachQty = $eachQty+($Qty-$ttlPerQty);
						}	
						array_push($colorArr,array('status'=>'success','color'=>$res['color'],'dp'=>$res['dp'],'qty'=>$eachQty));
						$i++;
					}
					echo '{"result":'.json_encode($colorArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$resArr = array('status'=>'failed');
					echo '{"result":'.json_encode($colorArr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}	
	}
?>