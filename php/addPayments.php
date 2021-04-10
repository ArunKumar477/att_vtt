<?php 
	error_reporting(E_ERROR);
	//require_once('config_s.php');
	require_once('config.php');
	require('FPDF-master/fpdf.php');
	require 'lib/PHPMailer/PHPMailerAutoload.php';
	require_once('lib/PHPMailer/class-phpmailer.php');
	date_default_timezone_set('Asia/Kolkata');
	$currtime = date("h:i A");
	$currDateT = date("d-M-Y");
	$currTime = date("h:i a");
	if(isset($_POST['invoiceNo']))
	{
		$shopNameTxt = $_POST['shopNameTxt'];
		$app_user = $_POST['app_user'];
		$invoiceNo = $_POST['invoiceNo'];
		$shpLat = $_POST['shpLat'];
		$shpLong = $_POST['shpLong'];
		//$ownerEmail = $_POST['ownerEmail'];
		if(isset($_POST['otpMbl']))
			$otpMbl = $_POST['otpMbl'];
		else
			$otpMbl = 9585878170;
		//$ownerName = $_POST['ownerName'];
		$shpFullName = $_POST['shpFullName'];
		if (strpos($shpFullName, '!!') !== false)
			$shpFullName = str_replace("!!","&",$shpFullName);
		if (strpos($shpFullName, '@@') !== false)
			$shpFullName = str_replace("@@","#",$shpFullName);
		
		$pymntTotal = $_POST['pymntTotal'];
		$infoRdBtn = $_POST['infoRdBtn'];
		$todayDate = date("Y-m-d");
		$todayDate_ordrId = explode('-',$todayDate);
		$todayDate_ordrId = implode("",$todayDate_ordrId);
		$setDateTime = date("Y-m-d H:i:s");
		if($con)
		{
			/* get Unique Id */
			$esql = "SELECT SUBSTR(unique_id,11) as uniqueId FROM invoice_payment where unique_id!='' group by unique_id";
			$eex  = mysqli_query($con,$esql);  
			$ccnt = mysqli_num_rows($eex);
			$uniqFinalVal = 0;
			if($ccnt!=0)
			{
				while($res = mysqli_fetch_array($eex))
				{
					if($uniqFinalVal<$res['uniqueId'])
					{
						$uniqFinalVal = $res['uniqueId'];
					}
				}
				$uniqFinalVal = $uniqFinalVal+1;
				$dateUnique = $todayDate_ordrId.'/P'.$uniqFinalVal;
			}
			else
			{
				$dateUnique = $todayDate_ordrId.'/P1';
			}
			/* end */
			
			$sql = "select id,user_name from app_users where user_name='$app_user' and Active=1";
			$ex = mysqli_query($con,$sql);
			if($ex)
			{
				$Id = mysqli_fetch_array($ex);
				$userId = $Id['id'];
				$invoiceNoAmt = explode(",",$invoiceNo);
				$invAmtSz = sizeof($invoiceNoAmt);
				$insertedIds_arr = array();
				for($i=0;$i<$invAmtSz;$i++)
				{
					$invNoAmt = $invoiceNoAmt[$i];
					$result = explode("^",$invNoAmt);
					$invNo = $result[0];
					$amt = $result[1];
					$cashTypeHidTxt = $result[2];
					if($cashTypeHidTxt == 'cash')
					{
						$pymnt_type = $result[3];
						$originalAmt = $result[4];
						$actualAmt = $result[5];
						$query_c = "select id from invoice_payment where user_id='$userId' and shop_id='$shopNameTxt' and invoice_no='$invNo' and amount='$amt' and latitude='$shpLat' and longitude='$shpLong' and cash_type='$cashTypeHidTxt' and pymnt_type='$pymnt_type' and actual_amt='$actualAmt' and DATE(pymnt_date)='$todayDate'";
						$exe_c = mysqli_query($con,$query_c);
						$cnt_c = mysqli_num_rows($exe_c);
						if($cnt_c==0)
						{
							$query = "insert into invoice_payment(user_id,shop_id,invoice_no,amount,latitude,longitude,cash_type,pymnt_type,unique_id,
							actual_amt,pymnt_date,sales_date)values('$userId','$shopNameTxt','$invNo','$amt','$shpLat','$shpLong','$cashTypeHidTxt',
							'$pymnt_type','$dateUnique','$actualAmt','$setDateTime',(select distinct outstanding_date from outstandings where ref_no='$invNo'))";
							$exe = mysqli_query($con,$query);
							$sql1 = mysqli_query($con,"SELECT LAST_INSERT_ID() as id");
							$ids = mysqli_fetch_array($sql1);
							array_push($insertedIds_arr,array($ids['id']));
						}
					}
					if($cashTypeHidTxt == 'cheque' || $cashTypeHidTxt == 'neft' || $cashTypeHidTxt == 'cn')
					{
						$pymnt_type = $result[5];
						$chequeNmbr = $result[3];
						$chequeDate = $result[4];
						$originalAmt = $result[6];
						$actualAmt = $result[7];
						if($cashTypeHidTxt=='cn')
							$query_ch = "select id from invoice_payment where user_id='$userId' and shop_id='$shopNameTxt' and invoice_no='$invNo' and amount='$amt' and cash_type='$cashTypeHidTxt' and cheque_no='$chequeNmbr' and actual_amt='$actualAmt' and DATE(pymnt_date)='$todayDate'";
						else
							$query_ch = "select id from invoice_payment where user_id='$userId' and shop_id='$shopNameTxt' and invoice_no='$invNo' and amount='$amt' and cash_type='$cashTypeHidTxt' and cheque_no='$chequeNmbr' and cheque_date='$chequeDate' and actual_amt='$actualAmt' and DATE(pymnt_date)='$todayDate'";
						$exe_ch = mysqli_query($con,$query_ch);
						$cnt_ch = mysqli_num_rows($exe_ch);
						if($cnt_ch==0)
						{
							$query = "insert into invoice_payment(user_id,shop_id,invoice_no,amount,latitude,longitude,cash_type,cheque_no,
							cheque_date,pymnt_type,unique_id,actual_amt,pymnt_date,sales_date) values('$userId','$shopNameTxt','$invNo','$amt',
							'$shpLat','$shpLong','$cashTypeHidTxt','$chequeNmbr','$chequeDate','$pymnt_type','$dateUnique',
							'$actualAmt','$setDateTime',(select distinct outstanding_date from outstandings where ref_no='$invNo'))";
							$exe = mysqli_query($con,$query);
							$sql1 = mysqli_query($con,"SELECT LAST_INSERT_ID() as id");
							$ids = mysqli_fetch_array($sql1);
							array_push($insertedIds_arr,array($ids['id']));
						}
					}
					
					if($exe)
					{
						$getPymntSts = "select pending_amount from outstandings where ref_no='$invNo' and party_name='$shpFullName' and payment_received='0' and pending_amount!='0'";
						$getPR_exe = mysqli_query($con,$getPymntSts);
						$len = mysqli_num_rows($getPR_exe);
						//echo $len;
						if($len>0)
						{
							$pending_amount = mysqli_fetch_array($getPR_exe);
							if($pending_amount['pending_amount']==$amt)
							{
								$updtPymntRecvd = "update outstandings set pending_amount='0',payment_received='1' where ref_no='$invNo' and party_name='$shpFullName'";
								$PR_exe = mysqli_query($con,$updtPymntRecvd);
							}
							else
							{
								$remainingAmt = (int)$pending_amount['pending_amount']-(int)$amt;
								$updtPymntAmt = "update outstandings set pending_amount='$remainingAmt' where ref_no='$invNo' and party_name='$shpFullName' and payment_received='0'";
								$uPA_exe = mysqli_query($con,$updtPymntAmt);
							}
						}
								
						if($invAmtSz-1==$i)
						{
							/*if($infoRdBtn=='Owner')
								$updtQuery = "update shops set Partner_name='$ownerName',primary_mobile='$otpMbl',primary_email='$ownerEmail' where id='$shopNameTxt' and Deleted='0'";	
							if($infoRdBtn=='Shop')
								$updtQuery = "update shops set Partner_name='$ownerName',secondary_mobile='$otpMbl',secondary_email='$ownerEmail' where id='$shopNameTxt' and Deleted='0'";
							if($infoRdBtn=='Staff')
								$updtQuery = "update shops set Staff_name='$ownerName',Staff_mobile='$otpMbl',Staff_email='$ownerEmail' where id='$shopNameTxt' and Deleted='0'";	
							$upExe = mysqli_query($con,$updtQuery);*/
						//  Amount to words convertion start here
							$num = $pymntTotal;
							$ones = array(1 => "one",2 => "two",3 => "three",4 => "four",5 => "five",6 => "six",7 => "seven",8 => "eight",9 => "nine",10 => "ten",11 => "eleven",12 => "twelve",13 => "thirteen",14 => "fourteen",15 => "fifteen",16 => "sixteen",17 => "seventeen",18 => "eighteen",19 => "nineteen"); 
							$tens = array(2 => "twenty", 3 => "thirty",4 => "forty",5 => "fifty", 6 => "sixty",7 => "seventy",8 => "eighty",9 => "ninety"); 
							$hundreds = array("hundred","thousand","million","billion","trillion","quadrillion"); //limit t quadrillion 
							$num = number_format($num,2,".",","); 
							$num_arr = explode(".",$num); 
							$wholenum = $num_arr[0]; 
							$decnum = $num_arr[1]; 
							$whole_arr = array_reverse(explode(",",$wholenum)); 
							krsort($whole_arr); 
							$rettxt = ""; 
							foreach($whole_arr as $key => $i)
							{ 
								if($i < 20){$rettxt .= $ones[$i];}
								elseif($i < 100){$rettxt .= $tens[substr($i,0,1)];$rettxt .= " ".$ones[substr($i,1,1)];}
								else{$rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];$rettxt .= " ".$tens[substr($i,1,1)];$rettxt .= " ".$ones[substr($i,2,1)];} 
								if($key > 0){$rettxt .= " ".$hundreds[$key]." ";} 
							} 
							if($decnum > 0)
							{
								$rettxt .= " and "; 
								if($decnum < 20)
								{ 
									$rettxt .= $ones[$decnum]; 
								}
								elseif($decnum < 100)
								{ 
									$rettxt .= $tens[substr($decnum,0,1)]; 
									$rettxt .= " ".$ones[substr($decnum,1,1)]; 
								}	 
							} 
							$inwords = $rettxt;
						//  words convertion end here 
							
							$getEmailQuery1 = "select Partner_name,primary_email,secondary_email from shops where id='$shopNameTxt' and Deleted='0'";
							$emaiExe1 = mysqli_query($con,$getEmailQuery1);
							$ownerEmail = '';
							$ownerName = '';
							if($emaiExe1)
							{
								if(mysqli_num_rows($emaiExe1)>0)
								{
									$emaiRes1 = mysqli_fetch_array($emaiExe1);
									$ownerName = $emaiRes1['Partner_name'];
									if($emaiRes1['primary_email']!='')
										$ownerEmail     = $emaiRes1['primary_email'];
									else
										$ownerEmail     = $emaiRes1['secondary_email'];
								}
							}
							if($ownerEmail!='')
							{
								$pdf = new FPDF('p','mm','A4');
								$pdf->AddPage();
								$pdf->SetFont('Arial','',8);
									
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
									
								$pdf->Cell('15','10','Amount','1','0','C');
								$pdf->SetFont('Arial','B',8);
								$pdf->Cell('178','10','Rs. '.$pymntTotal,'1','1');
								$pdf->SetFont('Arial','',8);
								$pdf->Cell('15','10','Inwords : ','LBT','0');
								$pdf->SetFont('Arial','B',8);
								$pdf->Cell('178','10',$inwords,'RBT','1');
								$pdf->SetFont('Arial','',8);
								$pdf->Cell('193','10','This is the system generated receipt. No signature necessary.','1','1');
								$pdf->Cell('193','10','* - Cheque payments are subject to realization.','1','1');
							
								$getEmailQuery = "select primary_email from shops where id='$shopNameTxt' and Deleted='0'";
								$emaiExe = mysqli_query($con,$getEmailQuery);
								$emaiRes = mysqli_fetch_array($emaiExe);
								if($emailto=='')
									$emailto     = $ownerEmail.',arun@vttech.in';
								else if($emaiRes['primary_email'])
									$emailto     = $emaiRes['primary_email'].',arun@vttech.in';
								//$pdf->Output("F",'./uploads/e-receipt.pdf'); 
	
								$bodyMsg = '';
								$bodyMsg .= '<p>M/s <strong>'.$shpFullName.'</strong> [ '.$ownerName.' : '.$otpMbl.' ],</p>';
								$bodyMsg .= 'Greetings from VeeTee Trading Pvt Ltd.<br>';
								$bodyMsg .= 'Please find attached e-receipt for your payment of Rs '.$pymntTotal.'.<br>';
								$bodyMsg .= '<p style="font-style: italic;">Kindly note that Cheque payments are subject to realization.</p>';
								$bodyMsg .= 'Reach out to your FOS, in case of any discrepancy.<br>'; 
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
								$mail->AddAddress($emailto);
								//$mail->AddAttachment("./uploads/e-receipt.pdf", '', $encoding = 'base64', $type = 'application/pdf');
								$mail->addStringAttachment($pdf->Output("S",'e-receipt.pdf'), 'e-receipt.pdf', $encoding = 'base64', $type = 'application/pdf');
								if(!$mail->Send())
									$arr = array('ownermail'=>'error');
								else 
									$arr = array('ownermail'=>'sent');
								
							}//end mail
							$arr = array('status'=>'success','lastInsertedIds'=>$insertedIds_arr);
							echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
						}// last time mail
							
					}//if($exe)
					else
					{
						$arr = array('status'=>'failed');
						echo '{"Result":'.json_encode($arr,JSON_UNESCAPED_SLASHES).'}';
					}
				}//for loop
			}//if($ex)
		}//if($con)
		mysqli_close($con);
	}//if(isset($_POST['invoiceNo']))
?>