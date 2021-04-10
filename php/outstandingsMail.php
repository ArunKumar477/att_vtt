<?php 
	error_reporting(E_ERROR);
	session_start();
	//require_once('config_s.php');
	require_once('config.php');
	session_unset();
	$currDate = date("Y-m-d");
	if($con)
	{
		//$sql = "select distinct(a.email),a.id,s.model_name,s.vch_no,s.particulars from app_users a,sales s,shops h where s.particulars=h.Name and h.fos=a.id";
		$sql = "select distinct(a.email),a.id,a.fos_name from app_users a,outstandings o,shops h where o.party_name=h.Name and h.Deleted='0' and h.fos=a.id and a.Active=1 group by a.email";
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		if($ex)
		{
			while($eachEmail = mysqli_fetch_array($ex))
			{
			$eachId = $eachEmail['id'];
			$email = $eachEmail['email'];
			$fos_name = $eachEmail['fos_name'];
			$msg 	  = '<p style="color:green"><strong>FOS NAME - '.$fos_name.'&nbsp;( '.$email.' )</strong></p>';
			if($cnt>0)
			{
				$due = 0;
				$ttlOutstng = "select sum(`pending_amount`) as Amount, sum(case when o.overdue>s.credit_period then pending_amount else 0 end) as Due from outstandings o left outer join shops s on  o.party_name=s.name  where s.fos='$eachId'";	
				$ttlOutstng_ex = mysqli_query($con,$ttlOutstng);
				if($ttlOutstng_ex)
				{
					if(mysqli_num_rows($ttlOutstng_ex)==1)
					{
						$res = mysqli_fetch_array($ttlOutstng_ex);
						$msg     .= '<p>Total Outstanding &nbsp;&nbsp;&nbsp;- <strong>'.round($res['Amount']).'</strong></strong></p>';
						$due = $res['Due'];
					}
				}
				$msg     .= '<p>Due Date Exceeded - <strong>'.round($due).'</strong></p>';
				$totalPendings = 0;
				$ttlOutstng = "select sum(o.pending_amount) as totalPendings from outstandings o,shops s where o.party_name=s.Name and s.Deleted='0' and s.fos='$eachId' and o.overdue<=s.credit_period";	
				$ttlOutstng_ex = mysqli_query($con,$ttlOutstng);
				if($ttlOutstng_ex)
				{
					if(mysqli_num_rows($ttlOutstng_ex)==1)
					{
						$res = mysqli_fetch_array($ttlOutstng_ex);
						$totalPendings = $res['totalPendings'];
					}
				}
				$msg     .= '<p>Within Due Date &nbsp;&nbsp;&nbsp;&nbsp;- <strong>'.round($totalPendings).'</strong></p>';
				$msg     .= '<div><table border="1" width="70%" style="border-collapse:collapse;padding:10px;border-collapse:collapse;padding:10px;">';
				$msg 	 .= '<tr style="background:#CCC0DA;text-align:center;"><th colspan="6">DUE DATE EXCEEDED</th></tr>';
				$msg 	 .= '<tr><th>Date</th><th>Ref. No.</th><th>Party Name</th><th>Pending Amount</th><th>Due on</th><th>Overdue</th>';
				$query = "select o.outstanding_date,o.ref_no,o.party_name,o.pending_amount,o.due_on,o.overdue from outstandings o,shops s where o.party_name=s.Name and s.Deleted='0' and s.fos='$eachId' and o.overdue>s.credit_period";
				$exe = mysqli_query($con,$query);
				$cnt = mysqli_num_rows($exe);
				if($cnt>0)
				{
					while($res = mysqli_fetch_array($exe))
					{
						$msg .= '<tr><td>'.$res['outstanding_date'].'</td><td>'.$res['ref_no'].'</td><td>'.$res['party_name'].'</td><td>'.$res['pending_amount'].'</td><td>'.$res['due_on'].'</td><td>'.$res['overdue'].'</td></tr>';
					}
					$stmt = "select sum(o.pending_amount) as totalPendings from outstandings o,shops s where o.party_name=s.Name and s.Deleted='0' and s.fos='$eachId' and o.overdue>s.credit_period";
					$stExe = mysqli_query($con,$stmt);
					$stRes = mysqli_fetch_assoc($stExe);
					$msg .= '<tr style="background:#CCC0DA"><td colspan="2"></td><td>Total</td><td colspan="4">'.round($stRes['totalPendings']).'</td></tr>';
					$totalPendings = round($stRes['totalPendings']);
				 }
				$msg .= '</tr></table></div>';
				
				//$msg     .= '<div style="margin-top:5%;"><p><strong>Payment Outstanding - With in Payment</strong></p>';
				$msg     .= '<div style="margin-top:2%;"><table border="1" width="70%" style="border-collapse:collapse;padding:10px;border-collapse:collapse;padding:10px;">'; 
				//$msg 	 .= '<tr style="background:#CCC0DA;text-align:center;"><td colspan="2"></td>';
				$msg 	 .= '<tr style="background:#CCC0DA;text-align:center;"><th colspan="6">WITHIN DUE DATE</th></tr>';
				$msg 	 .= '<tr><th>Date</th><th>Ref. No.</th><th>Party Name</th><th>Pending Amount</th><th>Due on</th><th>Overdue</th>';
				$query = "select o.outstanding_date,o.ref_no,o.party_name,o.pending_amount,o.due_on,o.overdue from outstandings o,shops s where o.party_name=s.Name and s.Deleted='0' and s.fos='$eachId' and o.overdue<=s.credit_period";
				$exe = mysqli_query($con,$query);
				$cnt = mysqli_num_rows($exe);
				if($cnt>0)
				{
					while($res = mysqli_fetch_array($exe))
					{
						$msg .= '<tr><td>'.$res['outstanding_date'].'</td><td>'.$res['ref_no'].'</td><td>'.$res['party_name'].'</td><td>'.$res['pending_amount'].'</td><td>'.$res['due_on'].'</td><td>'.$res['overdue'].'</td></tr>';
					}
					$stmt = "select sum(o.pending_amount) as totalPendings from outstandings o,shops s where o.party_name=s.Name and s.Deleted='0' and s.fos='$eachId' and o.overdue<=s.credit_period";
					$stExe = mysqli_query($con,$stmt);
					$stRes = mysqli_fetch_assoc($stExe);
					$msg .= '<tr style="background:#CCC0DA"><td colspan="2"></td><td>Total</td><td colspan="4">'.round($stRes['totalPendings']).'</td></tr>';
					$totalPendings = round($stRes['totalPendings']);
				 }
				$msg .= '</tr></table></div>';
			}// cnt>0
			
			if($cnt==0)
			{
				$msg     .= '<p><strong>No Records Available!</strong></p>';	
			}
			$emailto     = $email;
			$toname      = 'VeeTee Trading';
			$emailfrom   = 'vt.sales@outlook.com';
			$fromname    = 'Web Admin';
			$subject     = 'Outstandings Report - '.date("d-m-Y");
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
				echo('Outstandings Reports Sent Successfully.');
			}
			else
			{
				echo('Error of Sending Outstandings Reports!.');
			}
		}// end eachId while.
		}// end ex.
	} // end con.
?>
