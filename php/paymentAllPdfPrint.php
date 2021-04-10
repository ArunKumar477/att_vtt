<?php 
	error_reporting(E_ERROR);
	date_default_timezone_set('Asia/Kolkata');
	require('FPDF-master/fpdf.php');
	//require_once('config_s.php');
	require_once('config.php');
	//session_start();
	if(isset($_GET['dateWise']))
	{
		$currDate = $_GET['dateWise'];
		$currDate_s = DateTime::createFromFormat('Y-m-d', $currDate)->format('d-m-Y');
	}
	else
	{
		$currDate = date('Y-m-d',strtotime("-1 days"));
		$currDate_s = DateTime::createFromFormat('Y-m-d', $currDate)->format('d-m-Y');
	}
	//echo $currDate.'-----------'.$currDate_s;
	$pdf=new FPDF('L','mm','A4');
	$pdf->SetFont('Arial','B',10);
	if($con)
	{
		$sql_1 = "select distinct(i.user_id),a.user_name,a.fos_name from invoice_payment i,app_users a where DATE(i.pymnt_date)='$currDate' and i.user_id=a.id and a.Active=1";
		$ex_1 = mysqli_query($con,$sql_1);
		$cnt_1 = mysqli_num_rows($ex_1);
		if($ex_1)
		{
			if($cnt_1>0)
			{
				while($exRes_1 = mysqli_fetch_array($ex_1))
				{
					$pdf->SetFont('Arial','B',10);
					$fos = $exRes_1["user_id"];
					$fos_name = $exRes_1["fos_name"];
					$pdf->AddPage();
					$pdf->Image('../images/vtt.png',105,10,15);
					$pdf->Cell(113,20,'','0','0');
					$pdf->Cell(50,20,'VEETEE TRADING PVT LTD','0','0','L');
					$pdf->Cell(114,20,'','0','1');			
									
					$pdf->Cell(50,5,'Fos : '.$fos_name,'0','1','L');
					
					
					$pdf->Cell(50,10,'COLLECTIONS REPORT :-','0','0');
					$pdf->Cell(227,10,'Date : '.$currDate_s,'0','1','R');//end line
					
					$pdf->Cell(42,20,'Store Name','1','0','C');
					$pdf->Cell(15,20,'Inv No','1','0','C');
					$pdf->Cell(18,20,'Inv Amt','1','0','C');
					$pdf->Cell(20,20,'Received','1','0','C');
					$pdf->Cell(12,20,'Due','1','0','C');
					$pdf->Cell(13,20,'Status','1','0','C');
					$pdf->Cell(146,10,'Mode of Payment','1','0','C');
					$pdf->Cell(13,10,'Resp','LTR','1','C');
					$pdf->Cell(120,10,'','0','0');
					$pdf->Cell(16,10,'Cash','1','0','C');
					$pdf->Cell(15,10,'Cheque','1','0','C');
					$pdf->Cell(15,10,'Date','1','0','C');
					$pdf->Cell(14,10,'Number','1','0','C');
					$pdf->Cell(15,10,'NEFT','1','0','C');
					$pdf->Cell(15,10,'Date','1','0','C');
					$pdf->Cell(20,10,'Ref','1','0','C');
					$pdf->Cell(16,10,'CN','1','0','C');
					$pdf->Cell(20,10,'Ref','1','0','C');
					$pdf->Cell(13,10,'','LBR','1','C');
				
				
					$query = "select a.user_name,s.Name,i.invoice_no,sum(i.amount) as amount,DATE(i.pymnt_date) as pymntdate,TIME(i.pymnt_date) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type,actual_amt from invoice_payment i,shops s,app_users a where i.user_id='$fos' and s.Deleted='0' and i.user_id=a.id and i.shop_id=s.id and DATE(i.pymnt_date)='$currDate' and a.Active=1 group by i.invoice_no order by s.Name";
					$exe = mysqli_query($con,$query);
					$cnt1 = mysqli_num_rows($exe);
					if($exe)
					{	
						$y = 1;
						$InvAmt_total = 0;
						$AmtReceived_total = 0;
						$Due_total = 0;
						$cash_total = 0;
						$cheque_total = 0;
						$neft_total = 0;
						$cn_total = 0;
						if($cnt1>0)
						{
							$pdf->SetFont('Arial','',8);
							while($exRes = mysqli_fetch_array($exe))
							{
								$due = $exRes['actual_amt']-$exRes['amount'];
								if($exRes['actual_amt']==$exRes['amount'])
									$pymnt_type = 'Full';
								else
									$pymnt_type = 'Partial';
								$inv_number = $exRes['invoice_no'];
								//$pdf->Cell(40,10,$inv_number,'1','0');
								$inv_no = explode('/',$inv_number);
								if($cnt1!=$y)
								{
									$InvAmt_total = $InvAmt_total+$exRes['actual_amt'];
									$AmtReceived_total = $AmtReceived_total+$exRes['amount'];
									$Due_total = $Due_total+$due;
									$Name = strtolower($exRes['Name']);
									$pdf->Cell(42,10,ucfirst($Name),'LRT','0','C');
									//$pdf->MultiCell(39,10,$Name,1);
									$pdf->Cell(15,10,$inv_no[2],'LRT','0','C');
									$pdf->Cell(18,10,$exRes['actual_amt'],'LRT','0','C');
									$pdf->Cell(20,10,$exRes['amount'],'LRT','0','C');
									$pdf->Cell(12,10,$due,'LRT','0','C');
									$pdf->Cell(13,10,$pymnt_type,'LRT','0','C');
								}
								else
								{
									$InvAmt_total = $InvAmt_total+$exRes['actual_amt'];
									$AmtReceived_total = $AmtReceived_total+$exRes['amount'];
									$Due_total = $Due_total+$due;
									$Name = strtolower($exRes['Name']);
									$pdf->Cell(42,10,ucfirst($Name),'1','0','C',$flag);
									//$pdf->MultiCell(39,10,$Name,1);
									$pdf->Cell(15,10,$inv_no[2],'1','0','C',$flag);
									$pdf->Cell(18,10,$exRes['actual_amt'],'1','0','C',$flag);
									$pdf->Cell(20,10,$exRes['amount'],'1','0','C',$flag);
									$pdf->Cell(12,10,$due,'1','0','C',$flag);
									$pdf->Cell(13,10,$pymnt_type,'1','0','C',$flag);
								}
								$y++;
								//$sql = "select i.amount,i.cash_type,i.cheque_no,i.cheque_date,i.actual_amt from invoice_payment i,shops s where i.invoice_no='$inv_number' and DATE(i.pymnt_date)='$currDate' and i.user_id='$fos' and i.shop_id=s.id and s.Deleted='0'";
								//$sql = "select i.amount,i.cash_type,i.cheque_no,i.cheque_date,i.actual_amt,o.response_code from invoice_payment i,shops s,otp_details o where i.invoice_no='$inv_number' and DATE(i.pymnt_date)='$currDate' and i.user_id='$fos' and i.shop_id=s.id and s.Deleted='0' and o.message_id=i.message_id";
								$sql = "SELECT a.amount,a.cash_type,a.cheque_no,a.cheque_date,a.actual_amt,ifnull(o.response_code,'No data')as response_code,response_code2 from (select i.id,i.amount,i.cash_type,i.cheque_no,i.cheque_date,i.actual_amt,i.message_id from invoice_payment i inner join shops s on i.shop_id=s.id where i.invoice_no='$inv_number' and DATE(i.pymnt_date)='$currDate' and i.user_id='$fos') a left outer join otp_details o on a.message_id=o.message_id group by a.id";
								$ex  = mysqli_query($con,$sql);
								$cnt = mysqli_num_rows($ex);
								if($ex)
								{
									if($cnt>0)
									{
										$x= 1;
										while($res = mysqli_fetch_array($ex))
										{
											if($x==1)
											{
												if($res['response_code2']=='null' || $res['response_code2']==null || $res['response_code2']=='')
													$response_code = $res['response_code'];
												else
													$response_code = $res['response_code2'];
												if($response_code=='StatusUnknown')
													$response_code = 'Unknown';
												if($response_code=='FailedParameter Miss')
													$response_code = 'Failed';	
												if($res['cash_type']=='cash')
												{
													$cash_total = $cash_total+$res['amount'];
													$pdf->Cell(16,10,$res['amount'],'1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(14,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(13,10,$response_code,'1','1','C',$flag);
												}
												if($res['cash_type']=='cheque')
												{
													$cheque_total = $cheque_total+$res['amount'];
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,$res['amount'],'1','0','C',$flag);
													if($res['cheque_date']!=0)
														$cheque_date = DateTime::createFromFormat('Y-m-d', $res['cheque_date'])->format('d-m-y');
													else
														$cheque_date = $res['cheque_date'];
													$pdf->Cell(15,10,$cheque_date,'1','0','C',$flag);
													$pdf->Cell(14,10,$res['cheque_no'],'1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(13,10,$response_code,'1','1','C',$flag);
												}
												if($res['cash_type']=='neft')
												{
													$neft_total = $neft_total+$res['amount'];
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(14,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,$res['amount'],'1','0','C',$flag);
													if($res['cheque_date']!=0)
														$neft_date = DateTime::createFromFormat('Y-m-d', $res['cheque_date'])->format('d-m-y');
													else
														$neft_date = $res['cheque_date'];
													$pdf->Cell(15,10,$neft_date,'1','0','C',$flag);
													$pdf->Cell(20,10,$res['cheque_no'],'1','0','C',$flag);
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(13,10,$response_code,'1','1','C',$flag);
												}
												if($res['cash_type']=='cn')
												{
													$cn_total = $cn_total+$res['amount'];
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(14,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(16,10,$res['amount'],'1','0','C',$flag);
													$pdf->Cell(20,10,$res['cheque_no'],'1','0','C',$flag);
													$pdf->Cell(13,10,$response_code,'1','1','C',$flag);
												}
												$x++;
											}
											else
											{
												if($res['cash_type']=='cash')
												{
													$cash_total = $cash_total+$res['amount'];
													$pdf->Cell(42,10,'','LR','0');
													$pdf->Cell(15,10,'','LR','0');
													$pdf->Cell(18,10,'','LR','0');
													$pdf->Cell(20,10,'','LR','0');
													$pdf->Cell(12,10,'','LR','0');
													$pdf->Cell(13,10,'','LR','0');
													$pdf->Cell(16,10,$res['amount'],'1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(14,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(13,10,'','1','1','C',$flag);
												}
												if($res['cash_type']=='cheque')
												{
													$cheque_total = $cheque_total+$res['amount'];
													$pdf->Cell(42,10,'','LR','0');
													$pdf->Cell(15,10,'','LR','0');
													$pdf->Cell(18,10,'','LR','0');
													$pdf->Cell(20,10,'','LR','0');
													$pdf->Cell(12,10,'','LR','0');
													$pdf->Cell(13,10,'','LR','0');
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,$res['amount'],'1','0','C',$flag);
													if($res['cheque_date']!=0)
														$cheque_date = DateTime::createFromFormat('Y-m-d', $res['cheque_date'])->format('d-m-y');
													else
														$cheque_date = $res['cheque_date'];
													$pdf->Cell(15,10,$cheque_date,'1','0','C',$flag);
													$pdf->Cell(14,10,$res['cheque_no'],'1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(13,10,'','1','1','C',$flag);
												}
												if($res['cash_type']=='neft')
												{
													$neft_total = $neft_total+$res['amount'];
													$pdf->Cell(42,10,'','LR','0');
													$pdf->Cell(15,10,'','LR','0');
													$pdf->Cell(18,10,'','LR','0');
													$pdf->Cell(20,10,'','LR','0');
													$pdf->Cell(12,10,'','LR','0');
													$pdf->Cell(13,10,'','LR','0');
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(14,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,$res['amount'],'1','0','C',$flag);
													if($res['cheque_date']!=0)
														$neft_date = DateTime::createFromFormat('Y-m-d', $res['cheque_date'])->format('d-m-y');
													else
														$neft_date = $res['cheque_date'];
													$pdf->Cell(15,10,$neft_date,'1','0','C',$flag);
													$pdf->Cell(20,10,$res['cheque_no'],'1','0','C',$flag);
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(13,10,'','1','1','C',$flag);
												}
												if($res['cash_type']=='cn')
												{
													$cn_total = $cn_total+$res['amount'];
													$pdf->Cell(42,10,'','LR','0');
													$pdf->Cell(15,10,'','LR','0');
													$pdf->Cell(18,10,'','LR','0');
													$pdf->Cell(20,10,'','LR','0');
													$pdf->Cell(12,10,'','LR','0');
													$pdf->Cell(13,10,'','LR','0');
													$pdf->Cell(16,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(14,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(15,10,'','1','0','C',$flag);
													$pdf->Cell(20,10,'','1','0','C',$flag);
													$pdf->Cell(16,10,$res['amount'],'1','0','C',$flag);
													$pdf->Cell(20,10,$res['cheque_no'],'1','0','C',$flag);
													$pdf->Cell(13,10,'','1','1','C',$flag);
												}
											} // else
										} // while($res = mysqli_fetch_array($ex))
									} // if($cnt>0)
								} // if($ex)
							}// while($exRes = mysqli_fetch_array($exe))
						} // if($cnt1>0)
					} // if($exe)
					$pdf->SetFont('Arial','B',8);
					$pdf->SetFillColor(200,220,255);
					$clr = 'true';
					//$pdf->Cell(133,7,'Total','1','0','C',$clr);
					$pdf->Cell(42,7,'Total','1','0','C',$clr);
					$pdf->Cell(15,7,'','TB','0','C',$clr);
					$pdf->Cell(18,7,$InvAmt_total,'1','0','C',$clr);
					$pdf->Cell(20,7,$AmtReceived_total,'1','0','C',$clr);
					$pdf->Cell(12,7,$Due_total,'1','0','C',$clr);
					$pdf->Cell(13,7,'','LTB','0','C',$clr);
					$pdf->Cell(16,7,$cash_total,'LRTB','0','C',$clr);
					$pdf->Cell(15,7,$cheque_total,'LRTB','0','C',$clr);
					$pdf->Cell(15,7,'','LTB','0','C',$clr);
					$pdf->Cell(14,7,'','TB','0','C',$clr);
					$pdf->Cell(15,7,$neft_total,'1','0','C',$clr);
					$pdf->Cell(35,7,'','1','0','C',$clr);
					$pdf->Cell(16,7,$cn_total,'1','0','C',$clr);
					$pdf->Cell(20,7,'','TBL','0','C',$clr);
					$pdf->Cell(13,7,'','TBR','1','C',$clr);
					$pdf->SetFont('Arial','B',11);
					//$pdf->Cell(277,20,$cash_total.','.$cheque_total.','.$neft_total.','.$cn_total,'0','1');
					$pdf->Cell(277,20,'','0','1');
					$pdf->Cell(237,10,'                   Fos Signature','0','0');
					$pdf->Cell(40,10,'Cheked By','0','1');
				} // while($exRes = mysqli_fetch_array($ex_1))
			} // if($cnt_1) 
			if($cnt_1==0)
			{
				$pdf->AddPage();
				$pdf->Cell(30,5,'STATUS : No Payment Collection Records Available!..','0','1');
			}
		} // if($ex_1)
	$pdf->Output();
	} // if($con)
?>