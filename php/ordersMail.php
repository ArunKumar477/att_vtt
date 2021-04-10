<?php 
	error_reporting(E_ERROR);
	session_start();
	date_default_timezone_set("Asia/Kolkata");
	//require_once('config_s.php');
	require_once('config.php');
	session_unset();
	$mailSts=$_GET['mailSts'];
	$shopNameTxt = $_GET['shopNameTxt'];
	$app_user = $_GET['app_user'];
	$currDate = date("Y-m-d");
	$time = date("h:i a");
	$setDateTime = date("Y-m-d H:i:s");
	if($con)
	{
		if($mailSts=='orders')
		{	
			$query = "select a.user_name,s.Name,o.product_type,o.product_name,o.color,o.Quantity,DATE(o.order_date) as orderdate,TIME(o.order_date) as ordertime,o.unique_id from orders o,shops s,app_users a where o.user_id=a.id and a.user_name='$app_user' and s.Deleted='0' and o.shop_id=s.id and s.id='$shopNameTxt' and date(o.order_date)='$currDate' and a.Active=1";
		}
		if($mailSts=='pymnt')
		{
			$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.actual_amt,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id=a.id and a.user_name='$app_user' and s.Deleted='0' and i.shop_id=s.id and s.id='$shopNameTxt' and date(i.pymnt_date)='$currDate' and a.Active=1";
		}
		$exe = mysqli_query($con,$query);
		$cnt = mysqli_num_rows($exe);
		if($exe)
		{
			if($cnt>0)
			{
				$sql = "select p.user_name,p.fos_name from orders r,app_users p where r.user_id=p.id and p.user_name='$app_user' and p.Active=1";
				$ex = mysqli_query($con,$sql);
				$rs = mysqli_fetch_array($ex);
				
				if($mailSts=='orders')
				{	
					$ssQuery = "select a.user_name,s.Name,o.product_type,o.product_name,o.color,o.Quantity,DATE(o.order_date) as orderdate,TIME(o.order_date) as ordertime,o.unique_id from orders o,shops s,app_users a where o.user_id=a.id and a.user_name='$app_user' and s.Deleted='0' and o.shop_id=s.id and s.id='$shopNameTxt' and date(o.order_date)='$currDate' and a.Active=1";
					$ssExe = mysqli_query($con,$ssQuery);
					$ress = mysqli_fetch_array($ssExe);
					/*$time1 = strtotime($ress['ordertime']);
					$time2 = strtotime('06:30:00');
					$finalTime = $time1+$time2;
					$orderTime = date("H:i:s", $finalTime);
					$orderTime = date("g:i a", strtotime($orderTime));*/
					$msg 	  = '<p style="color:green"><strong>ORDERS INFORMATION</strong></p>';
					$msg     .= '<p><strong>Fos </strong> : '.$app_user.' ('.$rs['fos_name'].'),&nbsp;<strong>Shop Name </strong> : '.$ress['Name'].',&nbsp; 
							<strong>Order-Id</strong> : '.$ress['unique_id'].'</p>';
					$msg 	 .= '<p><strong>Date</strong> : '.$currDate.'&nbsp;'.$time.'.</p>';				
				}
				if($mailSts=='pymnt')
				{	
					$ssQuery1 = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,
						i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a 
						where i.user_id=a.id and a.user_name='$app_user' and s.Deleted='0' and i.shop_id=s.id and s.id='$shopNameTxt' and 	
						date(i.pymnt_date)='$currDate' and a.Active=1";
					$ssExe1 = mysqli_query($con,$ssQuery1);
					$ress1 = mysqli_fetch_array($ssExe1);
					/*$time1 = strtotime($ress1['pymnttime']);
					$time2 = strtotime('06:30:00');
					$finalTime = $time1+$time2;
					$pymnttime = date("H:i:s", $finalTime);
					$pymnttime = date("g:i a", strtotime($pymnttime));*/
					$msg 	  = '<p style="color:green"><strong>PAYMENT-INVOICE DETAILS</strong></p>';
					$msg     .= '<p><strong>Fos </strong> : '.$app_user.' ('.$rs['fos_name'].'),&nbsp;<strong>Shop Name </strong> : '.$ress1['Name'].'</p>';
					$msg 	 .= '<p><strong>Date</strong> : '.$ress1['pymntdate'].'&nbsp;'.$time.'.</p>';		
				}
				$msg     .= '<table border="1" width="90%" style="border-collapse:collapse;padding:10px;">'; 
				if($mailSts=='orders')
				{
					$msg 	 .= '<tr style="padding:10px;"><th>Dealer Name</th><th>Order ID</th><th>Fos Name</th>';
					$equery = "select product_model,color from products";
					$eexe = mysqli_query($con,$equery);
					$ecnt = mysqli_num_rows($eexe);
					if($eexe)
					{
						if($ecnt>0)
						{
							while($eres = mysqli_fetch_array($eexe))
							{
								$msg .= '<th>'.$eres["product_model"].'<br>'.$eres["color"].'</th>';
							}
						}
					}
					$msg 	 .= '</tr>';
					while($resK = mysqli_fetch_array($exe))
					{
						//array_push($resultDataArr,array($res['unique_id'].','.$res['product_name'].$res['color']=>$res['Quantity']));
						$_SESSION[$resK['product_name'].$resK['color']]=$resK['Quantity'];
					}// while
					$que = "select a.user_name,s.Name,o.product_type,o.product_name,o.color,o.Quantity,DATE(o.order_date) as orderdate,TIME(o.order_date) as ordertime,o.unique_id from orders o,shops s,app_users a where o.user_id=a.id and a.user_name='$app_user' and s.Deleted='0' and o.shop_id=s.id and s.id='$shopNameTxt' and date(o.order_date)='$currDate' group by o.unique_id and a.Active=1";	
					$out = mysqli_query($con,$que);
					if($out)
					{
						while($data = mysqli_fetch_array($out))
						{						
							/*$time1 = strtotime($res['ordertime']);
							$time2 = strtotime('06:30:00');
							$finalTime = $time1+$time2;
							$orderTime = date("H:i:s", $finalTime);
							$orderTime = date("g:i a", strtotime($orderTime));*/
							$msg     .= '<tr style="padding:10px;text-align:center;"><td>'.$data['Name'].'</td><td>'.$data['unique_id'].'</td><td>'.$rs['fos_name'].'</td>';
							$equery1 = "select product_model,color from products";
							$eexe1 = mysqli_query($con,$equery1);
							while($eres1 = mysqli_fetch_array($eexe1))
							{
								$val = $eres1["product_model"].$eres1["color"];
								$empty = "";
								if($_SESSION[$val])
								{
									$msg .= '<td style="color:blue;">'.strip_tags(html_entity_decode($_SESSION[$val])).'</td>';	
								}
								else
									$msg .= '<td>'.strip_tags(html_entity_decode($empty)).'</td>';
							}
							$msg .= '</tr>';
						}	
					}
				}
				if($mailSts=='pymnt')
				{
					$msg 	 .= '<tr><th>Invoice-No</th><th>Amount</th><th>Cash-Type</th><th>Cheque_No</th><th>Cheque_Date</th><th>Ref_no</th><th>Neft_date</th><th>CN_no</th><th>Pymnt_Type</th><th>Invoice_Amt</th></tr>';
					while($res = mysqli_fetch_array($exe))
					{
						if($res['cheque_no']!='')
							$chequeNo = $res['cheque_no'];
						else
							$chequeNo = 0;
						if($res['cheque_date']!='')
							$chequeDate = $res['cheque_date'];
						else
							$chequeDate = 0;
						
						if($res['cash_type']=='neft')
						{
							$chequeNo_neft = $res['cheque_no'];
							$chequeDate_neft = $res['cheque_date'];
							$chequeNo = 0;
							$chequeDate = 0;
							$chequeNo_cn = 0;
						}
						if($res['cash_type']=='cn')
						{
							$chequeNo_cn = $res['cheque_no'];
							$chequeNo = 0;
							$chequeDate = 0;
							$chequeNo_neft = 0;
							$chequeDate_neft = 0;
						}
						if($res['cash_type']=='cash')
						{
							$chequeNo_neft = 0;
							$chequeDate_neft = 0;
							$chequeNo = 0;
							$chequeDate = 0;
							$chequeNo_cn = 0;
						}
						if($res['cash_type']=='cheque')
						{
							$chequeNo_neft = 0;
							$chequeDate_neft = 0;
							$chequeNo_cn = 0;
							$chequeDate_cn = 0;
							$chequeNo = $res['cheque_no'];
						}	
													
						$time1 = strtotime($res['pymnttime']);
						$time2 = strtotime('06:30:00');
						$finalTime = $time1+$time2;
						$pymnttime = date("H:i:s", $finalTime);
						$pymnttime = date("g:i a", strtotime($pymnttime));
						$msg     .= '<tr><td>'.$res['invoice_no'].'</td><td>'.$res['amount'].'</td><td>'.$res['cash_type'].'</td><td>'.$chequeNo.'</td><td>'.$chequeDate.'</td><td>'.$chequeNo_neft.'</td><td>'.$chequeDate_neft.'</td><td>'.$chequeNo_cn.'</td><td>'.$res['pymnt_type'].'</td><td>'.$res['actual_amt'].'</td></tr>';
					}
				}
				$msg	.= '</table>';
				//$emailto     = 'veeteetrading@outlook.com';
				$emailto     = 'sales@vttrading.in';
				$toname      = 'VeeTee Trading';
				$emailfrom   = 'vt.sales@outlook.com';
				if($mailSts=='orders')
				{
					$fromname    = 'Web Admin';
					$subject     = 'New Order Details';
				}
				if($mailSts=='pymnt')
				{
					$fromname    = 'Web Admin';
					$subject     = 'New Payment Invoice';
				}
				$messagebody = $msg;
				
				/*$headers = 'Return-Path: ' . $emailfrom . "\r\n" . 
				'From: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" . 
				'X-Priority: 3' . "\r\n" . 
				'X-Mailer: PHP ' . phpversion() .  "\r\n" . 
				'Reply-To: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" .
				'Cc: u.venkatesh@live.com' . "\r\n".				
				'Bcc: arunit93@gmail.com' . "\r\n" .
				'MIME-Version: 1.0' . "\r\n" . 
				'Content-Transfer-Encoding: 7bit' . "\r\n" . 
				'Content-Type: text/html; charset=UTF-8' . "\r\n";
				$params = '-f ' . $emailfrom;
				$test = mail($emailto, $subject, $messagebody, $headers, $params);*/// $test should be TRUE if the mail function is called correctly
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From:'.$fromname.' <'.$emailfrom.'>' . "\r\n";
				//$headers .= 'Cc: u.venkatesh@live.com' . "\r\n";
				$headers .= 'Bcc: arunit93@gmail.com' . "\r\n";
				$test = mail($emailto,$subject,$messagebody,$headers);
				
				if($test)
				{
					  $mailResult = array('mail'=> 'sent');
					  echo '{"result":'.json_encode($mailResult,JSON_UNESCAPED_SLASHES).'}';
				}
				else
				{
					  $mailResult= array('mail'=> 'failed');
					  echo '{"result":'.json_encode($mailResult,JSON_UNESCAPED_SLASHES).'}';
				}
			}
			if($cnt==0)
			{
				$mailResult= array('status'=> 'norows');
				echo '{"result":'.json_encode($mailResult,JSON_UNESCAPED_SLASHES).'}';
			}
		}
	}
?>