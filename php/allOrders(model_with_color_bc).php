<?php 
	error_reporting(E_ERROR);
	session_start();
	//require_once('config_s.php');
	require_once('config.php');
	session_unset();
	$currDate = date("Y-m-d");
	if($con)
	{
		$sql = "select distinct(o.user_id),a.user_name,a.fos_name from orders o,app_users a where DATE(o.order_date)='$currDate' and o.user_id=a.id";
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		if($ex)
		{
			$msg 	  = '<p style="color:green"><strong>ALL ORDERS DETAILS</strong></p>';
			if($cnt>0)
			{
				while($exRes = mysqli_fetch_array($ex))
				{
					$fos = $exRes["user_id"];
					$query = "select a.user_name,s.Name,o.product_type,o.product_name,o.color,o.Quantity,DATE(o.order_date) as orderdate,TIME(o.order_date) as ordertime,o.unique_id from orders o,shops s,app_users a where o.user_id='$fos' and o.user_id=a.id and o.shop_id=s.id and DATE(o.order_date)='$currDate' and s.Deleted='0' order by o.unique_id";
					$exe = mysqli_query($con,$query);
					if($exe)
					{
						//$resultDataArr = array();
						$msg     .= '<p><strong>Employee Mobile </strong> : '.$exRes["user_name"].'&nbsp;('.$exRes['fos_name'].')</p>';				
						$msg     .= '<table border="1" width="90%" style="border-collapse:collapse;padding:10px;border-collapse:collapse;padding:10px;">'; 
						$msg 	 .= '<tr><th>Date</th><th>Dealer Name</th><th>Order Id</th><th>Fos Name</th>';
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
						while($res = mysqli_fetch_array($exe))
						{
							//array_push($resultDataArr,array($res['unique_id'].','.$res['product_name'].$res['color']=>$res['Quantity']));
							$_SESSION[$res['unique_id'].','.$res['product_name'].$res['color']]=$res['Quantity'];
						}// while
						$que = "select a.user_name,s.Name,o.product_type,o.product_name,o.color,o.Quantity,DATE(o.order_date) as orderdate,TIME(o.order_date) as ordertime,o.unique_id from orders o,shops s,app_users a where o.user_id='$fos' and o.user_id=a.id and o.shop_id=s.id and DATE(o.order_date)='$currDate' and s.Deleted='0' group by o.unique_id";	
						$out = mysqli_query($con,$que);
						if($out)
						{
							while($data = mysqli_fetch_array($out))
							{
								/*$time1 = strtotime($res['ordertime']);
								$time2 = strtotime('06:30:00');
								$finalTime = $time1+$time2;
								$ordertime = date("H:i:s", $finalTime);
								$ordertime = date("g:i a", strtotime($ordertime));*/
								$msg     .= '<tr style="text-align:center;"><td>'.$data['orderdate'].'<td>'.$data['Name'].'</td><td>'.$data['unique_id'].'</td><td>'.$exRes['fos_name'].'</td>';
								$equery1 = "select product_model,color from products";
								$eexe1 = mysqli_query($con,$equery1);
								while($eres1 = mysqli_fetch_array($eexe1))
								{
									$val = $data['unique_id'].','.$eres1["product_model"].$eres1["color"];
									$empty = "";
									if($_SESSION[$val])
										$msg .= '<td style="color:blue;"><p>'.$_SESSION[$val].'</p></td>';	
									else
										$msg .= '<td><p>'.$empty.'</p></td>';
								}
							}
							$msg .= '</tr>';
						}
						$msg	.= '</table>';
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
				$subject     = 'Orders Consolidated';
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
				'Content-Transfer-Encoding: 7bit' . "\r\n" . 
				'Content-Type: text/html; charset=UTF-8' . "\r\n";
				$params = '-f ' . $emailfrom;
				$test = mail($emailto, $subject, $messagebody, $headers, $params);// $test should be TRUE if the mail function is called correctly
							
				if($test)
				{
					echo('Today Orders consolidated records sent successfully.');
				}
				else
				{
					echo('Today Orders consolidated records sending error!.');
				}
			//}
		}// end ex
	} // end con
?>