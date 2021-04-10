<?php

	require_once('config.php');
	//require_once('config_s.php');
	//date_default_timezone_set("Asia/Kolkata");
	$todayDate = date("Y-m-d");
	if($con)
	{
		$query = "select distinct user_name as fos,fos_name,email,id from app_users where rights='0' and active='1' and id <> '15'";
		$exe = mysqli_query($con,$query);
		if($exe)
		{
            $msg 	  = '<p style="color:green;text-align:center;width:60%"><strong>Shopwise Sales Report</strong></p>';
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
					$msg 	 .= '<tr style="background:skyblue;"><th> Name </th><th> Target </th><th> Achieved </th><th> Pending</th>';
					$msg	 .= '<th> Achieved (%) </th></tr>';
					$sql = "SELECT Name,Target,ifnull(round(Achieved),0) as Achieved,ifnull(round(Target-Achieved),0) as Pending, ifnull(round(Achieved/Target *100),0) as AchievedPerc from (SELECT shops.fos,shops.Name as Name,shops.target_b as Target from shops LEFT OUTER JOIN sales on shops.Name=sales.particulars where shops.Deleted='0' and shops.target_b<>0 group by shops.Name) a left outer join (SELECT sales.particulars,sum(debit_amount) as Achieved FROM sales WHERE month(sales.sales_date)=month(curdate()) group by sales.particulars) b on a.Name=b.particulars where a.fos='$id' order by a.Target DESC";
					$ex = mysqli_query($con,$sql);
					$cnt1 = mysqli_num_rows($ex); 
					if($ex)
					{
						if($cnt1>0)
						{
							while($rs = mysqli_fetch_array($ex))
							{
								$msg     .= '<tr style="text-align:right;"><td style="text-align:left;padding:10px;">'. $rs['Name'] .'</td><td style="padding:10px;">'. $rs['Target'] .'</td><td style="padding:10px;">'. $rs['Achieved'] .'</td><td style="padding:5px;">'. $rs['Pending'] .'</td><td style="padding:10px;">'. $rs['AchievedPerc'] .'</td></tr>';
							}
						}
						else
							$msg     .= '<tr style="text-align:center;"><td colspan="5"><strong>No Records Found!</strong></td></tr>';					
					}
					$msg	.= '</table>';
					$emailto     = $email.',venkat@vttech.in';
                                        //$emailto     = 'prasanth@vttech.in';
					$toname      = 'VeeTee Trading';
					$emailfrom   = 'vt.sales@outlook.com';
					$fromname    = 'Web Admin';
					$subject     = 'Shopwise Value Target Report - '.$fosname;
					$messagebody = $msg;
					
					$headers = 
					'Return-Path: ' . $emailfrom . "\r\n" . 
					'From: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" . 
					'X-Priority: 3' . "\r\n" . 
					'X-Mailer: PHP ' . phpversion() .  "\r\n" . 
					'Reply-To: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" .
					'Cc: prasanth@vttech.in' . "\r\n".
					'Bcc: arun@vttech.in' . "\r\n" .
					'MIME-Version: 1.0' . "\r\n" . 
					'Content-Transfer-Encoding: 8bit' . "\r\n" . 	
					'Content-Type: text/html; charset=UTF-8' . "\r\n";
					$params = '-f ' . $emailfrom;
					$test = mail($emailto, $subject, $messagebody, $headers, $params);// $test should be TRUE if the mail function is called correctly
				
					
				}
					if($test)
					{
						echo('Shops Details Report sent successfully.');
					}
					else
					{
						echo('Error for Shops Details sending.');
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