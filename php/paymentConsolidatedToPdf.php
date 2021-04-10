<?php
	error_reporting(E_ERROR);
	require('FPDF-master/fpdf.php');
	//require('../class-phpmailer.php');
	//require_once('config_s.php');
	session_start();
	require_once('config.php');
	date_default_timezone_set('Asia/Kolkata');
	$currDateT = date("d-m-Y");
	$currDate = date('Y-m-d',strtotime("-1 days"));
	$currDate1 = date('d-m-y',strtotime("-1 days"));
	$currDate_s = date('d-m-y',strtotime("-1 days"));
	if(isset($_GET['dateWise']))
	{
		$currDate = $_GET['dateWise'];
		$currDate_s = DateTime::createFromFormat('Y-m-d', $currDate)->format('d-m-Y');
	}
	$pdf = new FPDF('p','mm','A4');
	//$pdf->AddPage();
	$pdf->SetFont('Arial','',11);
	
	if($con)
	{
		$sql = "select distinct(i.user_id),a.user_name,a.fos_name from invoice_payment i,app_users a where DATE(i.created)='$currDate' and i.user_id=a.id and a.Active=1";
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		if($ex)
		{
			if($cnt>0)
			{
				while($exRes = mysqli_fetch_array($ex))
				{
					$fos = $exRes["user_id"];
					$pdf->AddPage();
					
					$pdf->Image('../images/vtt.png',63,10,15);
					$pdf->Cell(70,20,'','0','0');
					$pdf->Cell(65,20,'VEETEE TRADING PVT LTD','0','1');
					
					$pdf->Cell(70,10,'','0','0');
					$pdf->Cell(50,10,'COLLECTIONS REPORT','0','0');
					$pdf->Cell(65,10,'','0','1');//end line
					
					$cashTotal = 0;
					$query1 = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.created) as pymntdate,TIME(i.created) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.created)='$currDate' and i.cash_type='cash' and s.Deleted='0' and a.Active=1";
					$exe1 = mysqli_query($con,$query1);
					$cnt2 = mysqli_num_rows($exe1);
					if($exe1)
					{	
						if($cnt2>0)
						{
							while($res1 = mysqli_fetch_array($exe1))
							{
								$cashTotal = (int)$cashTotal+(int)$res1['amount'];
							}
							
						}//cnt1
					}// exe
					$chequeTotal = 0;
					$query2 = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.created) as pymntdate,TIME(i.created) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.created)='$currDate' and i.cash_type='cheque' and s.Deleted='0' and a.Active=1";
					$exe2 = mysqli_query($con,$query2);
					$cnt3 = mysqli_num_rows($exe2);
					if($exe2)
					{
						if($cnt3>0)
						{	
							while($res2 = mysqli_fetch_array($exe2))
							{
								$chequeTotal = (int)$chequeTotal+(int)$res2['amount'];
							}// while
						}//cnt2
					}// exe
					
					$neftTotal = 0;
					$query2 = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.created) as pymntdate,TIME(i.created) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.created)='$currDate' and i.cash_type='neft' and s.Deleted='0' and a.Active=1";
					$exe2 = mysqli_query($con,$query2);
					$cnt3 = mysqli_num_rows($exe2);
					if($exe2)
					{
						if($cnt3>0)
						{	
							while($res2 = mysqli_fetch_array($exe2))
							{
								$neftTotal = (int)$neftTotal+(int)$res2['amount'];
							}// while
						}//cnt2
					}// exe
					
					$pdf->Cell(23,5,'','0','0');
					$pdf->Cell(25,5,'Cash Total :','0','0');
					$pdf->Cell(25,5, $cashTotal.' ,','0','0');
					
					$pdf->Cell(30,5,'Cheque Total :','0','0');
					$pdf->Cell(25,5, $chequeTotal.' ,','0','0');
					
					$pdf->Cell(27,5,'NEFT Total :','0','0');
					$pdf->Cell(30,5, $neftTotal,'0','1');
					
					$pdf->Cell(30,15,'Fos Name  : ','0','0');
					$pdf->Cell(120,15, $exRes["fos_name"],'0','0');
					$pdf->Cell(15,15,'Date : ','0','0');
					$pdf->Cell(30,15,$currDate_s,'0','1');//end line
					$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.created) as pymntdate,TIME(i.created) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.created)='$currDate' and i.cash_type='cash' and s.Deleted='0' and a.Active=1";
					$exe = mysqli_query($con,$query);
					$cnt1 = mysqli_num_rows($exe);
					if($exe)
					{	
						if($cnt1>0)
						{
							$pdf->Cell(30,10,'Cash Type : CASH','0','1');
							$pdf->Cell(95,10,'Shop Name','1','0','C');
							$pdf->Cell(50,10,'Invoice No','1','0','C');
							$pdf->Cell(30,10,'Payment Type','1','0','C');
							$pdf->Cell(20,10,'Amount','1','1','C');
							$totalCash = 0;
							while($res = mysqli_fetch_array($exe))
							{
								$totalCash = (int)$totalCash+(int)$res['amount'];
								if($res['cheque_no']!='')
										$chequeNo = $res['cheque_no'];
								else
									$chequeNo = 'Empty';
								if($res['cheque_date']!='')
								{
									$chequeDate = $res['cheque_date'];
								}
								else
									$chequeDate = 'Empty';
																	
								$pymntDate = DateTime::createFromFormat('Y-m-d', $res['pymntdate'])->format('d-m-Y');
								
								$ShpNameLen = strlen($res['Name']);
								if($ShpNameLen>35)
								{
									$startx = $pdf->GetX();
									$starty = $pdf->GetY();
									$rowmaxy = $starty + $hx;
									$pdf ->SetXY($startx, $starty);
									$pdf->MultiCell(95,10,$res['Name'],'LRT','L','0');
									$startx = $pdf->GetX();
									$starty = $pdf->GetY()-20;
									$rowmaxy = $starty + $hx;
									$pdf ->SetXY($startx, $starty);
									$pdf->Cell(95,20,'','LRB','0');
									$pdf->Cell(50,20,$res['invoice_no'],'1','0');
									$pdf->Cell(30,20,$res['pymnt_type'],'1','0');
									$pdf->Cell(20,20,$res['amount'],'1','1');
								}
								else
								{
									$pdf->Cell(95,10,$res['Name'],'1','0');
									$pdf->Cell(50,10,$res['invoice_no'],'1','0');
									$pdf->Cell(30,10,$res['pymnt_type'],'1','0');
									$pdf->Cell(20,10,$res['amount'],'1','1');
								}
							}// while
							$pdf->Cell(95,10,'','0','0');
							$pdf->Cell(50,10,'','0','0');
							$pdf->Cell(30,10,'     Total','1','0');
							$pdf->Cell(20,10,$totalCash,'1','1');
							
						}//cnt1
					}// exe
					
					$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.created) as pymntdate,TIME(i.created) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.created)='$currDate' and i.cash_type='cheque' and s.Deleted='0' and a.Active=1";
					$exe = mysqli_query($con,$query);
					$cnt2 = mysqli_num_rows($exe);
					if($exe)
					{
						if($cnt2>0)
						{	
							$pdf->Cell(30,10,'Cash Type : CHEQUE','0','1');
							$pdf->Cell(55,10,'Shop Name','1','0','C');
							$pdf->Cell(45,10,'Invoice No','1','0','C');
							$pdf->Cell(22,10,'Cheque No','1','0','C');
							$pdf->Cell(26,10,'Cheque Date','1','0','C');
							$pdf->Cell(28,10,'Payment Type','1','0','C');
							$pdf->Cell(20,10,'Amount','1','1','C');
							$totalCheque = 0;
							while($res = mysqli_fetch_array($exe))
							{
								$totalCheque = (int)$totalCheque+(int)$res['amount'];
								if($res['cheque_no']!='')
										$chequeNo = $res['cheque_no'];
								else
									$chequeNo = 'Empty';
								if($res['cheque_date']!='')
								{
									$chequeDate = $res['cheque_date'];
									$chequeDate = DateTime::createFromFormat('Y-m-d', $chequeDate)->format('d-m-y');
								}
								else
									$chequeDate = 'Empty';
																	
								$pymntDate = DateTime::createFromFormat('Y-m-d', $res['pymntdate'])->format('d-m-Y');
								
								$ShpNameLen = strlen($res['Name']);
								if($ShpNameLen>=20 && $ShpNameLen<=32)
								{
									$startx = $pdf->GetX();
									$starty = $pdf->GetY();
									$rowmaxy = $starty + $hx;
									$pdf ->SetXY($startx, $starty);
									$pdf->MultiCell(55,5,$res['Name'],'LRT','L','0');
									$startx = $pdf->GetX();
									$starty = $pdf->GetY()-10;
									$rowmaxy = $starty + $hx;
									$pdf ->SetXY($startx, $starty);
									$pdf->Cell(55,15,'','LRB','0');
									$pdf->Cell(45,15,$res['invoice_no'],'1','0');
									$pdf->Cell(22,15,$chequeNo,'1','0');
									$pdf->Cell(26,15,$chequeDate,'1','0');
									$pdf->Cell(28,15,$res['pymnt_type'],'1','0');
									$pdf->Cell(20,15,$res['amount'],'1','1');
								}
								if($ShpNameLen>32)
								{
									$startx = $pdf->GetX();
									$starty = $pdf->GetY();
									$rowmaxy = $starty + $hx;
									$pdf ->SetXY($startx, $starty);
									$pdf->MultiCell(55,5,$res['Name'],'LRT','L','0');
									$startx = $pdf->GetX();
									$starty = $pdf->GetY()-15;
									$rowmaxy = $starty + $hx;
									$pdf ->SetXY($startx, $starty);
									$pdf->Cell(55,20,'','LRB','0');
									$pdf->Cell(45,20,$res['invoice_no'],'1','0');
									$pdf->Cell(22,20,$chequeNo,'1','0');
									$pdf->Cell(26,20,$chequeDate,'1','0');
									$pdf->Cell(28,20,$res['pymnt_type'],'1','0');
									$pdf->Cell(20,20,$res['amount'],'1','1');
								}
								if($ShpNameLen<20)
								{	
									$pdf->Cell(55,10,$res['Name'],'1','0');
									$pdf->Cell(45,10,$res['invoice_no'],'1','0');
									$pdf->Cell(22,10,$chequeNo,'1','0');
									$pdf->Cell(26,10,$chequeDate,'1','0');
									$pdf->Cell(28,10,$res['pymnt_type'],'1','0');
									$pdf->Cell(20,10,$res['amount'],'1','1');
								}
	
							}// while
							$pdf->Cell(55,10,'','0','0');
							$pdf->Cell(45,10,'','0','0');
							$pdf->Cell(22,10,'','0','0');
							$pdf->Cell(26,10,'','0','0');
							$pdf->Cell(28,10,'     Total','1','0');
							$pdf->Cell(20,10,$totalCheque,'1','1');
						}//cnt2
					}// exe
					
					$query = "select a.user_name,s.Name,i.invoice_no,i.amount,DATE(i.created) as pymntdate,TIME(i.created) as pymnttime,i.cash_type,i.cheque_no,i.cheque_date,i.pymnt_type from invoice_payment i,shops s,app_users a where i.user_id='$fos' and i.user_id=a.id and i.shop_id=s.id and DATE(i.created)='$currDate' and i.cash_type='neft' and s.Deleted='0' and a.Active=1";
					$exe = mysqli_query($con,$query);
					$cnt2 = mysqli_num_rows($exe);
					if($exe)
					{
						if($cnt2>0)
						{	
							$pdf->Cell(30,10,'Cash Type : NEFT','0','1');
							$pdf->Cell(55,10,'Shop Name','1','0','C');
							$pdf->Cell(45,10,'Invoice No','1','0','C');
							$pdf->Cell(22,10,'Ref No','1','0','C');
							$pdf->Cell(26,10,'Neft Date','1','0','C');
							$pdf->Cell(28,10,'Payment Type','1','0','C');
							$pdf->Cell(20,10,'Amount','1','1','C');
							$totalNeft = 0;
							while($res = mysqli_fetch_array($exe))
							{
								$totalNeft = (int)$totalNeft+(int)$res['amount'];
								if($res['cheque_no']!='')
										$chequeNo = $res['cheque_no'];
								else
									$chequeNo = 'Empty';
								if($res['cheque_date']!='')
								{
									$chequeDate = $res['cheque_date'];
									$chequeDate = DateTime::createFromFormat('Y-m-d', $chequeDate)->format('d-m-y');
								}
								else
									$chequeDate = 'Empty';
																	
								$pymntDate = DateTime::createFromFormat('Y-m-d', $res['pymntdate'])->format('d-m-Y');
								
								$ShpNameLen = strlen($res['Name']);
								if($ShpNameLen>=20 && $ShpNameLen<32)
								{
									$startx = $pdf->GetX();
									$starty = $pdf->GetY();
									$rowmaxy = $starty + $hx;
									$pdf ->SetXY($startx, $starty);
									$pdf->MultiCell(55,5,$res['Name'],'LRT','L','0');
									$startx = $pdf->GetX();
									$starty = $pdf->GetY()-10;
									$rowmaxy = $starty + $hx;
									$pdf ->SetXY($startx, $starty);
									$pdf->Cell(55,15,'','LRB','0');
									$pdf->Cell(45,15,$res['invoice_no'],'1','0');
									$pdf->Cell(22,15,$chequeNo,'1','0');
									$pdf->Cell(26,15,$chequeDate,'1','0');
									$pdf->Cell(28,15,$res['pymnt_type'],'1','0');
									$pdf->Cell(20,15,$res['amount'],'1','1');
								}
								if($ShpNameLen>32)
								{
									$startx = $pdf->GetX();
									$starty = $pdf->GetY();
									$rowmaxy = $starty + $hx;
									$pdf ->SetXY($startx, $starty);
									$pdf->MultiCell(55,5,$res['Name'],'LRT','L','0');
									$startx = $pdf->GetX();
									$starty = $pdf->GetY()-15;
									$rowmaxy = $starty + $hx;
									$pdf ->SetXY($startx, $starty);
									$pdf->Cell(55,20,'','LRB','0');
									$pdf->Cell(45,20,$res['invoice_no'],'1','0');
									$pdf->Cell(22,20,$chequeNo,'1','0');
									$pdf->Cell(26,20,$chequeDate,'1','0');
									$pdf->Cell(28,20,$res['pymnt_type'],'1','0');
									$pdf->Cell(20,20,$res['amount'],'1','1');
								}
								if($ShpNameLen<20)
								{	
									$pdf->Cell(55,10,$res['Name'],'1','0');
									$pdf->Cell(45,10,$res['invoice_no'],'1','0');
									$pdf->Cell(22,10,$chequeNo,'1','0');
									$pdf->Cell(26,10,$chequeDate,'1','0');
									$pdf->Cell(28,10,$res['pymnt_type'],'1','0');
									$pdf->Cell(20,10,$res['amount'],'1','1');
								}
	
							}// while
							$pdf->Cell(55,10,'','0','0');
							$pdf->Cell(45,10,'','0','0');
							$pdf->Cell(22,10,'','0','0');
							$pdf->Cell(26,10,'','0','0');
							$pdf->Cell(28,10,'     Total','1','0');
							$pdf->Cell(20,10,$totalNeft,'1','1');
						}//cnt2
					}// exe
					
					$pdf->Cell(195,20,'','0','1');
					$pdf->Cell(165,10,'    Fos Signature','0','0');
					$pdf->Cell(30,10,'Cheked By','0','1');
				}// while
			}// cnt>0
			if($cnt==0)
			{
				$pdf->AddPage();
				$pdf->Cell(30,5,'STATUS : No Payment Collection Records Available!..','0','1');
			}
		}// end ex
	} // end con
		
	$pdf->Output();
	
?>