<?php 
	//require_once('config_s.php');
	session_start();
	require_once('config.php');
	$currDate = date("Y-m-d");
	$currDate1 = date("d-m-y");
	if($con)
	{
		$sql = "select distinct(i.user_id),a.user_name,a.fos_name from invoice_payment i,app_users a where DATE(i.pymnt_date)='$currDate' and i.user_id=a.id and a.Active=1";
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		if($ex)
		{
			$msg 	  = '<p style="color:green"><strong>COLLECTIONS REPORT</strong></p>';
			if($cnt>0)
			{
				$queryC = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='cash' and s.Deleted='0' and a.Active=1";
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
				
				$queryCH = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='cheque' and s.Deleted='0' and a.Active=1";
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
				
				$queryCH = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='neft' and s.Deleted='0' and a.Active=1";
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
				
				$queryCH = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='cn' and s.Deleted='0' and a.Active=1";
				$exeCH = mysqli_query($con,$queryCH);
				$cntCH = mysqli_num_rows($exeCH);
				$CNTotal = 0;
				if($exeCH)
				{	
					if($cntCH>0)
					{
						while($resCH = mysqli_fetch_array($exeCH))
						{
							$CNTotal = (int)$CNTotal+(int)$resCH['amount'];						
						}
					}
				}
				
				$msg .= '<p>Date : '.$currDate1.'</p>';
				$msg .= '<p>TOTAL - CASH : <strong>'.$cashTotal.'</strong>&nbsp;,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL - CHEQUE : <strong>'.$chequeTotal.'</strong>
							&nbsp;,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL - NEFT : <strong>'.$neftTotal.'</strong>&nbsp;,
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL - CN : <strong>'.$CNTotal.'</strong></p>';
							
				$msg .= '<table width="50%" border="1" style="border-collapse:collapse;padding:5px;text-align:center;">';
				$msg .= '<thead><tr style="background: cornflowerblue;color: white;"><th style="padding: 5px;">Fos</th><th>Cash</th><th>Cheque</th><th>NEFT</th><th>CN</th><th>Total</th></tr></thead><tbody>';
				$ttlAmt = mysqli_query($con,"SELECT A.user_id,B.fos_name,A.cash,A.cheque,A.neft,A.cn 
						FROM (SELECT T.USER_ID, SUM(T.CASH) AS CASH,SUM(T.CHEQUE) AS CHEQUE,SUM(T.NEFT) AS NEFT,SUM(T.CN) AS CN FROM
						(select user_id,cash_type,sum(amount) as amount ,
						if(cash_type='cash',SUM(amount),'') as cash,
						if(cash_type='cheque',SUM(amount),'')  as cheque,
						if(cash_type='neft',SUM(amount),'') as neft,
						if(cash_type='cn',SUM(amount),'')  as cn
						from invoice_payment 
						where Date(pymnt_date)='$currDate'
						group by cash_type,user_id) AS T
						GROUP BY T.USER_ID ) A
						LEFT OUTER JOIN app_users B ON A.USER_ID=B.ID");
				if($ttlAmt)
				{		
					if(mysqli_num_rows($ttlAmt)>0)
					{
						while($a = mysqli_fetch_array($ttlAmt))
						{
							$rowTotal = $a['cash']+$a['cheque']+$a['neft']+$a['cn'];
							$msg .= '<tr><td style="text-align:left;padding: 3px;">'.$a['fos_name'].'</td><td style="text-align:right;">'.$a['cash'].'</td><td style="text-align:right;">'.$a['cheque'].'</td><td style="text-align:right;">'.$a['neft'].'</td><td style="text-align:right;">'.$a['cn'].'</td><td style="text-align:right;"><strong>'.$rowTotal.'</strong></td></tr>';
						}
					}
					else
					{
						$msg .= '<tr><td colspan="5">No records found!.</td></tr>';
					}
				}
				$msg .= '</tbody></table>';
				while($exRes = mysqli_fetch_array($ex))
				{
					$fos = $exRes["user_id"];
					$msg     .= '<p><strong>Employee Mobile </strong> : '.$exRes["fos_name"].' ('.$exRes["user_name"].')</p>';				
					$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='cash' and s.Deleted='0' and a.Active=1";
					$exe = mysqli_query($con,$query);
					$cnt1 = mysqli_num_rows($exe);
					if($exe)
					{	
						if($cnt1>0)
						{
							$msg 	 .= '<div style="margin-bottom:5px;margin-left:30px;"><p>Cash Type : CASH</p>';
							$msg     .= '<table border="1" width="50%" style="border-collapse:collapse;padding:5px;text-align:center;">'; 
							$msg 	 .= '<tr style="background:skyblue;color:white;"><th style="padding:5px;">Shop Name</th><th>Invoice-No</th><th>Pymnt_Type</th><th>Amount</th></tr>';
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
																	
								$time1 = strtotime($res['pymnttime']);
								$time2 = strtotime('06:30:00');
								$finalTime = $time1+$time2;
								$pymnttime = date("H:i:s", $finalTime);
								$pymnttime = date("g:i a", strtotime($pymnttime));
								$pymntDate = DateTime::createFromFormat('Y-m-d', $res['pymntdate'])->format('d-m-Y');
								$msg     .= '<tr><td style="text-align:left;padding:3px;">'.$res['Name'].'</td><td style="text-align:left;">'.$res['invoice_no'].'</td><td style="text-align:left;">'.$res['pymnt_type'].'</td><td style="text-align:right;">'.$res['amount'].'</td></tr>';
							}// while
							$msg	.= '<tr style="background:beige"><td colspan="2" style="text-align:right;border:0;padding:3px;"></td><td>Total&nbsp;</td><td style="text-align:right;color:blue"><strong>'.$totalCash.'</strong></td></tr></table></div>';
						}//cnt1
					}// exe
					
					$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='cheque' and s.Deleted='0' and a.Active=1";
					$exe = mysqli_query($con,$query);
					$cnt2 = mysqli_num_rows($exe);
					if($exe)
					{
						if($cnt2>0)
						{	
							$msg 	 .= '<div style="margin-bottom:5px;margin-left:30px;"><p>Cash Type : CHEQUE</p>';
							$msg     .= '<table border="1" width="80%" style="border-collapse:collapse;padding:5px;text-align:center;">'; 
							$msg 	 .= '<tr style="background:skyblue;color:white;"><th style="padding:5px;">Shop Name</th><th>Invoice-No</th><th>Cheque_No</th><th>Cheque_Date</th><th>Pymnt_Type</th><th>Amount</th></tr>';							
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
									if($res['cheque_date']!='0000-00-00')
									{
										$chequeDate = $res['cheque_date'];
										$chequeDate = DateTime::createFromFormat('Y-m-d', $chequeDate)->format('d-m-y');
									}
									else
										$chequeDate = '0000-00-00';
								}
								else
									$chequeDate = 'Empty';
																	
								$time1 = strtotime($res['pymnttime']);
								$time2 = strtotime('06:30:00');
								$finalTime = $time1+$time2;
								$pymnttime = date("H:i:s", $finalTime);
								$pymnttime = date("g:i a", strtotime($pymnttime));
								$pymntDate = DateTime::createFromFormat('Y-m-d', $res['pymntdate'])->format('d-m-Y');
								$msg     .= '<tr><td style="text-align:left;padding:3px;">'.$res['Name'].'</td><td style="text-align:left;">'.$res['invoice_no'].'</td><td style="text-align:left;">'.$chequeNo.'</td><td style="text-align:left;">'.$chequeDate.'</td><td style="text-align:left;">'.$res['pymnt_type'].'</td><td style="text-align:right;">'.$res['amount'].'</td></tr>';
	
							}// while
							$msg	.= '<tr style="background:beige"><td colspan="4" style="text-align:right;border:0;padding:3px;"></td><td>Total&nbsp;</td><td style="text-align:right;color:blue"><strong>'.$totalCheque.'</strong></td></tr></table></div>';
						}//cnt2
					}// exe
					
					$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='neft' and s.Deleted='0' and a.Active=1";
					$exe = mysqli_query($con,$query);
					$cnt2 = mysqli_num_rows($exe);
					if($exe)
					{
						if($cnt2>0)
						{	
							$msg 	 .= '<div style="margin-bottom:5px;margin-left:30px;"><p>Cash Type : NEFT</p>';
							$msg     .= '<table border="1" width="80%" style="border-collapse:collapse;padding:5px;text-align:center;">'; 
							$msg 	 .= '<tr style="background:skyblue;color:white;"><th style="padding:5px;">Shop Name</th><th>Invoice-No</th><th>Ref_No</th><th>Neft_Date</th><th>Pymnt_Type</th><th>Amount</th></tr>';							
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
									if($res['cheque_date']!='0000-00-00')
									{	
										$chequeDate = $res['cheque_date'];
										$chequeDate = DateTime::createFromFormat('Y-m-d', $chequeDate)->format('d-m-y');	
									}
									else
										$chequeDate = '0000-00-00';
								}
								else
									$chequeDate = 'Empty';
																	
								$time1 = strtotime($res['pymnttime']);
								$time2 = strtotime('06:30:00');
								$finalTime = $time1+$time2;
								$pymnttime = date("H:i:s", $finalTime);
								$pymnttime = date("g:i a", strtotime($pymnttime));
								$pymntDate = DateTime::createFromFormat('Y-m-d', $res['pymntdate'])->format('d-m-Y');
								$msg     .= '<tr><td style="text-align:left;padding:3px;">'.$res['Name'].'</td><td style="text-align:left;">'.$res['invoice_no'].'</td><td style="text-align:left;">'.$chequeNo.'</td><td style="text-align:left;">'.$chequeDate.'</td><td style="text-align:left;">'.$res['pymnt_type'].'</td><td style="text-align:right;">'.$res['amount'].'</td></tr>';
	
							}// while
							$msg	.= '<tr style="background:beige"><td colspan="4" style="text-align:right;border:0;padding:3px;"></td><td>Total&nbsp;</td><td style="text-align:right;color:blue"><strong>'.$totalNeft.'</strong></td></tr></table></div>';
						}//cnt2
					}// exe
					
					$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and i.cash_type='cn' and s.Deleted='0' and a.Active=1";
					$exe = mysqli_query($con,$query);
					$cnt2 = mysqli_num_rows($exe);
					if($exe)
					{
						if($cnt2>0)
						{	
							$msg 	 .= '<div style="margin-bottom:5px;margin-left:30px;"><p>Cash Type : CN</p>';
							$msg     .= '<table border="1" width="80%" style="border-collapse:collapse;padding:5px;text-align:center;">'; 
							$msg 	 .= '<tr style="background:skyblue;color:white;"><th style="padding:5px;">Shop Name</th><th>Invoice-No</th><th>CN_No</th><th>CN_Date</th><th>Pymnt_Type</th><th>Amount</th></tr>';							
							$totalCN = 0;
							while($res = mysqli_fetch_array($exe))
							{
								$totalCN = (int)$totalCN+(int)$res['amount'];
								if($res['cheque_no']!='')
										$chequeNo = $res['cheque_no'];
								else
									$chequeNo = 'Empty';
								if($res['cheque_date']!='')
								{	
									if($res['cheque_date']!='0000-00-00')
									{
										$chequeDate = $res['cheque_date'];
										$chequeDate = DateTime::createFromFormat('Y-m-d', $chequeDate)->format('d-m-y');	
									}
									else
										$chequeDate = '0000-00-00';
								}
								else
									$chequeDate = 'Empty';
																	
								$time1 = strtotime($res['pymnttime']);
								$time2 = strtotime('06:30:00');
								$finalTime = $time1+$time2;
								$pymnttime = date("H:i:s", $finalTime);
								$pymnttime = date("g:i a", strtotime($pymnttime));
								$pymntDate = DateTime::createFromFormat('Y-m-d', $res['pymntdate'])->format('d-m-Y');
								$msg     .= '<tr><td style="text-align:left;padding:3px;">'.$res['Name'].'</td><td style="text-align:left;">'.$res['invoice_no'].'</td><td style="text-align:left;">'.$chequeNo.'</td><td style="text-align:left;">'.$chequeDate.'</td><td style="text-align:left;">'.$res['pymnt_type'].'</td><td style="text-align:right;">'.$res['amount'].'</td></tr>';
	
							}// while
							$msg	.= '<tr style="background:beige"><td colspan="4" style="text-align:right;border:0;padding:3px;"></td><td>Total&nbsp;</td><td style="text-align:right;color:blue"><strong>'.$totalCN.'</strong></td></tr></table></div>';
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
				//$emailto     = 'srini@vttrading.in, sathiya@vttrading.in, zia@vttrading.in, arunit@vttech.in, veeteetrading@outlook.com';
				$emailto     = 'vtzss@vttrading.in';
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
				'Cc: arun@vttech.in' . "\r\n".				
				//'Bcc: arunit93@gmail.com' . "\r\n" .
				'MIME-Version: 1.0' . "\r\n" . 
				'Content-Transfer-Encoding: 8bit' . "\r\n" . 
				'Content-Type: text/html; charset=UTF-8' . "\r\n";
				$params = '-f ' . $emailfrom;
				$test = mail($emailto, $subject, $messagebody, $headers, $params);// $test should be TRUE if the mail function is called correctly
							
				if($test)
				{
					echo('Today Payment-Collection records sent successfully.');
				}
				else
				{
					echo('Today Payment-Collection records sending error!.');
				}
			//}
		}// end ex
	} // end con
?>