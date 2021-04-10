<?php 
	class class_file
	{
		function getDelvrdUnDelvrdInfo($app_userId,$con)
		{
			$billed = 1;$del = 0;
			$query = $con->prepare("select b.shop_id,s.Name,s.Area,b.Inv_no,b.delivery_status from billed_orders b left outer join shops s on b.shop_id=s.id where b.billed_status=? and s.Deleted=? and b.delivery_date=curdate() group by b.Inv_no");
			$query->bind_param("ii",$billed,$del);
			$query->execute();
			$result = $query->get_result();
			if($result->num_rows == 0) exit('No rows');
			$arr = array();
			while($row = $result->fetch_assoc())
				array_push($arr,array('shop_id'=>$row['shop_id'],'ShpName'=>$row['Name'],'Area' => $row['Area'],'Inv_no' => $row['Inv_no'],'delivery_status' => $row['delivery_status']));
			//var_export($arr);
			$result->close();
			return $arr;
		}
		
		function getAllProductModel($con)
		{
			$product_name = 'Nokia';
			$query = $con->prepare("select product_category,product_model,color from product_master where product_name=? group by product_model,color");
			$query->bind_param("s",$product_name);
			$query->execute();
			$result = $query->get_result();
			if($result->num_rows == 0) exit('No rows');
			$arr = array();
			$todayStocksAll = array();
			$product_category = array();
			$totalArr = array();
			while($row = $result->fetch_assoc())
				array_push($arr,array('product_model'=>$row['product_model'],'color'=>$row['color'],'product_category'=>$row['product_category']));
			/* get all stocks today */
			$query1 = $con->prepare("select * from closing_stocks_c where Date(created)=curdate()");
			$query1->execute();
			$result1 = $query1->get_result();
			if($result1->num_rows > 0)
			{
				while($row1 = $result1->fetch_assoc())
					array_push($todayStocksAll,array('status'=>'success','product_model'=>$row1['product_model'],'color'=>$row1['color'],'quantity'=>$row1['quantity']));
			}
			else
				array_push($todayStocksAll,array('status'=>'no_rows'));
			/* get all stocks today end */
			
			/* get product_category */
			$query2 = $con->prepare("select distinct product_category from product_master where product_category!=''");
			$query2->execute();
			$result2 = $query2->get_result();
			if($result2->num_rows > 0)
			{
				while($row2 = $result2->fetch_assoc())
					array_push($product_category,array('status'=>'success','product_category'=>$row2['product_category']));
			}
			else
				array_push($product_category,array('status'=>'no_rows'));
			/* get product_category End */	
			$totalArr = array('allMdls'=>$arr,'todayStocksAll'=>$todayStocksAll,'product_categoryArr'=>$product_category);
			$result->close();
			return $totalArr;
		}
		
		function setAllClosingStocks($con,$jsonData,$user_id)
		{
			$todayDate = date("Y-m-d H:i:s");
			$status = 'success';
			$query = $con->prepare("insert into closing_stocks_h (user_id,product_model,color,quantity,totalQty,created) 
			select user_id,product_model,color,quantity,totalQty,created from closing_stocks_c");
			$query->execute();
			if($query)
			{
				$delQuery = $con->prepare("delete from closing_stocks_c");
				$delQuery->execute();
			}
			foreach($jsonData as $i)
			{
				$product_model = $i['product_model'];
				$color = $i['color'];
				$qty = $i['qty'];
				$totalQty = $i['totalQty'];
				
				$query_set = $con->prepare("insert into closing_stocks_c (user_id,product_model,color,quantity,totalQty,created) values(?,?,?,?,?,?)");
				$query_set->bind_param("isssis",$user_id,$product_model,$color,$qty,$totalQty,$todayDate);
				$query_set->execute();
				if(!$query_set)
					$status = 'failed';
				$query_set->close();
			}
			$resArr = array('status'=>$status);
			return $resArr;
		}
	}
?>