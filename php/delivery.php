<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$todayDate = date("Y-m-d");
	$todayDate_time = date("Y-m-d H:i:s");
	if($con)
	{
		if(isset($_POST['chBoxUncIds']))
		{
			$app_userId = $_POST['app_userId'];
			$shpId = $_POST['shpId'];
			$chBoxUncIds = $_POST['chBoxUncIds'];
			$splitData = explode(",",$chBoxUncIds);
			$totalSz = sizeof($splitData);
			$runStatus = 'success';
			$errorInv = '';
			for($i=0;$i<$totalSz;$i++)
			{
				$chBoxUncIds = $splitData[$i];
				$sql = "update billed_orders set delivery_status='1',delivery_date='$todayDate_time' where Inv_no='$chBoxUncIds' and delivery_status='0'";
				$ex = mysqli_query($con,$sql);
				if(!$ex)
				{
					$runStatus = 'someDataNotUpdated';
					if($totalSz-1==$i)
						$errorInv .= $chBoxUncIds;
					else
						$errorInv .= $chBoxUncIds.',';
				}
			}//for
			
			$check_shpExists = "select id,userid from stocks_c where shopid='$shpId'";
			$c_ex = mysqli_query($con,$check_shpExists);
			$stock_c = mysqli_fetch_array($c_ex);	
			if(mysqli_num_rows($c_ex)>0)
			{
				/* backup stocks */
					$stockC_Id = $stock_c['id'];
					$stock_c_bc = "insert into stocks_h(stockid,shopid,userid,purpose,datetime) select id,shopid,userid,purpose,datetime 
									from stocks_c where shopid='$shpId'";
					$stock_c_bcEx = mysqli_query($con,$stock_c_bc);
					$stocks_detail_c_bc = "insert into stocks_detail_h(stockid,model,quantity) select stockid,model,quantity 
											from stocks_detail_c where stockid='$stockC_Id' and quantity!='0'";
					$stocks_detail_c_bcEx = mysqli_query($con,$stocks_detail_c_bc);
				/* backup end */
								
				/* delete & insert new */
					$delStcks = "delete from stocks_c where shopid='$shpId'";
					$delStcks_ex = mysqli_query($con,$delStcks);
					$putStcksNew = "insert into stocks_c(shopid,userid,purpose,datetime)values('$shpId','$app_userId','Delivery','$todayDate_time')";
					$putStcksNew_ex = mysqli_query($con,$putStcksNew);
					/* get new stock id */
						$getCurrentStckId = "select id from stocks_c where shopid='$shpId'";
						$getCurrentStckId_ex = mysqli_query($con,$getCurrentStckId);
						$stock_c = mysqli_fetch_array($getCurrentStckId_ex);
						$stockC_Id_N = $stock_c['id'];
					/* new stock id end */
					$updtStck_detailC = "update stocks_detail_c set stockid='$stockC_Id_N' where stockid='$stockC_Id'";
					$updtStck_detailC_ex = mysqli_query($con,$updtStck_detailC);
				/* delete & insert end */
				
				for($j=0;$j<$totalSz;$j++)
				{
					$chBoxUncIds1 = $splitData[$j];
					$getMdls = "select product_name,Quantity from billed_orders where Inv_no='$chBoxUncIds1'";
					$getMdlsEx = mysqli_query($con,$getMdls);
					if($getMdls)
					{
						if(mysqli_num_rows($getMdlsEx)>0)
						{
							while($mdl = mysqli_fetch_array($getMdlsEx))
							{
								$mdlName = $mdl['product_name'];
								$Quantity = $mdl['Quantity'];
									
								//echo $stockC_Id.','.$mdlName.','.$Quantity.'<br>';
								$increaseQty = "select quantity from stocks_detail_c where stockid='$stockC_Id_N' and model='$mdlName'";
								$increaseQty_ex = mysqli_query($con,$increaseQty);
								if($increaseQty_ex)
								{
									if(mysqli_num_rows($increaseQty_ex)>0)
									{
										$qty = mysqli_fetch_array($increaseQty_ex);
										$finalQty = $qty['quantity']+$Quantity;
										//echo $stockC_Id.','.$mdlName.','.$finalQty.'<br>';
										$updtQty = "update stocks_detail_c set quantity='$finalQty' where stockid='$stockC_Id_N' and model='$mdlName'"; 
										$updtQty_ex = mysqli_query($con,$updtQty);
									}
									else
									{
										$getQty1 = "select Quantity from billed_orders where billed_status='1' and delivery_status='1' and product_name='$mdlName'";
										$getQty_ex1 = mysqli_query($con,$getQty1);
										$qty_p1 = 0;
										if($getQty_ex1)
										{
											if(mysqli_num_rows($getQty_ex1)>0)
											{	
												$qty_p1 = mysqli_fetch_array($getQty_ex1);
												$qty_p1 = $qty_p1['Quantity']; 
											}
											else
												$qty_p1 = 0;
										}
										$putNewMdl = "insert into stocks_detail_c(stockid,model,quantity) values ('$stockC_Id_N','$mdlName','$qty_p1')";
										$putNewMdl_ex = mysqli_query($con,$putNewMdl);
									}	
								}
							}//while
						}//if
					}//if($getMdls)
				}//for
			}//if
			else
			{
				$putShp = "insert into stocks_c (shopid,userid,purpose,datetime) values('$shpId','$app_userId','Delivery','$todayDate_time')";
				$putShp_ex = mysqli_query($con,$putShp);
				if($putShp_ex)
				{
					$getStckId = "select a.id,a.Name,s.product_model from (select c.id,c.shopid,s.Name from stocks_c c,shops s where c.shopid=s.id ) a 
					left outer join sales s on a.Name=s.particulars where a.shopid='$shpId' group by s.product_model";
					$getStckId_ex = mysqli_query($con,$getStckId);
					if($getStckId_ex)
					{
						if(mysqli_num_rows($getStckId_ex)>0)
						{
							$sId = '';
							while($stckId = mysqli_fetch_array($getStckId_ex))
							{
								$sId = $stckId['id'];
								$product_model = $stckId['product_model'];
								//echo $sId.','.$product_model.'<br>';
								if($product_model!='' && $product_model!='null')
								{										
									$putStckDetail = "insert into stocks_detail_c(stockid,model,quantity) values ('$sId','$product_model','0')";
									$put_details = mysqli_query($con,$putStckDetail);	
									
								}
							}//while
							
							for($j=0;$j<$totalSz;$j++)
							{
								$chBoxUncIds1 = $splitData[$j];
								$getMdls = "select product_name,Quantity from billed_orders where Inv_no='$chBoxUncIds1'";
								$getMdlsEx = mysqli_query($con,$getMdls);
								if($getMdlsEx)
								{
									if(mysqli_num_rows($getMdlsEx)>0)
									{
										while($mdl = mysqli_fetch_array($getMdlsEx))
										{
											$mdlName = $mdl['product_name'];
											$Quantity = $mdl['Quantity'];
							    			
											$getMdlName = "select id from stocks_detail_c where stockid='$sId' and model='$mdlName'";
											$getMdlName_ex = mysqli_query($con,$getMdlName);
              								if($getMdlName_ex)
											{
												if(mysqli_num_rows($getMdlName_ex)==0)
												{
													$unmatchMdl = "insert into stocks_detail_c (stockid,model,quantity) values ('$sId','$mdlName','$Quantity') ";
													$unmatchMdl_ex = mysqli_query($con,$unmatchMdl);
												}
												else
												{
													$selectQty = "select quantity from stocks_detail_c where stockid='$sId' and model='$mdlName'";
													$selectQty_ex = mysqli_query($con,$selectQty);
													$qty_p = 0;
													if($selectQty_ex)
													{
														if(mysqli_num_rows($selectQty_ex)>0)
														{
															$qty_p = mysqli_fetch_array($selectQty_ex);
															$qty_p = $qty_p['quantity']+$Quantity;
														}
														else
															$qty_p = 0;
													}
													
													$updtQtyStocks = "update stocks_detail_c set quantity='$qty_p' where stockid='$sId' and model='$mdlName'";
													$updtQtyStocks_ex = mysqli_query($con,$updtQtyStocks);
												}
											}
										}//while
									}
								}
							}//for
						}//if
					}//if
					
				}//if
			}//else
			
			if($runStatus == 'success')
			{
				$ssql = "select primary_mobile,secondary_mobile,primary_email,secondary_email from shops where id='$shpId' and Deleted='0'";
				$exe  = mysqli_query($con,$ssql);
				$ownerInfo = mysqli_fetch_array($exe); 
				$shops = array('status'=>'success','primary_mobile'=>$ownerInfo['primary_mobile'],'secondary_mobile'=>$ownerInfo['secondary_mobile'],
								'primary_email'=>$ownerInfo['primary_email'],'secondary_email'=>$ownerInfo['secondary_email']);
				echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
			}
			else if($runStatus == 'someDataNotUpdated')
			{
				$shops = array('status'=>'someDataNotUpdated','errorInv'=>$errorInv);
				echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
			}
			else
			{
				$shops = array('status'=>'failed');
				echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		if(isset($_GET['getOwnerInfo']))
		{
			$shpId = $_GET['shpId'];
			$ssql = "select Name,primary_mobile,secondary_mobile,primary_email,secondary_email,shop_PMobile,shop_SMobile,Partner_name,fos from shops where id='$shpId' and Deleted='0'";
			$exe  = mysqli_query($con,$ssql);
			if($exe)
			{
				$ownerInfo = mysqli_fetch_array($exe); 
				$shops = array('status'=>'success','Name'=>$ownerInfo['Name'],'primary_mobile'=>$ownerInfo['primary_mobile'],
				'secondary_mobile'=>$ownerInfo['secondary_mobile'],'primary_email'=>$ownerInfo['primary_email'],'secondary_email'=>$ownerInfo['secondary_email'],
				'shop_PMobile'=>$ownerInfo['shop_PMobile'],'shop_SMobile'=>$ownerInfo['shop_SMobile'],'Partner_name'=>$ownerInfo['Partner_name'],'fos'=>$ownerInfo['fos']);
				echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
			}
			else
			{
				$shops = array('status'=>'failed');
				echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		if(isset($_GET['app_user']))
		{
			$app_user = $_GET['app_user'];
			$getRights = "select rights from app_users where id='$app_user' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$query = "select o.id,o.Inv_no,o.unique_id,s.Name,o.delivery_date from billed_orders o,shops s where o.delivery_status='1' and DATE(o.delivery_date)='$todayDate' and o.shop_id=s.id and s.Deleted='0' group by o.Inv_no";
			else
				$query = "select o.id,o.Inv_no,o.unique_id,s.Name,o.delivery_date from billed_orders o,shops s where o.user_id='$app_user' and o.delivery_status='1' and DATE(o.delivery_date)='$todayDate' and o.shop_id=s.id and s.Deleted='0' group by o.Inv_no";
			$exe = mysqli_query($con,$query);
			$cnt = mysqli_num_rows($exe);
			$ordersId = array();
			if($exe)
			{
				if($cnt>0)
				{
					while($res = mysqli_fetch_array($exe))
					{
						array_push($ordersId,array('status'=>'success','id'=>$res['id'],'Inv_no'=>$res['Inv_no'],'Name'=>$res['Name'],'unique_id'=>$res['unique_id'],'delivery_date'=>$res['delivery_date']));	
					}
					echo '{"Result":'.json_encode($ordersId,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$ordersId = array('status'=>'failed');
					echo '{"Result":'.json_encode($ordersId,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
		if(isset($_GET['getCollectedPymntShops']))
		{
			$appUserId = $_GET['appUserId'];
			$status = $_GET['status'];
			if($status=='dayWise')
				$todayDate = $_GET['dateVal'];
			$getRights = "select rights from app_users where id='$appUserId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$query = "select s.Name,a.fos_name,i.invoice_no,i.amount,i.cash_type,i.cheque_no from invoice_payment i,shops s,app_users a where i.shop_id=s.id and DATE(i.pymnt_date)='$todayDate' and a.id=i.user_id and s.Deleted='0' and a.Active=1";
			else
				$query = "select s.Name,a.fos_name,i.invoice_no,i.amount,i.cash_type,i.cheque_no from invoice_payment i,shops s,app_users a where i.shop_id=s.id and i.user_id='$appUserId' and DATE(i.pymnt_date)='$todayDate' and a.id=i.user_id and s.Deleted='0' and a.Active=1";
			$exe = mysqli_query($con,$query);
			$cnt = mysqli_num_rows($exe);
			$collectedPymntData = array();
			if($exe)
			{
				if($cnt>0)
				{		
					while($res = mysqli_fetch_array($exe))
					{					
						if($res['cash_type']=='cash')
							array_push($collectedPymntData,array('status'=>'success','Name'=>$res['Name'],'fos_name'=>$res['fos_name'],'invoice_no'=>$res['invoice_no'],'amount'=>$res['amount'],'cash_type'=>$res['cash_type']));	
						if($res['cash_type']=='cheque')
							array_push($collectedPymntData,array('status'=>'success','Name'=>$res['Name'],'fos_name'=>$res['fos_name'],'invoice_no'=>$res['invoice_no'],'amount'=>$res['amount'],'cash_type'=>$res['cash_type'],'cheque_no'=>$res['cheque_no']));
						if($res['cash_type']=='neft')
							array_push($collectedPymntData,array('status'=>'success','Name'=>$res['Name'],'fos_name'=>$res['fos_name'],'invoice_no'=>$res['invoice_no'],'amount'=>$res['amount'],'cash_type'=>$res['cash_type'],'cheque_no'=>$res['cheque_no']));	
						if($res['cash_type']=='cn')
							array_push($collectedPymntData,array('status'=>'success','Name'=>$res['Name'],'fos_name'=>$res['fos_name'],'invoice_no'=>$res['invoice_no'],'amount'=>$res['amount'],'cash_type'=>$res['cash_type'],'cheque_no'=>$res['cheque_no']));
					}
					echo '{"Result":'.json_encode($collectedPymntData,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$collectedPymntData = array('status'=>'failed');
					echo '{"Result":'.json_encode($collectedPymntData,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
		if(isset($_GET['unvisitedShp']))
		{
			$app_userId = $_GET['app_userId'];
			$status = $_GET['status'];
			$FifteenEndDate = date('Y-m-d',strtotime("-7 days"));
			$FifteenEndDateSales = date('Y-m-d',strtotime("-15 days"));
			//echo $app_user.','.$app_userId.','.$FifteenEndDate;
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($status=='notification')
			{
				if($rights['rights']=='2')
				{	
					$queryS = "select a.id, a.name,  b.attnds_date, datediff(sysdate(),ifnull(b.attnds_date,sysdate())) unvisited_days,
								c.sales_date, datediff(sysdate(),ifnull(c.sales_date,sysdate())) nobilling_days
								from (select name, id from shops where Deleted='0') as a left outer join
								(select shop_id, max(DATE(attendance_date)) as attnds_date 
									from attendance where shop_id not in 
								( select shop_id from attendance where DATE(attendance_date) between '$FifteenEndDate' and '$todayDate') 
									group by shop_id) as b
								on a.id=b.shop_id left outer join 
								(select particulars,max(sales_date) sales_date from sales 
									where particulars not in ( select particulars from sales where 
									 sales_date between '$FifteenEndDateSales' and '$todayDate'	)								
									group by particulars) as c on  a.name = c.particulars
								where not (b.shop_id is  null and c.particulars is null) order by 4 desc, 6 desc limit 3";
				}
				else
				{	
					$queryS = "select a.id, a.name,  b.attnds_date, datediff(sysdate(),ifnull(b.attnds_date,sysdate())) unvisited_days,
								c.sales_date, datediff(sysdate(),ifnull(c.sales_date,sysdate())) nobilling_days
								from (select name, id from shops where fos='$app_userId' and Deleted='0') as a left outer join
								(select shop_id, max(DATE(attendance_date)) as attnds_date 
									from attendance where shop_id not in 
								( select shop_id from attendance where DATE(attendance_date) between '$FifteenEndDate' and '$todayDate') 
									group by shop_id) as b
								on a.id=b.shop_id left outer join 
								(select particulars,max(sales_date) sales_date from sales 
									where particulars not in ( select particulars from sales where 
									 sales_date between '$FifteenEndDateSales' and '$todayDate'	)								
									group by particulars) as c on  a.name = c.particulars
								where not (b.shop_id is  null and c.particulars is null) order by 4 desc, 6 desc limit 3";
				}
			}
			if($status=='pageShow')
			{
				if($rights['rights']=='2')
				{	
					$queryS = "select a.id, a.name,  b.attnds_date, datediff(sysdate(),ifnull(b.attnds_date,sysdate())) unvisited_days,
								c.sales_date, datediff(sysdate(),ifnull(c.sales_date,sysdate())) nobilling_days
								from (select name, id from shops where Deleted='0') as a left outer join
								(select shop_id, max(DATE(attendance_date)) as attnds_date 
									from attendance where shop_id not in 
								( select shop_id from attendance where DATE(attendance_date) between '$FifteenEndDate' and '$todayDate') 
									group by shop_id) as b
								on a.id=b.shop_id left outer join 
								(select particulars,max(sales_date) sales_date from sales 
									where particulars not in ( select particulars from sales where 
									 sales_date between '$FifteenEndDateSales' and '$todayDate'	)								
									group by particulars) as c on  a.name = c.particulars
								where not (b.shop_id is  null and c.particulars is null) order by 4 desc, 6 desc";
				}
				else
				{	
					$queryS = "select a.id, a.name,  b.attnds_date, datediff(sysdate(),ifnull(b.attnds_date,sysdate())) unvisited_days,
								c.sales_date, datediff(sysdate(),ifnull(c.sales_date,sysdate())) nobilling_days
								from (select name, id from shops where fos='$app_userId' and Deleted='0') as a left outer join
								(select shop_id, max(DATE(attendance_date)) as attnds_date 
									from attendance where shop_id not in 
								( select shop_id from attendance where DATE(attendance_date) between '$FifteenEndDate' and '$todayDate') 
									group by shop_id) as b
								on a.id=b.shop_id left outer join 
								(select particulars,max(sales_date) sales_date from sales 
									where particulars not in ( select particulars from sales where 
									 sales_date between '$FifteenEndDateSales' and '$todayDate'	)								
									group by particulars) as c on  a.name = c.particulars
								where not (b.shop_id is  null and c.particulars is null) order by 4 desc, 6 desc";
				}
			}
			mysqli_query($con,"SET SQL_BIG_SELECTS=1");
			$exeS = mysqli_query($con,$queryS);
			$cntS = mysqli_num_rows($exeS);
			$unvisitesShpArr = array();
			if($exeS)
			{
				if($cntS>0)
				{
					while($uv_data = mysqli_fetch_array($exeS))
					{
						$attnds_date = $uv_data['attnds_date'];
						$sales_date = $uv_data['sales_date'];
						$unvisited_days = $uv_data['unvisited_days'];
						$nobilling_days = $uv_data['nobilling_days'];
						if($attnds_date!='')
							$attnds_date = DateTime::createFromFormat('Y-m-d', $attnds_date)->format('d-m-Y');
						if($sales_date!='')
							$sales_date = DateTime::createFromFormat('Y-m-d', $sales_date)->format('d-m-Y');
						//echo $attnds_date.'<br>';
						/*$date1 = new DateTime($attnds_date);
						$date2 = new DateTime($todayDate);		
						$diff = $date2->diff($date1)->format("%a");
						
						$date1Sales = new DateTime($sales_date);
						$date2Sales = new DateTime($todayDate);
						$diff1Sales = $date2Sales->diff($date1Sales)->format("%a");*/
						
						array_push($unvisitesShpArr,array('status'=>'success','Name'=>$uv_data['name'],'attnds_date'=>$attnds_date,
									'attnds_days'=>$unvisited_days,'sales_date'=>$sales_date,'sales_days'=>$nobilling_days));
					}
					echo '{"Result":'.json_encode($unvisitesShpArr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					array_push($unvisitesShpArr,array('status'=>'norows'));
					echo '{"Result":'.json_encode($unvisitesShpArr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				array_push($unvisitesShpArr,array('status'=>'failed'));
				echo '{"Result":'.json_encode($unvisitesShpArr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		
		if(isset($_GET['pymntColectPage']))
		{
			$appUserId = $_GET['app_userId'];
			$shpId = $_GET['shpId'];
			$yearmnth = $_GET['yearmnth'];
			$getRights = "select rights from app_users where id='$appUserId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$query = "select s.Name,a.fos_name,i.invoice_no,i.amount,i.cash_type,i.cheque_no,DATE(i.pymnt_date) as payment_date from invoice_payment i,shops s,app_users a where i.shop_id=s.id and i.shop_id='$shpId' and a.id=i.user_id and SUBSTRING_INDEX(DATE(i.pymnt_date),'-',2)='$yearmnth' and s.Deleted='0' and a.Active=1";
			else
				$query = "select s.Name,a.fos_name,i.invoice_no,i.amount,i.cash_type,i.cheque_no,DATE(i.pymnt_date) as payment_date from invoice_payment i,shops s,app_users a where i.shop_id=s.id and i.user_id='$appUserId' and i.shop_id='$shpId' and a.id=i.user_id and SUBSTRING_INDEX(DATE(i.pymnt_date),'-',2)='$yearmnth' and s.Deleted='0' and a.Active=1";
			$exe = mysqli_query($con,$query);
			$cnt = mysqli_num_rows($exe);
			$collectedPymntData = array();
			if($exe)
			{
				if($cnt>0)
				{		
					while($res = mysqli_fetch_array($exe))
					{
						if($res['payment_date']!='')
							$payment_date = DateTime::createFromFormat('Y-m-d', $res['payment_date'])->format('d-m');
						
						if($res['cash_type']=='cash')
							array_push($collectedPymntData,array('status'=>'success','Name'=>$res['Name'],'fos_name'=>$res['fos_name'],'invoice_no'=>$res['invoice_no'],'amount'=>$res['amount'],'cash_type'=>$res['cash_type'],'payment_date'=>$payment_date));	
						if($res['cash_type']=='cheque')
							array_push($collectedPymntData,array('status'=>'success','Name'=>$res['Name'],'fos_name'=>$res['fos_name'],'invoice_no'=>$res['invoice_no'],'amount'=>$res['amount'],'cash_type'=>$res['cash_type'],'cheque_no'=>$res['cheque_no'],'payment_date'=>$payment_date));
						if($res['cash_type']=='neft')
							array_push($collectedPymntData,array('status'=>'success','Name'=>$res['Name'],'fos_name'=>$res['fos_name'],'invoice_no'=>$res['invoice_no'],'amount'=>$res['amount'],'cash_type'=>$res['cash_type'],'cheque_no'=>$res['cheque_no'],'payment_date'=>$payment_date));	
						if($res['cash_type']=='cn')
							array_push($collectedPymntData,array('status'=>'success','Name'=>$res['Name'],'fos_name'=>$res['fos_name'],'invoice_no'=>$res['invoice_no'],'amount'=>$res['amount'],'cash_type'=>$res['cash_type'],'cheque_no'=>$res['cheque_no'],'payment_date'=>$payment_date));
					}
					echo '{"Result":'.json_encode($collectedPymntData,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$collectedPymntData = array('status'=>'failed');
					echo '{"Result":'.json_encode($collectedPymntData,JSON_UNESCAPED_SLASHES).'}';
				}
			}
		}
	}
?>