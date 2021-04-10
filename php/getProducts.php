<?php
	//require_once('config_s.php');
	require_once('config.php');
	$stDate  = date("Y-m-01");
	$endDate = date("Y-m-t");
	
	if(isset($_GET['getModels']))
		$prdctTypeBtn = $_GET['prdctTypeBtn'];
	if(isset($_GET['req']))
	{
		$prdctTypeBtn = $_GET['prdctTypeBtn'];
		$prdct_model = $_GET['prdct_model'];
	}
	if(isset($_GET['getquantity']))
	{
		$prdct_Type = $_GET['prdct_Type'];
		$prdct_name = $_GET['prdct_name'];
		$prdct_color = $_GET['prdct_color'];
		if(isset($_GET['shpId']))
			$shpId = $_GET['shpId'];
		if(isset($_GET['fosId']))
			$fosId = $_GET['fosId'];
	}
	if(isset($_GET['getAllData']))
	{
		$uId = $_GET['uId'];
	}
	
	if($con)
	{
			if(isset($_GET['getModels']))
				$sql = "select DISTINCT product_model,round(dp)as dp from products where product_name='$prdctTypeBtn' group by product_model";
			if(isset($_GET['req']))
				$sql = "select DISTINCT color from products where product_name='$prdctTypeBtn' and product_model='$prdct_model'";
			if(isset($_GET['getquantity']))
			{	
				if($prdct_color=='no color')
					$sql = "select quantity from products where product_name='$prdct_Type' and product_model='$prdct_name' and color=''";
				else
					$sql = "select quantity from products where product_name='$prdct_Type' and product_model='$prdct_name' and color='$prdct_color'";
			}
			if(isset($_GET['getAllProducts']))
				$sql = "select distinct(product_name) from products";
			if(isset($_GET['getAllData']))
				//$sql = "select * from products where product_name='$uId'";	
				$sql = "select p.id,p.model,p.product_model,p.color,p.dp,p.mop,p.mrp,p.quantity,b.sum FROM products p left outer join (select product_model ,sum(quantity) as sum from products b group by product_model) as b on p.product_model=b.product_model where p.product_name='$uId'";
			if(isset($_GET['getAllProducts']))
				$sql = "select distinct product_name from products";
							
			$ex  = mysqli_query($con,$sql);
			$cnt = mysqli_num_rows($ex);
			$arr = array();
			if($cnt>0)
			{
				if($ex)
				{
					if(isset($_GET['getquantity']))
					{
						$target = 'empty';
						$achievement = 'empty';
						if($prdct_name=='2 DS' || $prdct_name=='3 DS' || $prdct_name=='5 DS' || $prdct_name=='6 DS' || $prdct_name=='8 DS')
						{
							if($prdct_name=='2 DS')
							{
								$query = "select 2DS_target as target from shops where fos='$fosId' and id='$shpId' and Deleted='0'";	
								$exe = mysqli_query($con,$query);
							}
							if($prdct_name=='3 DS')
							{
								$query = "select target as target from shops where fos='$fosId' and id='$shpId' and Deleted='0'";
								$exe = mysqli_query($con,$query);
							}
							if($prdct_name=='5 DS')
							{
								$query = "select 5DS_target as target from shops where fos='$fosId' and id='$shpId' and Deleted='0'";
								$exe = mysqli_query($con,$query);
							}
							if($prdct_name=='6 DS')
							{
								$query = "select 6DS_target as target from shops where fos='$fosId' and id='$shpId' and Deleted='0'";
								$exe = mysqli_query($con,$query);
							}
							if($prdct_name=='8 DS')		
							{
								$query = "select 8DS_target as target from shops where fos='$fosId' and id='$shpId' and Deleted='0'";
								$exe = mysqli_query($con,$query);
							}
							
							if($exe)
							{
								if(mysqli_num_rows($exe)==1)
								{
									$res_q = mysqli_fetch_array($exe);
									$target = $res_q['target'];
								}
								else
									$target = 'empty';
							}
							$query1 = "select sum(qty) as achievement from sales s,shops sh where s.particulars=sh.Name and sh.fos='$fosId' 
							and sh.id='$shpId' and s.product_model='$prdct_name' and sh.Deleted='0' and s.sales_date between '$stDate' and '$endDate' group by s.product_model";
							$exe1 = mysqli_query($con,$query1);
							if($exe1)
							{
								if(mysqli_num_rows($exe1)==1)
								{
									$res_a = mysqli_fetch_array($exe1);
									$achievement = $res_a['achievement'];
								}
								else
									$achievement = 'empty';
							}
						}
					}
					while($res=mysqli_fetch_array($ex))
					{
						if(isset($_GET['getModels']))
							array_push($arr,array('product_name'=>$res['product_model'],'dp'=>$res['dp']));
						if(isset($_GET['req']))
							array_push($arr,array('color'=>$res['color']));
						if(isset($_GET['getquantity']))
						{
							$arr = array('quantity'=>$res['quantity'],'target'=>$target,'achievement'=>$achievement);
						}
						if(isset($_GET['getAllProducts']))
							array_push($arr,array('product_name'=>$res['product_name']));
						if(isset($_GET['getAllData']))	
							array_push($arr,array('id'=>$res['id'],'product_model'=>$res['product_model'],'color'=>$res['color'],'dp'=>$res['dp'],'mop'=>$res['mop'],'mrp'=>$res['mrp'],'quantity'=>$res['quantity'],'model'=>$res['model'],'ttlQty'=>$res['sum']));
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
				$arr = array('status'=>'error');
				echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
			}
	}
?>