<?php
	error_reporting(E_ERROR);
	require('FPDF-master/fpdf.php');
	require 'lib/PHPMailer/PHPMailerAutoload.php';
	require_once('lib/PHPMailer/class-phpmailer.php');
	//require_once('config_s.php');
	require_once('config.php');
	date_default_timezone_set('Asia/Kolkata');
	$currDateT = date("d-M-Y");
	$currTime = date("h:i a");
	$shopNameTxt='198';
	$app_user='10';
	$invoiceNo='NOK/2017/05142^500^cash^partial^2500^2500,NOK/2017/05143^1000^cheque^521462^2017-10-09^partial^2000^2500,NOK/2017/05144^700^cn^521742^2017-10-22^partial^2000^1000,NOK/2017/05145^200^neft^111541^2017-11-23^partial^2000^3000,NOK/2017/05142^300^cash^partial^400^7500';
	$shpLat='80.15452';
	$shpLong='12.54418';	
	$ownerEmail='arunit93@gmail.com';
	$otpMbl='9585878170';
	$ownerName='Arun';
	$shpFullName='Landmarks';
	$pymntTotal='2500';
	$infoRdBtn='Owner';
	
	$pdf = new FPDF('p','mm','A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','',8);
	
	if($con)
	{
		$pdf->Image('../images/vtt.png','12','13','25','25');
		$pdf->Cell('35','31','','LTB','0');
		$pdf->Cell('116','5','','TR','0');
		$pdf->Cell('42','5','','TR','1');
		$pdf->Cell('35','10','','0','0');
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell('116','6','VEETEE TRADING PRIVATE LIMITED','R','0');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell('42','6','No.9800','R','1');
		$pdf->Cell('35','5','','0','0');
		$pdf->Cell('116','5','No.2/16, Thanthai Periyar Nagar, Velacherry,','R','0');
		$pdf->Cell('42','5','Date : '.$currDateT,'R','1');
		$pdf->Cell('35','5','','0','0');
		$pdf->Cell('116','5','Tharamani Link Road, Chennai - 600113.','R','0');
		$pdf->Cell('42','5','','R','1');
		$pdf->Cell('35','5','','0','0');
		$pdf->Cell('116','5','Phone : 9677272127 , Mail : veeteetrading@outlook.com','R','0');
		$pdf->Cell('42','5','Time : '.$currTime,'R','1');
		$pdf->Cell('151','5','','BR','0');
		$pdf->Cell('42','5','','BR','1');
		$pdf->Cell('38','10','Received with thanks from : ','LT','0');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell('155','10',$shpFullName,'RT','1');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell('20','10','Person Name : ','LB','0');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell('131','10',$ownerName,'B','0');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell('15','10','Contact : ','B','0');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell('27','10',$otpMbl,'RB','1');
		$pdf->Cell('193','10','PAYMENT DETAILS','1','1','C');
		$pdf->SetFont('Arial','',8);
	
		$invoiceNoVal = explode(",",$invoiceNo);
		$sizeVal = sizeof($invoiceNoVal);
		$h = 0;
		for($i=0;$i<$sizeVal;$i++)
		{
			$invoiceData = $invoiceNoVal[$i];
			$eachVal = explode("^",$invoiceData);
			$invNo = $eachVal[0];
			$invAmt = $eachVal[1];
			$invCashTypeVal = $eachVal[2];
			if($eachVal[2]=='cash')
			{
				$invPymntTypeVal = $eachVal[3];
				$invActualAmt = $eachVal[5];
				if($h==0)
				{
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell('33','10','Cash','LTR','0','C');
					$pdf->Cell('80','10','Invoice No','1','0','L');
					$pdf->Cell('80','10','Amount','1','1','R');
				}
				$pdf->SetFont('Arial','',8);
				$pdf->Cell('33','10','','LR','0','C');
				$pdf->Cell('80','10',$invNo,'1','0','L');
				$pdf->Cell('80','10',$invAmt,'1','1','R');	
			$h = $h+1;
			}
		}
		$invoiceNoValD = explode(",",$invoiceNo);
		$sizeValD = sizeof($invoiceNoValD);
		$k = 0;
		for($j=0;$j<$sizeValD;$j++)
		{
			$invoiceData = $invoiceNoValD[$j];
			$eachVal = explode("^",$invoiceData);
			$invNo = $eachVal[0];
			$invAmt = $eachVal[1];
			$invCashTypeVal = $eachVal[2];	
			if($eachVal[2]=='cheque')
			{
				$chequeNo = $eachVal[3];
				$chequeDate = $eachVal[4];
				$invPymntTypeVal = $eachVal[5];
				$invActualAmt = $eachVal[7];
				if($k==0)
				{
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell('33','10','Cheque *','LTR','0','C');
					$pdf->Cell('40','10','Invoice No','1','0','L');
					$pdf->Cell('40','10','Cheque No','1','0','C');
					$pdf->Cell('40','10','Date','1','0','C');
					$pdf->Cell('40','10','Amount','1','1','R');
				}
				$pdf->SetFont('Arial','',8);
				$pdf->Cell('33','10','','LR','0','C');
				$pdf->Cell('40','10',$invNo,'1','0','L');
				$pdf->Cell('40','10',$chequeNo,'1','0','C');
				$pdf->Cell('40','10',$chequeDate,'1','0','C');
				$pdf->Cell('40','10',$invAmt,'1','1','R');
			$k = $k+1;
			}
		}
		$invoiceNoValT = explode(",",$invoiceNo);
		$sizeValT = sizeof($invoiceNoValT);
		$m = 0;
		for($l=0;$l<$sizeValT;$l++)
		{
			$invoiceData = $invoiceNoValT[$l];
			$eachVal = explode("^",$invoiceData);
			$invNo = $eachVal[0];
			$invAmt = $eachVal[1];
			$invCashTypeVal = $eachVal[2];
			if($eachVal[2]=='neft')
			{
				$neftNo = $eachVal[3];
				$neftDate = $eachVal[4];
				$invPymntTypeVal = $eachVal[5];
				$invActualAmt = $eachVal[7];
				if($m==0)
				{
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell('33','10','NEFT','LTR','0','C');
					$pdf->Cell('40','10','Invoice No','1','0','L');
					$pdf->Cell('40','10','NEFT No','1','0','C');
					$pdf->Cell('40','10','Date','1','0','C');
					$pdf->Cell('40','10','Amount','1','1','R');
				}
				$pdf->SetFont('Arial','',8);
				$pdf->Cell('33','10','','LR','0','C');
				$pdf->Cell('40','10',$invNo,'1','0','L');
				$pdf->Cell('40','10',$neftNo,'1','0','C');
				$pdf->Cell('40','10',$neftDate,'1','0','C');
				$pdf->Cell('40','10',$invAmt,'1','1','R');	
			$m = $m+1;
			}
		}
		$invoiceNoValTT = explode(",",$invoiceNo);
		$sizeValTT = sizeof($invoiceNoValTT);
		$n = 0;
		for($p=0;$p<$sizeValTT;$p++)
		{
			$invoiceData = $invoiceNoValTT[$p];
			$eachVal = explode("^",$invoiceData);
			$invNo = $eachVal[0];
			$invAmt = $eachVal[1];
			$invCashTypeVal = $eachVal[2];
			if($eachVal[2]=='cn')
			{
				$cnNo = $eachVal[3];
				$invPymntTypeVal = $eachVal[5];
				$invActualAmt = $eachVal[7];
				if($n==0)
				{
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell('33','10','Credit Note','LTR','0','C');
					$pdf->Cell('40','10','Invoice No','1','0','L');
					$pdf->Cell('80','10','CN No','1','0','C');
					$pdf->Cell('40','10','Amount','1','1','R');
				}
				$pdf->SetFont('Arial','',8);
				$pdf->Cell('33','10','','LR','0','C');
				$pdf->Cell('40','10',$invNo,'1','0','L');
				$pdf->Cell('80','10',$cnNo,'1','0','C');
				$pdf->Cell('40','10',$invAmt,'1','1','R');
			$n = $n+1;
			}
		}
		
		$pdf->Cell('15','10','Amount','1','0');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell('178','10','Rs. '.$pymntTotal,'1','1');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell('15','10','Inwords : ','LBT','0');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell('178','10','two thousand five hundred','RBT','1');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell('193','10','This is the system generated receipt. No signature necessary.','1','1');
		$pdf->Cell('193','10','* - Cheque payments are subject to realization.','1','1');
	}
	//$pdf->Output();
	//$pdf->Output("F",'./uploads/e-receipt.pdf'); 
	
	$bodyMsg = '';
	$bodyMsg .= '<p>M/s <strong>'.$shpFullName.'</strong> [ '.$ownerName.' : '.$otpMbl.' ],</p>';
	$bodyMsg .= 'Greetings from VeeTee Trading Pvt Ltd.<br>';
	$pdf->SetFont('Arial','B',8);
	$bodyMsg .= 'Please find attached e-receipt for your payment of Rs '.$pymntTotal.'<br>.';
	$pdf->SetFont('Arial','',8);
	$bodyMsg .= '<p style="font-style: italic;">Kindly note that Cheque payments are subject to realization.</p>';
	$bodyMsg .= 'Reach out to your FOS, in case of any discrepancy<br>'; 
	$bodyMsg .= 'Thank you.<br>';
	$bodyMsg .= '<br>';
	$bodyMsg .= 'Regards,<br>';
	$bodyMsg .= 'Team VeeTee.';
	
	$mail = new PHPMailer;
	$mail->isMAIL();
	$mail->IsHTML(true);		                            // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';                       // SMTP server
	$mail->SMTPAuth = true;                         // Enable SMTP authentication
	$mail->Username = 'arunit93@gmail.com';                 // SMTP username
	$mail->Password = '29500222';                 // SMTP password
	$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
	$mail->From = 'vt.sales@outlook.com';
	$mail->Port = 587;                              // SMTP Port
	$mail->FromName  = 'VeeTee Trading';
	
	$mail->Subject   = "E-receipt for your payment";
	$mail->Body      = $bodyMsg;
	$mail->AddAddress('arunit93@gmail.com');
	//$mail->AddAttachment("./uploads/e-receipt.pdf", '', $encoding = 'base64', $type = 'application/pdf');
	$mail->addStringAttachment($pdf->Output("S",'e-receipt.pdf'), 'e-receipt.pdf', $encoding = 'base64', $type = 'application/pdf');
	if(!$mail->Send())
	{
		echo "Mailer Error: " . $mail->ErrorInfo;
		//return false;
 	} else {
   		echo "Message has been sent";
 		//return true;
	}
?>
