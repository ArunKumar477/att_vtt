<?php 
	//require_once('config_s.php');
	session_start();
	require_once('config.php');
	$currDate = date("Y-m-d");
	$currDate1 = date("d-m-y");
	if($con)
	{
		$sql = "select distinct(i.user_id),a.user_name,a.fos_name from invoice_payment i,app_users a where DATE(i.created)='$currDate' and i.user_id=a.id";
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		if($ex)
		{
			$msg 	  = '<p style="color:green"><strong>COLLECTIONS REPORT</strong></p>';
			if($cnt>0)
			{
				while($fos = mysqli_fetch_array($ex))
				{
					$fosId = $fos['user_id'];
					$msg .= '<p>Date : '.$currDate1.'</p>';
					$msg .= '<p><strong>Employee Name </strong> : '.$fos["fos_name"].' ('.$fos["user_name"].')</p>';
					$query = "select s.Name,i.invoice_no,i.cash_type,i.cheque_no,i.cheque_date,i.amount,i.pymnt_type,i.actual_amt 
								from invoice_payment i,shops s where i.user_id='$fosId' and i.shop_id=s.id and DATE(i.created)='$currDate'";
					$exe   = mysqli_query($con,$query);
					$cntVal= mysqli_num_rows($exe); 
					if($exe)
					{
						if($cntVal>0)
						{
							$msg    .= '<table border="1" width="80%" style="border-collapse:collapse;padding:5px;text-align:center;">'; 
							$msg 	.= '<tr><th>Store Name</th><th>Invoice Number</th><th>Invoice Amount</th><th>Amount Received</th><th>Due</th><th>Status</th><th colspan="9">Mode of Payment</th></tr>';
							$msg	.= '<tr><th>Cash</th><th>Cheque</th><th>Cheque Date</th><th>Cheque Number</th><th>NEFT</th><th>NEFT Date</th><th>NEFT Ref</th><th>Credit Note</th><th>CN Ref</th></tr>';
							/*while($data = mysqli_fetch_array($exe))
							{
								if($data['cash_type']=='cash')
								{
									$msg .= '<tr><td>'.$data['Name'].'</td><td>'.$data['invoice_no'].'</td><td></td><td></td><td></td><td>'.$data['amount'].'</td><td></td><td></td><td></td><td></td><td></td><td>'.$data['pymnt_type'].'</td><td></td><td>'.$data['actual_amt'].'</td></tr>';
								}
								if($data['cash_type']=='cheque')
								{
									$chequeDate = DateTime::createFromFormat('Y-m-d', $data['cheque_date'])->format('d-m-y');
									$msg .= '<tr><td>'.$data['Name'].'</td><td>'.$data['invoice_no'].'</td><td>'.$data['cheque_no'].'</td><td>'.$chequeDate.'</td><td>'.$data['amount'].'</td><td></td><td></td><td></td><td></td><td></td><td></td><td>'.$data['pymnt_type'].'</td><td></td><td>'.$data['actual_amt'].'</td></tr>';
								}
								if($data['cash_type']=='neft')
								{
									$msg .= '<tr><td>'.$data['Name'].'</td><td>'.$data['invoice_no'].'</td><td></td><td></td><td></td><td></td><td>'.$data['cheque_no'].'</td><td>'.$data['cheque_date'].'</td><td>'.$data['amount'].'</td><td></td><td></td><td>'.$data['pymnt_type'].'</td><td></td><td>'.$data['actual_amt'].'</td></tr>';
								}
								if($data['cash_type']=='cn')
								{
									$msg .= '<tr><td>'.$data['Name'].'</td><td>'.$data['invoice_no'].'</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>'.$data['cheque_no'].'</td><td>'.$data['amount'].'</td><td>'.$data['pymnt_type'].'</td><td></td><td>'.$data['actual_amt'].'</td></tr>';
								}
							}*/
							$msg .= '</table>';						
						}
						else
						{
							$msg .= '<p>No Records Available!</p>';
						}
					} 
				}
			}// cnt>0
			if($cnt==0)
			{
				$msg     .= '<p><strong>No Records Available!</strong></p>';	
			}
			
				$emailto     = 'thiru@vttech.in, venkat@vttech.in, srini@vttrading.in, sathiya@vttrading.in, zia@vttrading.in, arun@vttech.in, veeteetrading@outlook.com';
				$toname      = 'VeeTee Trading';
				$emailfrom   = 'vt.sales@outlook.com';
				$fromname    = 'Web Admin';
				$subject     = 'New Payment Invoice';
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