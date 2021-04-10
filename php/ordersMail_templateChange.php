<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$mailSts=$_GET['mailSts'];
	$shopNameTxt = $_GET['shopNameTxt'];
	$app_user = $_GET['app_user'];
	$currDate = date("Y-m-d");
	$time = date("h:i a");
	if($con)
	{
		if($mailSts=='orders')
		{	
			$query = "select a.user_name,s.Name,o.product_type,o.product_name,o.color,o.Quantity,DATE(o.created) as orderdate,TIME(o.created) as ordertime,o.unique_id from orders o,shops s,app_users a where o.user_id=a.id and a.user_name='$app_user' and o.shop_id=s.id and s.id='$shopNameTxt' and date(o.created)='$currDate'";
		}
		if($mailSts=='pymnt')
		{
			$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.created) as pymntdate,TIME(i.created) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id=a.id and a.user_name='$app_user' and i.shop_id=s.id and s.id='$shopNameTxt' and date(i.created)='$currDate'";
		}
		$exe = mysqli_query($con,$query);
		$cnt = mysqli_num_rows($exe);
		if($exe)
		{
			if($cnt>0)
			{
				$sql = "select p.user_name,p.fos_name from orders r,app_users p where r.user_id=p.id and p.user_name='$app_user'";
				$ex = mysqli_query($con,$sql);
				$rs = mysqli_fetch_array($ex);
				
				if($mailSts=='orders')
				{	
					$ssQuery = "select a.user_name,s.Name,o.product_type,o.product_name,o.color,o.Quantity,DATE(o.created) as orderdate,TIME(o.created) as ordertime,o.unique_id from orders o,shops s,app_users a where o.user_id=a.id and a.user_name='$app_user' and o.shop_id=s.id and s.id='$shopNameTxt' and date(o.created)='$currDate'";
					$ssExe = mysqli_query($con,$ssQuery);
					$ress = mysqli_fetch_array($ssExe);
					$time1 = strtotime($ress['ordertime']);
					$time2 = strtotime('06:30:00');
					$finalTime = $time1+$time2;
					$orderTime = date("H:i:s", $finalTime);
					$orderTime = date("g:i a", strtotime($orderTime));
					$msg 	  = '<p style="color:green"><strong>ORDERS INFORMATION</strong></p>';
					$msg     .= '<p><strong>Fos </strong> : '.$app_user.' ('.$rs['fos_name'].'),&nbsp;<strong>Shop Name </strong> : '.$ress['Name'].',&nbsp; 
							<strong>Order-Id</strong> : '.$ress['unique_id'].'</p>';
					$msg 	 .= '<p><strong>Date</strong> : '.$ress['orderdate'].'&nbsp;'.$orderTime.'.</p>';				
				}
				if($mailSts=='pymnt')
				{	
					$ssQuery1 = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.created) as pymntdate,TIME(i.created) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,
						i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id=a.id and a.user_name='$app_user' and i.shop_id=s.id and s.id='$shopNameTxt' and 	
						date(i.created)='$currDate'";
					$ssExe1 = mysqli_query($con,$ssQuery1);
					$ress1 = mysqli_fetch_array($ssExe1);
					$time1 = strtotime($ress1['pymnttime']);
					$time2 = strtotime('06:30:00');
					$finalTime = $time1+$time2;
					$pymnttime = date("H:i:s", $finalTime);
					$pymnttime = date("g:i a", strtotime($pymnttime));
					$msg 	  = '<p style="color:green"><strong>PAYMENT-INVOICE DETAILS</strong></p>';
					$msg     .= '<p><strong>Fos </strong> : '.$app_user.' ('.$rs['fos_name'].'),&nbsp;<strong>Shop Name </strong> : '.$ress1['Name'].'</p>';
					$msg 	 .= '<p><strong>Date</strong> : '.$ress1['pymntdate'].'&nbsp;'.$pymnttime.'.</p>';		
				}
				$msg     .= '<table border="1">'; 
				if($mailSts=='orders')
				{
					$msg 	 .= '<tr><th>Product Type</th><th>Product Model</th><th>Color</th><th>Quantity</th></tr>';
					while($res = mysqli_fetch_array($exe))
					{						
						$time1 = strtotime($res['ordertime']);
						$time2 = strtotime('06:30:00');
						$finalTime = $time1+$time2;
						$orderTime = date("H:i:s", $finalTime);
						$orderTime = date("g:i a", strtotime($orderTime));
						$msg     .= '<tr><td>'.$res['product_type'].'</td><td>'.$res['product_name'].'</td><td>'.$res['color'].'</td><td>'.$res['Quantity'].'</td></tr>';
					}	
				}
				if($mailSts=='pymnt')
				{
					$msg 	 .= '<tr><th>Invoice-No</th><th>Amount</th><th>Cash-Type</th><th>Cheque_No</th><th>Cheque_Date</th><th>Pymnt_Type</th></tr>';
					while($res = mysqli_fetch_array($exe))
					{
						if($res['cheque_no']!='')
							$chequeNo = $res['cheque_no'];
						else
							$chequeNo = 'Empty';
						if($res['cheque_date']!='')
							$chequeDate = $res['cheque_date'];
						else
							$chequeDate = 'Empty';
													
						$time1 = strtotime($res['pymnttime']);
						$time2 = strtotime('06:30:00');
						$finalTime = $time1+$time2;
						$pymnttime = date("H:i:s", $finalTime);
						$pymnttime = date("g:i a", strtotime($pymnttime));
						$msg     .= '<tr><td>'.$res['invoice_no'].'</td><td>'.$res['amount'].'</td><td>'.$res['cash_type'].'</td><td>'.$chequeNo.'</td><td>'.$chequeDate.'</td><td>'.$res['pymnt_type'].'</td></tr>';
					}
				}
				$msg	.= '</table>';
				$emailto     = 'veeteetrading@outlook.com';
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
				
				$headers = 
				'Return-Path: ' . $emailfrom . "\r\n" . 
				'From: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" . 
				'X-Priority: 3' . "\r\n" . 
				'X-Mailer: PHP ' . phpversion() .  "\r\n" . 
				'Reply-To: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" .
				'Cc: u.venkatesh@live.com' . "\r\n".				
				'Bcc: arunit93@gmail.com' . "\r\n" .
				'MIME-Version: 1.0' . "\r\n" . 
				'Content-Transfer-Encoding: 8bit' . "\r\n" . 
				'Content-Type: text/html; charset=UTF-8' . "\r\n";
				$params = '-f ' . $emailfrom;
				$test = mail($emailto, $subject, $messagebody, $headers, $params);// $test should be TRUE if the mail function is called correctly
			
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