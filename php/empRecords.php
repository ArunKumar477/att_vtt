<?php

	require_once('config.php');
	//require_once('config_s.php');
	//date_default_timezone_set("Asia/Kolkata");
	$todayDate = date("Y-m-d");
	if($con)
	{
		$query = "select distinct(a.fos),s.fos_name from attendance a,app_users s where DATE(a.attendance_date)='$todayDate' and a.fos=s.user_name and s.Active=1";
		$exe = mysqli_query($con,$query);
		if($exe)
		{
			$msg 	  = '<p style="color:green;text-align:center;width:60%"><strong>FOS Daily Attendance Records</strong></p>';
			$cnt = mysqli_num_rows($exe);
			if($cnt>0)
			{
				while($res = mysqli_fetch_array($exe))
				{
					$fos = $res['fos'];
					$fosname = $res['fos_name'];
					$msg     .= '<p style="margin-left:30px;"><strong>Employee Mobile </strong> : '.$fosname.' ('.$fos.')</p>';				
					$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
					$msg 	 .= '<tr style="background:skyblue;"><th> Date </th><th> Time </th><th> Shop Name </th><th> Area </th><th> Pincode </th>';
					$msg	 .= '<th> Purpose </th></tr>';
					$sql = "select a.id,DATE(a.attendance_date) as attendance_date,TIME(a.attendance_date) as attendance_time,s.Name,s.Area,s.Pincode,a.purpose 
							from attendance a,shops s where a.shop_id=s.id and a.fos='$fos' and DATE(a.attendance_date)='$todayDate' and s.Deleted='0'";
					$ex = mysqli_query($con,$sql);
					if($ex)
					{
						while($rs = mysqli_fetch_array($ex))
						{
							$attendance_time = $rs['attendance_time'];
							$attendance_time = date("g:i:s a", strtotime($attendance_time));
							$attendance_date = $rs['attendance_date'];
							$attendance_date = DateTime::createFromFormat('Y-m-d', $attendance_date)->format('d-m-y');
							
							$msg     .= '<tr><td style="padding:10px;">'. $attendance_date .'</td><td style="padding:10px;">'. $attendance_time .'</td><td style="padding:10px;">'. $rs['Name'] .'</td><td style="padding:10px;">'. $rs['Area'] .'</td><td style="padding:10px;">'. $rs['Pincode'] .'</td><td style="padding:10px;">'. $rs['purpose'] .'</td></tr>';
						}
					}
					$msg	.= '</table>';
				}
			}
			else
			{
				$msg     .= '<p><strong>No Records Found!</strong></p>';
			}
		}
		
		//$toEmails = array("thiru@vttech.in","venkat@vttech.in","srini@vttrading.in","sathiya@vttrading.in","zia@vttrading.in","arunit93@gmail.com");	
		//foreach($toEmails as $toMail)
		//{
			//$emailto     = "thiru@vttech.in, venkat@vttech.in, srini@vttrading.in, sathiya@vttrading.in, zia@vttrading.in, arunit93@gmail.com";
			$emailto     = "vtzss@vttrading.in";
			$toname      = 'VeeTee Trading';
			$emailfrom   = 'vt.sales@outlook.com';
			$fromname    = 'Web Admin';
			$subject     = 'Fos Daily Attendance Report';
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
				echo('Fos Daily attendance records sent successfully.');
			}
			else
			{
				echo('Error for Fos Daily attendance record sending.');
			}
		//}
	}

?>