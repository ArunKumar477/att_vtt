<?php 
	error_reporting(E_ERROR);
	//require_once('config_s.php');
	require_once('config.php');
	date_default_timezone_set('Asia/Kolkata');
	$setDateTime = date("Y-m-d H:i:s");
	if($con)
	{
		if(isset($_POST['add_orders']))
		{
			$shopId = $_POST['shopNameTxt'];
			$app_user = $_POST['app_user'];
			$userId = $_POST['app_userId'];
			$jsonString_orders = $_POST['jsonString_orders'];
			$jsonString_orders1 = json_decode($jsonString_orders, true);
			$shpLat = $_POST['shpLat'];
			$shpLong = $_POST['shpLong'];
			/*$shopId = 592;
			$app_user = 9500005342;
			$userId = 3;
			$jsonString_orders = '[{"prdctType":"Nokia","prdctQuantity":"2","prdctName":"150 DS","prdctColor":"Black","orderVal":"3750"},{"prdctType":"Nokia","prdctQuantity":"1","prdctName":"105 DS","prdctColor":"Black New","orderVal":"1000"}]';
			$jsonString_orders1 = json_decode($jsonString_orders, true);
			$shpLat = 12.5466;
			$shpLong = 80.54514;*/
			
			$todayDate = date("Y-m-d");
			$todayDate_ordrId = date("Ymd");
			$categoryWiseOrder = 0;
			$Y = date("Y");
			$run_status = 1;
			$insertedIds_arr = array();
			$finalUnqId = '';
			//var_dump($jsonString_orders1);
			foreach($jsonString_orders1 as $dat=>$v)
			{
				if($v['prdctQuantity']!='' && $v['prdctQuantity']!=0 && $v['prdctQuantity']!="0")
				{
					$prdctType = $v['prdctType'];
					$prdctName = $v['prdctName'];
					$prdctColor = $v['prdctColor'];
					$prdctQuantity = $v['prdctQuantity'];
					$orderVal = $v['orderVal'];
					$query_billed = '';
					
					if($categoryWiseOrder==0)
						$getUnqId = mysqli_query($con,"select unique_id from billed_orders where shop_id='$shopId' and DATE(order_date)='$todayDate' and delivery_status='0' limit 1");
					else
						$getUnqId = mysqli_query($con,"select unique_id from billed_orders where shop_id='$shopId' and DATE(order_date)='$todayDate' and delivery_status='0' and product_category=(select product_category from product_master where product_model='$prdctName' limit 1)");
					
					if($getUnqId)
					{
						if(mysqli_num_rows($getUnqId)>0)
						{
							$res = mysqli_fetch_array($getUnqId);
							$UniqueId = $res['unique_id'];
							$finalUnqId = $UniqueId;
							$query_billed = mysqli_query($con,"insert into billed_orders(user_id,shop_id,product_category,product_type,product_name,color,Quantity,latitude,
							longitude,unique_id,order_date) values('$userId','$shopId',(select product_category from product_master where product_model='$prdctName' 
							limit 1),'$prdctType','$prdctName','$prdctColor','$prdctQuantity','$shpLat','$shpLong','$UniqueId','$setDateTime')");
							
							$query_orders = mysqli_query($con,"insert into orders(user_id,shop_id,product_category,product_type,product_name,color,Quantity,latitude,
							longitude,unique_id,order_date) values('$userId','$shopId',(select product_category from product_master where product_model='$prdctName' 
							limit 1),'$prdctType','$prdctName','$prdctColor','$prdctQuantity','$shpLat','$shpLong','$UniqueId','$setDateTime')");
						}
						else
						{
							/* get Unique Id */
							$getExistsUnqId = mysqli_query($con,"SELECT SUBSTR(unique_id,10) as uniqueId,SUBSTR(unique_id,1,4) as uniqueIdYear FROM `billed_orders` where unique_id!='' order by unique_id desc limit 1");
							$uniqFinalVal = 0;
							$uniqueIdYear = '';
							if(mysqli_num_rows($getExistsUnqId)!=0)
							{
								$res = mysqli_fetch_array($getExistsUnqId);
								$uniqFinalVal = $res['uniqueId'];
								$uniqueIdYear = $res['uniqueIdYear'];
								if($Y==$uniqueIdYear)
								{
									$uniqFinalVal = $uniqFinalVal+1;
									$dateUnique = $todayDate_ordrId.'/'.$uniqFinalVal;
								}
								else
									$dateUnique = $todayDate_ordrId.'/1';
							}
							else
							{
								$dateUnique = $todayDate_ordrId.'/1';
							}
							/* Unique end */
							
							/* check yesterday data */
							$yesterday = date('Y-m-d',strtotime("-1 days"));
							if($categoryWiseOrder==0)
								$uniqIdYesterday = mysqli_query($con,"select unique_id from billed_orders where shop_id='$shopId' and DATE(order_date)='$yesterday' limit 1");
							else
								$uniqIdYesterday = mysqli_query($con,"select unique_id from billed_orders where shop_id='$shopId' and DATE(order_date)='$yesterday' and product_category=(select product_category from product_master where product_model='$prdctName' limit 1)");
							if(mysqli_num_rows($uniqIdYesterday)>0)
							{
								$dres = mysqli_fetch_array($uniqIdYesterday);
								$dateUnique1 = $dres['unique_id'];
								$daysOrders_cnt = mysqli_query($con,"select distinct(DATE(order_date)) from billed_orders where unique_id='$dateUnique1'");
								if(mysqli_num_rows($daysOrders_cnt)>1)
								{
									$finalUnqId = $dateUnique;
									$query_billed = mysqli_query($con,"insert into billed_orders(user_id,shop_id,product_category,product_type,product_name,color,Quantity,
									latitude,longitude,unique_id,order_date) values('$userId','$shopId',(select product_category from product_master 
									where product_model='$prdctName' limit 1),'$prdctType','$prdctName','$prdctColor','$prdctQuantity','$shpLat',
									'$shpLong','$dateUnique','$setDateTime')");
									
									$query_orders = mysqli_query($con,"insert into orders(user_id,shop_id,product_category,product_type,product_name,color,Quantity,
									latitude,longitude,unique_id,order_date) values('$userId','$shopId',(select product_category from product_master 
									where product_model='$prdctName' limit 1),'$prdctType','$prdctName','$prdctColor','$prdctQuantity','$shpLat',
									'$shpLong','$dateUnique','$setDateTime')");
								}
								else
								{
									$finalUnqId = $dateUnique1;
									$query_billed = mysqli_query($con,"insert into billed_orders(user_id,shop_id,product_category,product_type,product_name,color,Quantity,
									latitude,longitude,unique_id,order_date) values('$userId','$shopId',(select product_category from product_master
									 where product_model='$prdctName' limit 1),'$prdctType','$prdctName','$prdctColor','$prdctQuantity','$shpLat','$shpLong',
									 '$dateUnique1','$setDateTime')");
									 
									 $query_orders = mysqli_query($con,"insert into orders(user_id,shop_id,product_category,product_type,product_name,color,Quantity,
									latitude,longitude,unique_id,order_date) values('$userId','$shopId',(select product_category from product_master
									 where product_model='$prdctName' limit 1),'$prdctType','$prdctName','$prdctColor','$prdctQuantity','$shpLat','$shpLong',
									 '$dateUnique1','$setDateTime')");							
								}
							}//if(mysqli_num_rows($uniqIdYesterday)>0)
							else
							{
								$finalUnqId = $dateUnique;
								$query_billed = mysqli_query($con,"insert into billed_orders(user_id,shop_id,product_category,product_type,product_name,color,Quantity,
								latitude,longitude,unique_id,order_date)values('$userId','$shopId',(select product_category from product_master where 
								product_model='$prdctName' limit 1),'$prdctType','$prdctName','$prdctColor','$prdctQuantity','$shpLat','$shpLong','$dateUnique',
								'$setDateTime')");
								
								$query_orders = mysqli_query($con,"insert into orders(user_id,shop_id,product_category,product_type,product_name,color,Quantity,
								latitude,longitude,unique_id,order_date)values('$userId','$shopId',(select product_category from product_master where 
								product_model='$prdctName' limit 1),'$prdctType','$prdctName','$prdctColor','$prdctQuantity','$shpLat','$shpLong','$dateUnique',
								'$setDateTime')");
							}	
							/* end yesterday data */	
						}//else
						$exe_billed = $query_billed;
						$sql1 = mysqli_query($con,"SELECT LAST_INSERT_ID() as id");
						$ids = mysqli_fetch_array($sql1);
						array_push($insertedIds_arr,$ids['id']);
						if($exe_billed)
						{
							/* Reduce Quantity when get order script start..*/
							$getQty = mysqli_query($con,"select quantity from products where product_name='$prdctType' and product_model='$prdctName' and color='$prdctColor'");
							if($getQty)
							{
								if(mysqli_num_rows($getQty)==1)
								{
									$Qty = mysqli_fetch_array($getQty);
									$newQty = (int)$Qty['quantity']-(int)$prdctQuantity;
									$updtQty = mysqli_query($con,"update products set quantity='$newQty' where product_name='$prdctType' and product_model='$prdctName' and color='$prdctColor'");
								}
							}
							/* End Quantity reduce script !*/
						}
						else
						{
							$run_status = 0;
						}
					}//if($getUnqId)
				}
			}//foreach($jsonString_orders1 as $dat=>$v)
			if($run_status==1)
			{
				$ssql = mysqli_query($con,"select primary_mobile,secondary_mobile,primary_email,secondary_email from shops where id='$shopId' and Deleted='0'");
				$ownerInfo = mysqli_fetch_array($ssql); 
				$arr = array('status'=>'success','dateUnique'=>$finalUnqId,'primary_mobile'=>$ownerInfo['primary_mobile'],
							'secondary_mobile'=>$ownerInfo['secondary_mobile'],'primary_email'=>$ownerInfo['primary_email'],
							'secondary_email'=>$ownerInfo['secondary_email'],'lastInsertedIds'=>$insertedIds_arr);
				echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
			else
			{
				$arr = array('status'=>'failed');
				echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
		}//if(isset($_GET['prdctType']))
	}//$con
?>