<?php
	require_once('config.php');
	//require_once('config_s.php');
	date_default_timezone_set('Asia/Kolkata');
	$currentDateTime = date("Y-m-d H:i:s");
	if($con)
	{
		$exe = mysqli_query($con,"select id,Date(DATE_SUB(now(), INTERVAL 6 MONTH)) as lastSixMnthDate from shops where Deleted='0'");
		if($exe)
		{
			if(mysqli_num_rows($exe)>0)
			{
				while($res = mysqli_fetch_array($exe))
				{
					$now = time(); // or your date as well
					$your_date = strtotime($res['lastSixMnthDate']);
					$datediff = $now - $your_date;
					$lastSixMnthDays = floor($datediff/(60*60*24)).'<br>';
					 
					$id = $res['id'];
					$query = mysqli_query($con,"SELECT sum(s.debit_amount) as dbVal,sh.credit_period FROM `sales` s left outer join shops sh 
					on s.particulars=sh.name where sh.id='$id' and s.sales_date > DATE_SUB(now(), INTERVAL 6 MONTH) group by s.particulars");
					if($query)
					{
						$creditValFinal = 0;
						if(mysqli_num_rows($query)>0)
						{
							$final = mysqli_fetch_array($query);
							$creditVal = $final['dbVal']/$lastSixMnthDays;
							$creditValFinal = round($creditVal*$final['credit_period']);
							//echo $id.'---------------'.$final['dbVal'].'========='.round($final['dbVal']/$lastSixMnthDays).'______cp:'.$final['credit_period'].'<br>';
						}
						$sql = mysqli_query($con,"update shops set credit_value='$creditValFinal' where id='$id'");
					}
				}
				echo 'success';
			}
			else
				echo 'norows';
		}
	}
?>