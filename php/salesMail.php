<?php 
	error_reporting(E_ERROR);
	//require_once('config_s.php');
	require_once('config.php');
	$currDate = date("Y-m-d");
	$st_date = date('Y-m-01');
	$ed_date = date('Y-m-t');
	if($con)
	{
		$sql = "select distinct(a.email),a.id,a.fos_name from app_users a,sales s,shops h where s.particulars=h.Name and h.fos=a.id and s.sales_date between '$st_date' and '$ed_date' and h.Deleted='0' and a.Active=1 group by a.email";
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		if($ex)
		{
			while($eachEmail = mysqli_fetch_array($ex))
			{
			$eachId = $eachEmail['id'];
			$email = $eachEmail['email'];
			$fos_name = $eachEmail['fos_name'];
			$gradTotal = 0;
			$grandFinalAmt = 0;
			$msg 	  = '<p style="color:green"><strong>SALES REPORT - '.$fos_name.'&nbsp;( '.$email.' )</strong></p>';
			if($cnt>0)
			{
				$msg     .= '<table border="1" width="100%" style="border-collapse:collapse;padding:10px;border-collapse:collapse;padding:10px;">'; 
				$msg 	 .= '<tr style="background:aliceblue;"><th style="padding:10px;">Dealer Name</th>';
				//$equery = "select distinct s.product_model from sales s left outer join shops sh on s.particulars=sh.name where sh.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date'";
				//$equery = "select distinct s.product_model,sum(s.qty) as qty from sales s left outer join shops sh on s.particulars=sh.name where sh.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date' group by s.qty,s.product_model order by qty asc";
				$equery = "select distinct a.product_model,a.qty,round(p.dp) as dp from (select distinct s.product_model,sum(s.qty) as qty from sales s left outer join shops sh on s.particulars=sh.name where sh.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date' group by s.qty,s.product_model) a left outer join product_master p on a.product_model=p.product_model where dp!=0 group by p.product_model order by dp";	
				$eexe = mysqli_query($con,$equery);
				$ecnt = mysqli_num_rows($eexe);
				$mdlShpQtyArr = array();
				if($eexe)
				{
					if($ecnt>0)
					{
						while($eres = mysqli_fetch_array($eexe))
						{
							$msg .= '<th>'.$eres["product_model"].'</th>';
						}
					}
				}
				$msg 	 .= '<th>Total Qty</th><th>Amount</th><th>Target</th></tr>';				
				
				$query = "select distinct(s.particulars) from sales s,shops h,app_users a where s.particulars=h.Name and h.fos=a.id and h.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date' and h.Deleted='0' and a.Active=1 group by s.particulars";
				$exe = mysqli_query($con,$query);
				
				$rSql1 = "select * from (select shops.id,shops.name as particulars,sales.product_model,sum(sales.qty) as qty from shops inner join sales on 
				shops.name=sales.particulars where shops.fos='$eachId' and (sales.sales_date BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate()) and 
				sales.product_model!='' group by shops.Name,sales.product_model 
				union 
				(SELECT shops.id,shops.name as particulars,null,0 from shops where shops.name not in (select sales.particulars from sales 
				where (sales.sales_date BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate())) and shops.fos='$eachId')) a order by a.qty desc";
				$rExe1 = mysqli_query($con,$rSql1);
				while($rRes1 = mysqli_fetch_array($rExe1))
				{
					//echo $rRes1["particulars"].','.$rRes1["product_model"].','.$rRes1["qty"].'<br>';
					if($rRes1["product_model"]!=null)
						array_push($mdlShpQtyArr,array('particulars'=>$rRes1["particulars"],'product_model'=>$rRes1["product_model"],'Qty'=>$rRes1["qty"]));
				}
				$rSql = "select * from (select shops.id,shops.name as particulars,sales.product_model,sum(sales.qty) as qty from shops inner join sales on 
				shops.name=sales.particulars where shops.fos='$eachId' and (sales.sales_date BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate()) and 
				sales.product_model!='' group by shops.Name 
				union 
				(SELECT shops.id,shops.name as particulars,null,0 from shops where shops.name not in (select sales.particulars from sales 
				where (sales.sales_date BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate())) and shops.fos='$eachId')) a order by a.qty desc";
				$rExe = mysqli_query($con,$rSql);
				$totalTarget = 0;
				while($rRes = mysqli_fetch_array($rExe))
				{
					//echo $rRes["particulars"].'<br>';
					$msg.= '<tr>';
					$msg.= '<td style="padding: 5px;">&nbsp;'.$rRes["particulars"].'</td>';	
					//$equery1 = "select distinct s.product_model from sales s left outer join shops sh on s.particulars=sh.name where sh.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date'";
					//$equery1 = "select distinct s.product_model,sum(s.qty) as qty from sales s left outer join shops sh on s.particulars=sh.name where sh.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date' group by s.qty,s.product_model order by qty asc";
					$equery1 = "select distinct a.product_model,a.qty,round(p.dp) as dp from (select distinct s.product_model,sum(s.qty) as qty from sales s left outer join shops sh on s.particulars=sh.name where sh.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date' group by s.qty,s.product_model) a left outer join product_master p on a.product_model=p.product_model where dp!=0 group by p.product_model order by dp";
					$eexe1 = mysqli_query($con,$equery1);
					$totalQty = 0;
					$dpTtl = 0;
					
					while($eres1 = mysqli_fetch_array($eexe1))
					{
						echo $eres1["product_model"].'<br>';
						$flag = 0;
						foreach($mdlShpQtyArr as $dat)
						{
							if($rRes["particulars"]==$dat['particulars'] && $dat['product_model']==$eres1["product_model"])
							{
								$msg .= '<td style="text-align:right;">'.$dat['Qty'].'&nbsp;</td>';
								$totalQty = (int)$totalQty+(int)$dat['Qty'];
								$product_model = $eres1["product_model"];
								$sl = mysqli_query($con,"select round(dp) as dp from product_master where product_model='$product_model' and dp!=''");
								if($sl)
								{
									if(mysqli_num_rows($sl)>0)
									{
										$rs = mysqli_fetch_array($sl);
										$dpVal = (int)$rs['dp']*(int)$dat['Qty'];
										$dpTtl = (int)$dpTtl+(int)$dpVal;
									}
								}
								$flag = 1;
							}
						}//foreach
						if($flag == 0)
						{
							$msg .= '<td>&nbsp;</td>';
						}								
					}//while
					
					$gradTotal = (int)$gradTotal+(int)$totalQty;
					$grandFinalAmt = (int)$grandFinalAmt+(int)$dpTtl;
					$msg .= '<td style="text-align:right;"><strong>'.$totalQty.'</strong>&nbsp;</td>';
					$msg .= '<td style="text-align:right;color:blue;">'.$dpTtl.'&nbsp;</td>';
					$id = $rRes["id"];
					$qry = mysqli_query($con,"SELECT sum(TargetValue) as TargetValue FROM `monthlyshoptargets` where ShopID='$id' and Year(Period)=YEAR(CURDATE()) and Month(Period)=MONTH(CURDATE()) group by ShopID");
					if($qry)
					{
						if(mysqli_num_rows($qry)>0)
						{
							$target = mysqli_fetch_array($qry);
							$msg .= '<td style="text-align:right;">'.$target['TargetValue'].'&nbsp;</td>';
							$totalTarget = (int)$totalTarget+(int)$target['TargetValue'];
						}
						else
							$msg .= '<td style="text-align:right;">0&nbsp;</td>';	
					}
					$msg .= '</tr>';
				}//while
				
				$msg .= '<tr style="background:rgb(220, 230, 241);text-align:right;"><td style="text-align:left;padding:10px;">&nbsp;<strong>Grand Total</strong></td>';
				//$ssql = "select distinct s.product_model from sales s left outer join shops sh on s.particulars=sh.name where sh.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date'";
				//$ssql = "select distinct s.product_model,sum(s.qty) as qty from sales s left outer join shops sh on s.particulars=sh.name where sh.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date' group by s.qty,s.product_model order by qty asc";
				$ssql = "select distinct a.product_model,a.qty,round(p.dp) as dp from (select distinct s.product_model,sum(s.qty) as qty from sales s left outer join shops sh on s.particulars=sh.name where sh.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date' group by s.qty,s.product_model) a left outer join product_master p on a.product_model=p.product_model where dp!=0 group by p.product_model order by dp";
				$execute = mysqli_query($con,$ssql);
				while($sRes = mysqli_fetch_array($execute))
				{
					$model = $sRes['product_model'];
					$que = "select sum(qty) as total_qty from sales s,app_users a,shops h where product_model='$model' and s.particulars=h.Name and h.fos=a.id and h.fos='$eachId' and s.sales_date between '$st_date' and '$ed_date' and h.Deleted='0' and a.Active=1 group by product_model";
					$run = mysqli_query($con,$que);
					$grandTtl = mysqli_fetch_array($run);
					if($grandTtl['total_qty']!=0)
						$msg .= '<td><strong>'.$grandTtl['total_qty'].'</strong>&nbsp;</td>';
					else
						$msg .= '<td><strong>0</strong>&nbsp;</td>';
				}
				$msg .= '<td><strong>'.$gradTotal.'</strong></td>';
				$msg .= '<td style="color:red"><strong>'.$grandFinalAmt.'</strong></td>';
				$msg .= '<td style="color:black"><strong>'.$totalTarget.'</strong></td>';
				$msg .= '</tr></table>';
			}// cnt>0
			
			if($cnt==0)
			{
				$msg     .= '<p><strong>No Records Available!</strong></p>';	
			}
			//$emailto     = $email.',vtzss@vttrading.in';
			$emailto     = 'arun@vttech.in';
			$toname      = 'VeeTee Trading';
			$emailfrom   = 'vt.sales@outlook.com';
			$fromname    = 'Web Admin';
			$subject     = 'Sales Report - '.date("d-m-Y");
			$messagebody = $msg;
							
			$headers = 
			'Return-Path: ' . $emailfrom . "\r\n" . 
			'From: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" . 
			'X-Priority: 3' . "\r\n" . 
			'X-Mailer: PHP ' . phpversion() .  "\r\n" . 
			'Reply-To: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" .
			//'Cc: arun@vttech.in' . "\r\n".				
			//'Bcc: arunit93@gmail.com' . "\r\n" .
			'MIME-Version: 1.0' . "\r\n" . 
			'Content-Transfer-Encoding: 8bit' . "\r\n" . 
			'Content-Type: text/html; charset=UTF-8' . "\r\n";
			$params = '-f ' . $emailfrom;
			$test = mail($emailto, $subject, $messagebody, $headers, $params);// $test should be TRUE if the mail function is called correctly
						
			if($test)
			{
				echo('Sales Reports Sent Successfully.');
			}
			else
			{
				echo('Error of Sending Sales Reports!.');
			}
		}// end eachId while.
		}// end ex.
	} // end con.
?>
