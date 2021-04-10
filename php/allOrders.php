<?php 
	error_reporting(E_ERROR);
	session_start();
	//require_once('config_s.php');
	require_once('config.php');
	session_unset();
	$currDate = date("Y-m-d");
	if($con)
	{
		$query = "select o.user_id,a.fos_name,o.product_name,sum(o.Quantity) as Quantity,DATE(o.order_date) as orderdate,TIME(o.order_date) as ordertime from orders o,shops s,app_users a where o.user_id=a.id and o.shop_id=s.id and DATE(o.order_date)='$currDate' and s.Deleted='0' and a.Active=1 group by o.product_name,o.user_id order by o.user_id";
		$exe = mysqli_query($con,$query);
		if($exe)
		{
			if(mysqli_num_rows($exe)>0)
			{
				//$msg     .= '<p><strong>Employee Mobile </strong> : '.$exRes["user_name"].'&nbsp;('.$exRes['fos_name'].')</p>';				
				$msg 	  = '<p style="color:green"><strong>ALL ORDERS DETAILS</strong></p>';
				$msg     .= '<table border="1" width="90%" style="border-collapse:collapse;padding:10px;border-collapse:collapse;padding:10px;">'; 
				$msg 	 .= '<tr style="background:deepskyblue;color:white;"><th style="padding:10px;">Date</th><th>Fos Name</th>';
				//$equery = "select distinct(product_model) as product_model,color from products where product_name='Nokia' group by product_model";
				$equery = "select distinct(product_name) as product_model from orders where Date(order_date)='$currDate' group by product_model";
				$eexe = mysqli_query($con,$equery);
				$ecnt = mysqli_num_rows($eexe);
				if($eexe)
				{
					if($ecnt>0)
					{
						while($eres = mysqli_fetch_array($eexe))
						{
							$msg .= '<th>'.$eres["product_model"].'</th>';
						}
					}
				}
				$msg 	 .= '</tr>';				
				while($res = mysqli_fetch_array($exe))
				{
					$_SESSION[$res['product_name'].''.$res['user_id']]=$res['Quantity'];
				}// while
				$que = "select o.user_id,a.fos_name,o.product_name,sum(o.Quantity) as Quantity,DATE(o.order_date) as orderdate,TIME(o.order_date) as ordertime from orders o,shops s,app_users a where o.user_id=a.id and o.shop_id=s.id and DATE(o.order_date)='$currDate' and s.Deleted='0' and a.Active=1 group by o.user_id order by o.user_id";	
				$out = mysqli_query($con,$que);
				if($out)
				{
					while($data = mysqli_fetch_array($out))
					{
						$msg     .= '<tr style="text-align:center;"><td>'.$data['orderdate'].'</td><td>'.$data['fos_name'].'</td>';
						//$equery1 = "select distinct(product_model) as product_model,color from products where product_name='Nokia' group by product_model";
						$equery1 = "select distinct(product_name) as product_model from orders where Date(order_date)='$currDate' group by product_model";
						$eexe1 = mysqli_query($con,$equery1);
						while($eres1 = mysqli_fetch_array($eexe1))
						{
							$val = $eres1["product_model"].''.$data['user_id'];
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
			}
			else
			{
				$msg     .= '<p><strong>No Records Available!</strong></p>';
			}
		}// exe
			
		//$toEmails = array("thiru@vttech.in","venkat@vttech.in","srini@vttrading.in","sathiya@vttrading.in","zia@vttrading.in","arunit93@gmail.com","veeteetrading@outlook.com");	
		//$emailto     = 'vtzss@vttrading.in';
		$emailto     = 'arun@vttech.in';
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
	} // end con
?>