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
		$sql = "select distinct(a.email),a.id,a.fos_name from app_users a,outstandings o,shops h where o.party_name=h.Name and h.fos=a.id and h.Deleted='0' and a.Active=1 group by a.email";
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		if($ex)
		{
			$msg = '';
			while($eachEmail = mysqli_fetch_array($ex))
			{
				$eachId = $eachEmail['id'];
				$email = $eachEmail['email'];
				$fos_name = $eachEmail['fos_name'];
				$msg 	  .= '<p style="color:green"><strong>OUTSTANDINGS REPORT - '.$fos_name.'&nbsp;( '.$email.' )</strong></p>';
				if($cnt>0)
				{
					$msg     .= '<table border="1" width="65%" style="border-collapse:collapse;padding:10px;border-collapse:collapse;padding:10px;">'; 
					$msg 	 .= '<tr style="background:#CCC0DA;text-align:center;"><td colspan="6"><strong>VEETEE TRADING PVT LTD</strong></td></tr>';
					$msg 	 .= '<tr style="background:#CCC0DA;text-align:center;"><td colspan="6"><strong>Bills Receivable Outstanding Report</strong></td></tr>';
					$msg 	 .= '<tr><th>Date</th><th>Ref. No.</th><th>Party Name</th><th>Pending Amount</th><th>Due on</th><th>Overdue</th>';
					$query = "select o.outstanding_date,o.ref_no,o.party_name,o.pending_amount,o.due_on,o.overdue from outstandings o,shops s,app_users a where o.party_name=s.Name and s.fos=a.id and s.fos='$eachId' and s.Deleted='0' and a.Active=1";
					$exe = mysqli_query($con,$query);
					$cnt = mysqli_num_rows($exe);
					if($cnt>0)
					{
						while($res = mysqli_fetch_array($exe))
						{
							$msg .= '<tr><td>'.$res['outstanding_date'].'</td><td>'.$res['ref_no'].'</td><td>'.$res['party_name'].'</td><td>'.$res['pending_amount'].'</td><td>'.$res['due_on'].'</td><td>'.$res['overdue'].'</td></tr>';
						}
						$stmt = "select sum(o.pending_amount) as totalPendings from outstandings o,shops s,app_users a where o.party_name=s.Name and s.fos=a.id and s.fos='$eachId' and s.Deleted='0' and a.Active=1";
						$stExe = mysqli_query($con,$stmt);
						$stRes = mysqli_fetch_assoc($stExe);
						$msg .= '<tr style="background:#CCC0DA"><td colspan="2"></td><td>Total</td><td colspan="4">'.round($stRes['totalPendings']).'</td></tr>';
					}
					$msg .= '</tr></table>';
				}// cnt>0
				
				if($cnt==0)
				{
					$msg     .= '<p><strong>No Records Available!</strong></p>';	
				}
			}// end eachId while.
			$emailto     = 'vtzss@vttrading.in';
			$toname      = 'VeeTee Trading';
			$emailfrom   = 'vt.sales@outlook.com';
			$fromname    = 'Web Admin';
			$subject     = 'Outstandings Report';
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
				echo('All Outstandings Reports Sent to perticular person.');
			else
				echo('Error of Sending All Outstandings Reports!.');
		}// end ex.
	} // end con.
?>
