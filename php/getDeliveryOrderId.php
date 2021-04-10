<?php
	//require_once('config_s.php');
	require_once('config.php');
	$currDate = date("Y-m-d");
	/* Order Delivery script start*/
		if(isset($_GET['srchShopName']))
		{	
			$app_userId = $_GET['app_userId'];
			$srchShopName = $_GET['srchShopName'];
		}
		if(isset($_GET['unique_id']))
		{
			$app_userId = $_GET['app_userId'];
			$unique_id = $_GET['unique_id'];
			$shop_id = $_GET['shop_id'];
		}
	/* Order Delivery script end*/
	
	/* Orders Report script start*/
		if(isset($_GET['req']))
		{
			$app_userId = $_GET['app_userId'];
			$currDate = $_GET['currDate'];
			$date2 = $_GET['date2'];
		}
		if(isset($_GET['request']))
		{
			$app_userId = $_GET['app_userId'];
			$unique_id = $_GET['uniqueOrd_id'];
			$shop_id = $_GET['shop_id'];
			$days = $_GET['days'];
			$minusDateClick = date('Y-m-d', strtotime('-'.$days.' days'));
		}
		if(isset($_GET['modelwise']))
		{
			$app_userId = $_GET['app_userId'];
			$currDate = $_GET['currDate'];
			$date2 = $_GET['date2'];
		}
		if(isset($_GET['modelwiseclick']))
		{
			$app_userId = $_GET['app_userId'];
			$product_name = $_GET['product_name'];
			$color = $_GET['color'];
			$curr_date = $_GET['curr_date'];
			$date2 = $_GET['date2'];
		}
		if(isset($_GET['modelwiseclick2']))
		{
			$app_userId = $_GET['app_userId'];
			$product_model = $_GET['product_model'];
			//$color = $_GET['color'];
			$days = $_GET['days'];
			$modelClickMinus = date('Y-m-d', strtotime('-'.$days.' days'));
		}
		if(isset($_GET['orderwiseradio']))
		{
			$app_userId = $_GET['app_userId'];
			$days = $_GET['days'];
			$minusDate = date('Y-m-d', strtotime('-'.$days.' days'));
		}
		if(isset($_GET['modelwiseradio']))	
		{
			$app_userId = $_GET['app_userId'];
			$days = $_GET['days'];
			$modelMinus = date('Y-m-d', strtotime('-'.$days.' days'));
		}	
		if(isset($_GET['OrderWiseMonth']))
		{
			$app_userId = $_GET['app_userId'];
			$currDate = $_GET['currDate'];
			$date2 = $_GET['date2'];
		}
		if(isset($_GET['ModelWiseMonth']))
		{
			$app_userId = $_GET['app_userId'];
			$currDate = $_GET['currDate'];
			$date2 = $_GET['date2'];
		}
		if(isset($_GET['monthWiseOrdersClick']))
		{
			$app_userId = $_GET['app_userId'];
			$unique_id = $_GET['uniqueOrd_id'];
			$shop_id = $_GET['shop_id'];
			$date2 = $_GET['monthYear'];
		}
		if(isset($_GET['monthWiseModelsClick']))
		{
			$app_userId = $_GET['app_userId'];
			$product_model = $_GET['product_model'];
			//$color = $_GET['color'];
			$date2 = $_GET['monthYear'];
		}
	/* Orders Report script end*/
	
	if($con)
	{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			
			if(isset($_GET['srchShopName']))
			{
				if($rights['rights']=='2')
					$sql = "select distinct unique_id,id,Inv_no,shop_id from billed_orders where delivery_status='0' and shop_id='$srchShopName' and deleted='0' and billed_status='1' group by unique_id";
				else
					$sql = "select distinct unique_id,id,Inv_no,shop_id from billed_orders where delivery_status='0' and shop_id='$srchShopName' and deleted='0' and billed_status='1' group by unique_id";
			}
			if(isset($_GET['unique_id']))
			{
				if($rights['rights']=='2')
					$sql = "select id,product_name,product_type,color,Quantity,unique_id from orders where delivery_status='0' and shop_id='$shop_id' and unique_id='$unique_id'";
				else
					$sql = "select id,product_name,product_type,color,Quantity,unique_id from orders where delivery_status='0' and user_id='$app_userId' and shop_id='$shop_id' and unique_id='$unique_id'";	
			}
			
			if(isset($_GET['req']))
			{
				if($rights['rights']=='2')
					$sql = "select distinct a.vch_no,a.particulars,b.id,sum(a.debit_amount) as debit_amount from sales a,shops b where a.particulars=b.Name and b.Deleted='0' group by a.vch_no order by a.vch_no,a.particulars";
				else
					$sql = "select distinct a.vch_no,a.particulars,b.id,sum(a.debit_amount) as debit_amount from sales a,shops b where a.particulars=b.Name and b.fos='$app_userId' and b.Deleted='0' group by a.vch_no order by a.vch_no,a.particulars";
			}
			if(isset($_GET['request']))
			{
				if($rights['rights']=='2')
					$sql = "select s.sales_date,s.product_model,s.color,sum(s.qty) as qty,sum(s.debit_amount) as value from sales s,shops sh where sh.id='$shop_id' and s.particulars=sh.Name and s.sales_date between '$minusDateClick' and '$currDate' and sh.Deleted='0' group by s.color,s.product_model order by s.product_model";
				else
					$sql = "select s.sales_date,s.product_model,s.color,sum(s.qty) as qty,sum(s.debit_amount) as value from sales s,shops sh where sh.fos='$app_userId' and sh.id='$shop_id' and s.particulars=sh.Name and s.sales_date between '$minusDateClick' and '$currDate' and sh.Deleted='0' group by s.color,s.product_model order by s.product_model";
			}
			if(isset($_GET['modelwise']))
			{
				if($rights['rights']=='2')
					$sql = "select distinct a.model_name,a.particulars,b.product_model,sum(a.qty) as Quantity,b.color,sum(a.debit_amount) as debit_amount from sales a,product_master b,shops c where a.model_name=b.model and a.particulars=c.Name and c.Deleted='0' group by a.model_name order by a.model_name";
				else
					$sql = "select distinct a.model_name,a.particulars,b.product_model,sum(a.qty) as Quantity,b.color,sum(a.debit_amount) as debit_amount from sales a,product_master b,shops c where a.model_name=b.model and a.particulars=c.Name and c.fos='$app_userId' and c.Deleted='0' group by a.model_name order by a.model_name";
			}
			if(isset($_GET['modelwiseclick']))
			{
				if($rights['rights']=='2')
					$sql = "select distinct a.model_name,a.particulars,sum(a.qty) as Quantity,a.sales_date,sum(a.debit_amount) as debit_amount from sales a,product_master b,shops c where b.product_model='$product_name' and b.color='$color' and a.model_name=b.model and a.particulars=c.Name and c.Deleted='0' group by a.particulars order by sum(a.qty) desc";
				else	
					$sql = "select distinct a.model_name,a.particulars,sum(a.qty) as Quantity,a.sales_date,sum(a.debit_amount) as debit_amount from sales a,product_master b,shops c where b.product_model='$product_name' and b.color='$color' and a.model_name=b.model and a.particulars=c.Name and c.fos='$app_userId' and c.Deleted='0' group by a.particulars order by sum(a.qty) desc";
			}
			if(isset($_GET['modelwiseclick2']))
			{
				if($rights['rights']=='2')
					$sql = "select distinct a.product_model,a.particulars,sum(a.qty) as qty,a.sales_date,sum(a.debit_amount) as value from sales a,shops c where a.product_model='$product_model' and a.particulars=c.Name and a.sales_date between '$modelClickMinus' and '$currDate' and c.Deleted='0' group by a.particulars order by sum(a.qty) desc";
				else
					$sql = "select distinct a.product_model,a.particulars,sum(a.qty) as qty,a.sales_date,sum(a.debit_amount) as value from sales a,shops c where a.product_model='$product_model' and a.particulars=c.Name and c.fos='$app_userId' and a.sales_date between '$modelClickMinus' and '$currDate' and c.Deleted='0' group by a.particulars order by sum(a.qty) desc";
			}
			if(isset($_GET['orderwiseradio']))
			{
				if($rights['rights']=='2')	
					$sql = "SELECT distinct s.particulars,s.sales_date,sum(s.debit_amount) as debit_amount,sh.id FROM sales s,shops sh where s.particulars=sh.Name and s.sales_date between '$minusDate' and '$currDate' and sh.Deleted='0' group by s.particulars";
				else
					$sql = "SELECT distinct s.particulars,s.sales_date,sum(s.debit_amount) as debit_amount,sh.id FROM sales s,shops sh where s.particulars=sh.Name and sh.fos='$app_userId' and s.sales_date between '$minusDate' and '$currDate' and sh.Deleted='0' group by s.particulars";
			}
			if(isset($_GET['modelwiseradio']))
			{
				if($rights['rights']=='2')
					$sql = "select distinct a.product_model,a.sales_date,sum(a.qty) as qty,sum(a.debit_amount) as value from sales a,shops c where a.particulars=c.Name and a.sales_date between '$modelMinus' and '$currDate' and c.Deleted='0' group by a.product_model order by a.model_name";
				else
					$sql = "select distinct a.product_model,a.sales_date,sum(a.qty) as qty,sum(a.debit_amount) as value from sales a,shops c where a.particulars=c.Name and c.fos='$app_userId' and a.sales_date between '$modelMinus' and '$currDate' and c.Deleted='0' group by a.product_model order by a.model_name";
			}
			
			if(isset($_GET['OrderWiseMonth']))
			{
				$split = explode("-",$date2);
				if(date("m")<$split[1])
					$date2 = date("Y",strtotime("-1 year")).'-'.$split[1];
				
				if($rights['rights']=='2')
					$sql = "SELECT distinct s.particulars,s.sales_date,sum(s.debit_amount) as debit_amount,sh.id FROM sales s,shops sh where s.particulars=sh.Name and SUBSTRING(sales_date,'1','7')='$date2' and sh.Deleted='0' group by s.particulars";
				else
					$sql = "SELECT distinct s.particulars,s.sales_date,sum(s.debit_amount) as debit_amount,sh.id FROM sales s,shops sh where s.particulars=sh.Name and sh.fos='$app_userId' and SUBSTRING(sales_date,'1','7')='$date2' and sh.Deleted='0' group by s.particulars";
			}
			if(isset($_GET['ModelWiseMonth']))		
			{
				$split = explode("-",$date2);
				if(date("m")<$split[1])
					$date2 = date("Y",strtotime("-1 year")).'-'.$split[1];

				if($rights['rights']=='2')
					$sql = "select distinct a.product_model,a.sales_date,sum(a.qty) as qty,sum(a.debit_amount) as value from sales a,shops c where a.particulars=c.Name and SUBSTRING_INDEX(a.sales_date,'-',2)='$date2' and c.Deleted='0' group by a.product_model order by a.model_name";
				else
					$sql = "select distinct a.product_model,a.sales_date,sum(a.qty) as qty,sum(a.debit_amount) as value from sales a,shops c where a.particulars=c.Name and c.fos='$app_userId' and SUBSTRING_INDEX(a.sales_date,'-',2)='$date2' and c.Deleted='0' group by a.product_model order by a.model_name";
			}
			
			if(isset($_GET['monthWiseOrdersClick']))	
			{
				$split = explode("-",$date2);
				if(date("m")<$split[1])
					$date2 = date("Y",strtotime("-1 year")).'-'.$split[1];

				if($rights['rights']=='2')
					$sql = "select s.sales_date,s.product_model,s.color,sum(s.qty) as qty,sum(s.debit_amount) as value from sales s,shops sh where  sh.id='$shop_id' and s.particulars=sh.Name and SUBSTRING_INDEX(s.sales_date,'-','2')='$date2' and sh.Deleted='0' group by s.color,s.product_model order by s.product_model";
				else
					$sql = "select s.sales_date,s.product_model,s.color,sum(s.qty) as qty,sum(s.debit_amount) as value from sales s,shops sh where sh.fos='$app_userId' and sh.id='$shop_id' and s.particulars=sh.Name and SUBSTRING_INDEX(s.sales_date,'-','2')='$date2' and sh.Deleted='0' group by s.color,s.product_model order by s.product_model";
			}
			if(isset($_GET['monthWiseModelsClick']))
			{
				$split = explode("-",$date2);
				if(date("m")<$split[1])
					$date2 = date("Y",strtotime("-1 year")).'-'.$split[1];

				if($rights['rights']=='2')
					$sql = "select distinct a.product_model,a.particulars,sum(a.qty) as qty,a.sales_date,sum(a.debit_amount) as value from sales a,shops c where a.product_model='$product_model' and a.particulars=c.Name and SUBSTRING_INDEX(a.sales_date,'-',2)='$date2' and c.Deleted='0' group by a.particulars order by sum(a.qty) desc";
				else
					$sql = "select distinct a.product_model,a.particulars,sum(a.qty) as qty,a.sales_date,sum(a.debit_amount) as value from sales a,shops c where a.product_model='$product_model' and a.particulars=c.Name and c.fos='$app_userId' and SUBSTRING_INDEX(a.sales_date,'-',2)='$date2' and c.Deleted='0' group by a.particulars order by sum(a.qty) desc";
			}
			
			$ex  = mysqli_query($con,$sql);
			$cnt = mysqli_num_rows($ex);
			$arr = array();
			if($cnt>0)
			{
				if($ex)
				{
					while($res=mysqli_fetch_array($ex))
					{
						if(isset($_GET['srchShopName']))
						{
							array_push($arr,array('status'=>'success','id'=>$res['id'],'unique_id'=>$res['unique_id'],'Inv_no'=>$res['Inv_no'],'shop_id'=>$res['shop_id']));
						}
						if(isset($_GET['unique_id']))
						{
							array_push($arr,array('status'=>'success','id'=>$res['id'],'product_name'=>$res['product_name'],'Quantity'=>$res['Quantity'],'unique_id'=>$res['unique_id'],'product_type'=>$res['product_type'],'color'=>$res['color']));						
						}
						
						
						if(isset($_GET['req']))
							array_push($arr,array('status'=>'success','shop_id'=>$res['id'],'Name'=>$res['particulars'],'unique_id'=>$res['vch_no'],'debit_amount'=>$res['debit_amount']));
						if(isset($_GET['request']))
							array_push($arr,array('status'=>'success','product_model'=>$res['product_model'],'Quantity'=>$res['qty'],'totalQty'=>$res['qty'],'color'=>$res['color'],'value'=>$res['value']));
						if(isset($_GET['modelwise']))
							array_push($arr,array('status'=>'success','product_name'=>$res['product_model'],'Quantity'=>$res['Quantity'],'color'=>$res['color'],'debit_amount'=>$res['debit_amount']));
						if(isset($_GET['modelwiseclick']))
							array_push($arr,array('status'=>'success','Name'=>$res['particulars'],'Quantity'=>$res['Quantity'],'debit_amount'=>$res['debit_amount']));
						if(isset($_GET['modelwiseclick2']))
						{
							array_push($arr,array('status'=>'success','Name'=>$res['particulars'],'qty'=>$res['qty'],'value'=>$res['value']));
						}
						if(isset($_GET['orderwiseradio']))
						{
							array_push($arr,array('status'=>'success','shop_id'=>$res['id'],'Name'=>$res['particulars'],'debit_amount'=>$res['debit_amount']));
						}
						if(isset($_GET['modelwiseradio']))
						{
							array_push($arr,array('status'=>'success','product_model'=>$res['product_model'],'qty'=>$res['qty'],'value'=>$res['value']));
						}
						
						if(isset($_GET['OrderWiseMonth']))
							array_push($arr,array('status'=>'success','shop_id'=>$res['id'],'Name'=>$res['particulars'],'debit_amount'=>$res['debit_amount']));
						if(isset($_GET['ModelWiseMonth']))
							array_push($arr,array('status'=>'success','product_model'=>$res['product_model'],'qty'=>$res['qty'],'value'=>$res['value']));
						if(isset($_GET['monthWiseOrdersClick']))
							array_push($arr,array('status'=>'success','product_model'=>$res['product_model'],'Quantity'=>$res['qty'],'totalQty'=>$res['qty'],'color'=>$res['color'],'value'=>$res['value']));
						if(isset($_GET['monthWiseModelsClick']))
							array_push($arr,array('status'=>'success','Name'=>$res['particulars'],'qty'=>$res['qty'],'value'=>$res['value']));
					}
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					$arr = array('status'=>'failed');
					echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			else
			{
				$arr = array('status'=>'norows');
				echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
	}
?>