<?php

	require_once('config.php');
	//require_once('config_s.php');
	//date_default_timezone_set("Asia/Kolkata");
	$todayDate = date("Y-m-d");
	if($con)
	{
		$query = "select distinct user_name as fos,fos_name,email,id from app_users where email!='' and rights='0' and Active=1";
		$exe = mysqli_query($con,$query);
		if($exe)
		{
            $msg 	  = '<p style="color:green;text-align:center;width:60%"><strong>FOS Unvisited Shops</strong></p>';
			$cnt = mysqli_num_rows($exe);
			if($cnt>0)

			{
				$emailto="";
				$x=0;
				while($res = mysqli_fetch_array($exe))
				{
					$msg='';
					$email=$res['email'];
					$fos = $res['fos'];
					$fosname = $res['fos_name'];
					$id=$res['id'];
					$msg     .= '<p style="margin-left:30px;"><strong>Employee Mobile </strong> : '.$fosname.' ('.$fos.')</p>';
					$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
					$msg 	 .= '<tr style="background:skyblue;"><th> FOS Name </th><th> Shop Name </th><th> Area </th><th> Last Visited Date </th>';
					$msg	 .= '<th> Days Since Last Visited </th></tr>';
					$sql = "select * from (select au.fos_name as FOS_Name,s.Name,s.Area,max(date(a.attendance_date)) as LastVisitedDate,datediff(CURDATE()
							,max(date(a.attendance_date))) as DaysSinceLastVisited from shops s left outer join attendance a on s.id=a.shop_id 
							left outer join app_users au on s.fos=au.id where au.id=$id  and s.Deleted='0' group by s.name,s.id ) UnvisitedShops  where 
							dayssincelastvisited>15 order by dayssincelastvisited desc ";
					$ex = mysqli_query($con,$sql);
					$cnt1 = mysqli_num_rows($ex); 
					if($ex)
					{
						if($cnt1>0)
						{
							while($rs = mysqli_fetch_array($ex))
							{
								$time1 = strtotime($rs['LastVisitedDate']);
								$time2 = strtotime('06:30:00');
								$finalTime = $time1+$time2;
								$LastVisitedDate = $rs['LastVisitedDate'];
								$LastVisitedDate = DateTime::createFromFormat('Y-m-d', $LastVisitedDate)->format('d-m-y');
								
								$msg     .= '<tr><td style="padding:10px;">'. $rs['FOS_Name'] .'</td><td style="padding:10px;">'. $rs['Name'] .'</td><td style="padding:10px;">'. $rs['Area'] .'</td><td style="padding:5px;">'. $LastVisitedDate .'</td><td style="padding:10px;">'. $rs['DaysSinceLastVisited'] .'</td></tr>';
							}
						}
						else
							$msg     .= '<tr style="text-align:center;"><td colspan="5"><strong>No Records Found!</strong></td></tr>';					
					}
					$msg	.= '</table>';
					$emailto     = $email;
					$toname      = 'VeeTee Trading';
					$emailfrom   = 'vt.sales@outlook.com';
					$fromname    = 'Web Admin';
					$subject     = 'Unvisited Shops Report - '.date("d-m-Y");
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
				
					
				}
					if($test)
					{
						echo('Fos Unvisited Shops Report sent successfully.');
					}
					else
					{
						echo('Error for Fos Unvisited Shops sending.');
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
			
		}
	

?>