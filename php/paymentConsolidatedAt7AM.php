<?php 
	//require_once('config_s.php');
	session_start();
	require_once('config.php');
	date_default_timezone_set('Asia/Kolkata');
	$currDate = date('Y-m-d',strtotime("-1 days"));
	$currDate1 = date('d-m-y',strtotime("-1 days"));
	if($con)
	{
		$sql = "select distinct(i.user_id),a.user_name,a.fos_name from invoice_payment i,app_users a where DATE(i.pymnt_date)='$currDate' and i.user_id=a.id";
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		if($ex)
		{
			$msg 	  = '<p style="color:green"><strong>COLLECTIONS REPORT</strong></p>';
			if($cnt>0)
			{
				$queryC = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='cash' and s.Deleted='0'";
				$exeC = mysqli_query($con,$queryC);
				$cntC = mysqli_num_rows($exeC);
				$cashTotal = 0;
				if($exeC)
				{	
					if($cntC>0)
					{
						while($resC = mysqli_fetch_array($exeC))
						{
							$cashTotal = (int)$cashTotal+(int)$resC['amount'];						
						}
					}
				}
				
				$queryCH = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='cheque' and s.Deleted='0'";
				$exeCH = mysqli_query($con,$queryCH);
				$cntCH = mysqli_num_rows($exeCH);
				$chequeTotal = 0;
				if($exeCH)
				{	
					if($cntCH>0)
					{
						while($resCH = mysqli_fetch_array($exeCH))
						{
							$chequeTotal = (int)$chequeTotal+(int)$resCH['amount'];						
						}
					}
				}
				
				$queryCH = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='neft' and s.Deleted='0'";
				$exeCH = mysqli_query($con,$queryCH);
				$cntCH = mysqli_num_rows($exeCH);
				$neftTotal = 0;
				if($exeCH)
				{	
					if($cntCH>0)
					{
						while($resCH = mysqli_fetch_array($exeCH))
						{
							$neftTotal = (int)$neftTotal+(int)$resCH['amount'];						
						}
					}
				}
				
				$msg .= '<p>Date : '.$currDate1.'</p>';
				$msg .= '<p>TOTAL CASH : <strong>'.$cashTotal.'</strong>&nbsp;,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL CHEQUE : <strong>'.$chequeTotal.'</strong>
							&nbsp;,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL NEFT : <strong>'.$neftTotal.'</strong></p>';
				while($exRes = mysqli_fetch_array($ex))
				{
					$fos = $exRes["user_id"];
					$msg     .= '<p><strong>Employee Mobile </strong> : '.$exRes["fos_name"].' ('.$exRes["user_name"].')</p>';				
					$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='cash' and s.Deleted='0'";
					$exe = mysqli_query($con,$query);
					$cnt1 = mysqli_num_rows($exe);
					if($exe)
					{	
						if($cnt1>0)
						{
							$msg 	 .= '<div style="margin-bottom:5px;margin-left:30px;"><p>Cash Type : CASH</p>';
							$msg     .= '<table border="1" width="50%" style="border-collapse:collapse;padding:5px;text-align:center;">'; 
							$msg 	 .= '<tr style="background:skyblue;color:white;"><th>Shop Name</th><th>Invoice-No</th><th>Pymnt_Type</th><th>Amount</th></tr>';
							$totalCash = 0;
							while($res = mysqli_fetch_array($exe))
							{
								$totalCash = (int)$totalCash+(int)$res['amount'];
								if($res['cheque_no']!='')
										$chequeNo = $res['cheque_no'];
								else
									$chequeNo = 'Empty';
								if($res['cheque_date']!='')
									$chequeDate = $res['cheque_date'];
								else
									$chequeDate = 'Empty';
																	
								/*$time1 = strtotime($res['pymnttime']);
								$time2 = strtotime('06:30:00');
								$finalTime = $time1+$time2;
								$pymnttime = date("H:i:s", $finalTime);
								$pymnttime = date("g:i a", strtotime($pymnttime));*/
								$pymntDate = DateTime::createFromFormat('Y-m-d', $res['pymntdate'])->format('d-m-Y');
								$msg     .= '<tr><td>'.$res['Name'].'</td><td>'.$res['invoice_no'].'</td><td>'.$res['pymnt_type'].'</td><td>'.$res['amount'].'</td></tr>';
							}// while
							$msg	.= '<tr style="background:beige"><td colspan="2" style="text-align:right;"></td><td>Total&nbsp;</td><td style="text-align:center;color:blue"><strong>'.$totalCash.'</strong></td></tr></table></div>';
						}//cnt1
					}// exe
					
					$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='cheque' and s.Deleted='0'";
					$exe = mysqli_query($con,$query);
					$cnt2 = mysqli_num_rows($exe);
					if($exe)
					{
						if($cnt2>0)
						{	
							$msg 	 .= '<div style="margin-bottom:5px;margin-left:30px;"><p>Cash Type : CHEQUE</p>';
							$msg     .= '<table border="1" width="80%" style="border-collapse:collapse;padding:5px;text-align:center;">'; 
							$msg 	 .= '<tr style="background:skyblue;color:white;"><th>Shop Name</th><th>Invoice-No</th><th>Cheque_No</th><th>Cheque_Date</th><th>Pymnt_Type</th><th>Amount</th></tr>';							
							$totalCheque = 0;
							while($res = mysqli_fetch_array($exe))
							{
								$totalCheque = (int)$totalCheque+(int)$res['amount'];
								if($res['cheque_no']!='')
										$chequeNo = $res['cheque_no'];
								else
									$chequeNo = 'Empty';
								if($res['cheque_date']!='')
								{
									$chequeDate = $res['cheque_date'];
									$chequeDate = DateTime::createFromFormat('Y-m-d', $chequeDate)->format('d-m-y');	
								}
								else
									$chequeDate = 'Empty';
																	
								/*$time1 = strtotime($res['pymnttime']);
								$time2 = strtotime('06:30:00');
								$finalTime = $time1+$time2;
								$pymnttime = date("H:i:s", $finalTime);
								$pymnttime = date("g:i a", strtotime($pymnttime));*/
								$pymntDate = DateTime::createFromFormat('Y-m-d', $res['pymntdate'])->format('d-m-Y');
								$msg     .= '<tr><td>'.$res['Name'].'</td><td>'.$res['invoice_no'].'</td><td>'.$chequeNo.'</td><td>'.$chequeDate.'</td><td>'.$res['pymnt_type'].'</td><td>'.$res['amount'].'</td></tr>';
	
							}// while
							$msg	.= '<tr style="background:beige"><td colspan="4" style="text-align:right;"></td><td>Total&nbsp;</td><td style="text-align:center;color:blue"><strong>'.$totalCheque.'</strong></td></tr></table></div>';
						}//cnt2
					}// exe
					
					$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='neft' and s.Deleted='0'";
					$exe = mysqli_query($con,$query);
					$cnt2 = mysqli_num_rows($exe);
					if($exe)
					{
						if($cnt2>0)
						{	
							$msg 	 .= '<div style="margin-bottom:5px;margin-left:30px;"><p>Cash Type : NEFT</p>';
							$msg     .= '<table border="1" width="80%" style="border-collapse:collapse;padding:5px;text-align:center;">'; 
							$msg 	 .= '<tr style="background:skyblue;color:white;"><th>Shop Name</th><th>Invoice-No</th><th>Ref_No</th><th>Neft_Date</th><th>Pymnt_Type</th><th>Amount</th></tr>';							
							$totalNeft = 0;
							while($res = mysqli_fetch_array($exe))
							{
								$totalNeft = (int)$totalNeft+(int)$res['amount'];
								if($res['cheque_no']!='')
										$chequeNo = $res['cheque_no'];
								else
									$chequeNo = 'Empty';
								if($res['cheque_date']!='')
								{
									$chequeDate = $res['cheque_date'];
									$chequeDate = DateTime::createFromFormat('Y-m-d', $chequeDate)->format('d-m-y');	
								}
								else
									$chequeDate = 'Empty';
																	
								/*$time1 = strtotime($res['pymnttime']);
								$time2 = strtotime('06:30:00');
								$finalTime = $time1+$time2;
								$pymnttime = date("H:i:s", $finalTime);
								$pymnttime = date("g:i a", strtotime($pymnttime));*/
								$pymntDate = DateTime::createFromFormat('Y-m-d', $res['pymntdate'])->format('d-m-Y');
								$msg     .= '<tr><td>'.$res['Name'].'</td><td>'.$res['invoice_no'].'</td><td>'.$chequeNo.'</td><td>'.$chequeDate.'</td><td>'.$res['pymnt_type'].'</td><td>'.$res['amount'].'</td></tr>';
	
							}// while
							$msg	.= '<tr style="background:beige"><td colspan="4" style="text-align:right;"></td><td>Total&nbsp;</td><td style="text-align:center;color:blue"><strong>'.$totalNeft.'</strong></td></tr></table></div>';
						}//cnt2
					}// exe
					
				}// while
			}// cnt>0
			if($cnt==0)
			{
				$msg     .= '<p><strong>No Records Available!</strong></p>';	
			}
			
			//$toEmails = array("thiru@vttech.in","venkat@vttech.in","srini@vttrading.in","sathiya@vttrading.in","zia@vttrading.in","arunit93@gmail.com","veeteetrading@outlook.com");	
			//foreach($toEmails as $toMail)
			//{
				$emailto     = 'selin@vttrading.in, arunit@vttech.in';
				$toname      = 'VeeTee Trading';
				$emailfrom   = 'vt.sales@outlook.com';
				$fromname    = 'Web Admin';
				$subject     = 'Payment Consolidated';
				$messagebody = $msg;
								
				$headers = 
				'Return-Path: ' . $emailfrom . "\r\n" . 
				'From: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" . 
				'X-Priority: 3' . "\r\n" . 
				'X-Mailer: PHP ' . phpversion() .  "\r\n" . 
				'Reply-To: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" .
				//'Cc: u.venkatesh@live.com' . "\r\n".				
				//'Bcc: arunit93@gmail.com' . "\r\n" .
				'MIME-Version: 1.0' . "\r\n" . 
				'Content-Transfer-Encoding: 8bit' . "\r\n" . 
				'Content-Type: text/html; charset=UTF-8' . "\r\n";
				$params = '-f ' . $emailfrom;
				$test = mail($emailto, $subject, $messagebody, $headers, $params);// $test should be TRUE if the mail function is called correctly
							
				if($test)
				{
					echo('Yesterday Payment-Collection records sent successfully.');
				}
				else
				{
					echo('Yesterday Payment-Collection records sending error!.');
				}
			//}
		}// end ex
	} // end con
?>