<?php 
	class class_file
	{
		function getDelvrdUnDelvrdInfo($app_userId,$con)
		{
			$query = mysqli_query($con,"select b.shop_id,s.Name,s.Area,b.Inv_no,b.delivery_status from billed_orders b left outer join shops s on b.shop_id=s.id where b.billed_status='1' and s.Deleted='0' and b.delivery_date=curdate() group by b.Inv_no");
			if(mysqli_num_rows($query) == 0) exit('No rows');
			$arr = array();
			while($row = mysqli_fetch_assoc($query))
				array_push($arr,array('shop_id'=>$row['shop_id'],'ShpName'=>$row['Name'],'Area' => $row['Area'],'Inv_no' => $row['Inv_no'],'delivery_status' => $row['delivery_status']));
			return $arr;
		}
		
		function getAllProductModel($con)
		{
			$query = mysqli_query($con,"select product_category,product_model,color from product_master where product_name='Nokia' group by product_model,color");
			if(mysqli_num_rows($query) == 0) exit('No rows');
			$arr = array();
			$todayStocksAll = array();
			$product_category = array();
			$totalArr = array();
			while($row = mysqli_fetch_assoc($query))
				array_push($arr,array('product_model'=>$row['product_model'],'color'=>$row['color'],'product_category'=>$row['product_category']));
			/* get all stocks today */
			$query1 = mysqli_query($con,"select * from closing_stocks_c where Date(created)=curdate()");
			if(mysqli_num_rows($query1) > 0)
			{
				while($row1 = mysqli_fetch_assoc($query1))
					array_push($todayStocksAll,array('status'=>'success','product_model'=>$row1['product_model'],'color'=>$row1['color'],'quantity'=>$row1['quantity'],'verified_by'=>$row1['verified_by']));
			}
			else
				array_push($todayStocksAll,array('status'=>'no_rows'));
			/* get all stocks today end */
			
			/* get product_category */
			$query2 = mysqli_query($con,"select distinct product_category from product_master where product_category!=''");
			if(mysqli_num_rows($query2) > 0)
			{
				while($row2 = mysqli_fetch_assoc($query2))
					array_push($product_category,array('status'=>'success','product_category'=>$row2['product_category']));
			}
			else
				array_push($product_category,array('status'=>'no_rows'));
			/* get product_category End */	
			$totalArr = array('allMdls'=>$arr,'todayStocksAll'=>$todayStocksAll,'product_categoryArr'=>$product_category);
			return $totalArr;
		}
		
		function setAllClosingStocks($con,$jsonData,$user_id)
		{
			$todayDate = date("Y-m-d H:i:s");
			$status = 'success';
			$query = mysqli_query($con,"insert into closing_stocks_h (user_id,product_model,color,quantity,totalQty,created) 
			select user_id,product_model,color,quantity,totalQty,created from closing_stocks_c");
			if($query)
			{
				$delQuery = mysqli_query($con,"delete from closing_stocks_c");
			}
			foreach($jsonData as $i)
			{
				$product_model = $i['product_model'];
				$color = $i['color'];
				$qty = $i['qty'];
				$totalQty = $i['totalQty'];
				
				$query_set = mysqli_query($con,"insert into closing_stocks_c (user_id,product_model,color,quantity,totalQty,created)
												 values('$user_id','$product_model','$color','$qty','$totalQty','$todayDate')");
				if(!$query_set)
					$status = 'failed';
			}
			$resArr = array('status'=>$status);
			return $resArr;
		}
	}
?>