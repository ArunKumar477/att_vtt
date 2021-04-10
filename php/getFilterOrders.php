<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$currDate = date("Y-m-d");
	if(isset($_GET['orderwise']) || isset($_GET['modelwise']))
	{
		$app_userId = $_GET['app_user'];
		$currDate = $_GET['currDate'];
		if($currDate=='')
			$currDate = date("Y-m-d");
	}
	if(isset($_GET['request_ords']))
	{
		$app_userId = $_GET['app_userId'];
		$uniqueOrd_id = $_GET['uniqueOrd_id'];
		$shop_id = $_GET['shop_id'];
		$currDate = $_GET['currDate'];
		if($currDate=='')
			$currDate = date("Y-m-d");
	}
	if(isset($_GET['modelwiseclick']))
	{
		$app_userId = $_GET['app_userId'];
		$product_name = $_GET['product_name'];
		$currDate = $_GET['curr_date'];
		if($currDate=='')
			$currDate = date("Y-m-d");
	}
	if($con)
	{
		if(isset($_GET['orderwise']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$sql = "select distinct o.unique_id,s.Name,o.shop_id from orders o,shops s where o.shop_id=s.id and DATE(o.order_date) = '$currDate' and s.Deleted='0' group by o.unique_id";
			else
				$sql = "select distinct o.unique_id,s.Name,o.shop_id from orders o,shops s where o.user_id='$app_userId' and o.shop_id=s.id and DATE(o.order_date) = '$currDate' and s.Deleted='0' group by o.unique_id";
		}
		if(isset($_GET['modelwise']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$sql = "select distinct product_name,sum(Quantity) as Quantity from orders where DATE(order_date) = '$currDate' group by product_name";
			else
				$sql = "select distinct product_name,sum(Quantity) as Quantity from orders where user_id='$app_userId' and DATE(order_date) = '$currDate' group by product_name";
		}
		if(isset($_GET['request_ords']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$sql = "select id,product_name,Quantity,color from orders where shop_id='$shop_id' and unique_id='$uniqueOrd_id' and DATE(order_date) = '$currDate'";
			else
				$sql = "select id,product_name,Quantity,color from orders where user_id='$app_userId' and shop_id='$shop_id' and unique_id='$uniqueOrd_id' and DATE(order_date) = '$currDate'";
		}
		if(isset($_GET['modelwiseclick']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$sql = "select o.id,s.Name,o.color,o.Quantity from orders o,shops s where o.shop_id=s.id and o.product_name='$product_name' and DATE(o.order_date) = '$currDate' and s.Deleted='0'";
			else
				$sql = "select o.id,s.Name,o.color,o.Quantity from orders o,shops s where o.shop_id=s.id and o.product_name='$product_name' and o.user_id='$app_userId' and DATE(o.order_date) = '$currDate' and s.Deleted='0'";
		}
		
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		$ordersId = array();
		if($ex)
		{
			if($cnt>0)
			{
				while($res= mysqli_fetch_array($ex))
				{ 
					if(isset($_GET['orderwise']))
						array_push($ordersId,array('status'=>'success','shop_id'=>$res['shop_id'],'Name'=>$res['Name'],'unique_id'=>$res['unique_id']));
					if(isset($_GET['modelwise']))
						array_push($ordersId,array('status'=>'success','product_name'=>$res['product_name'],'Quantity'=>$res['Quantity']));
					if(isset($_GET['request_ords']))
						array_push($ordersId,array('status'=>'success','product_name'=>$res['product_name'],'Quantity'=>$res['Quantity'],'color'=>$res['color']));
					if(isset($_GET['modelwiseclick']))
						array_push($ordersId,array('status'=>'success','Name'=>$res['Name'],'color'=>$res['color'],'Quantity'=>$res['Quantity']));
				}
				echo '{"Result":'.json_encode($ordersId,JSON_UNESCAPED_SLASHES).'}';
			}
			else
			{
				$ordersId = array('status'=>'norows');
				echo '{"Result":'.json_encode($ordersId,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		else
		{
			$ordersId = array('status'=>'failed');
			echo '{"Result":'.json_encode($ordersId,JSON_UNESCAPED_SLASHES).'}';
		}
	}
?>