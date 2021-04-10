<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$app_user = $_GET['app_userId'];
	$currDate = date("Y-m-d");
	if($con)
	{
		if(isset($_GET['tdyTask']))
		{
			$getRights = "select rights from app_users where id='$app_user' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$sql = "select a.overdue,c.credit_period,a.party_name,a.pending_amount from outstandings a,shops c where a.party_name=c.Name and a.payment_received='0' and a.overdue!='0' and c.credit_period!='0' and a.pending_amount!='0' and c.Deleted='0'";
			else
				$sql = "select a.overdue,c.credit_period,a.party_name,a.pending_amount from outstandings a,shops c where a.party_name=c.Name and c.fos='$app_user' and a.payment_received='0' and a.overdue!='0' and c.credit_period!='0' and a.pending_amount!='0' and c.Deleted='0'";
			$ex = mysqli_query($con,$sql);
			$cnt = mysqli_num_rows($ex);
			$totalArr = array();
			$tdyOverdues = array();
			$pndOverdues = array();
			$allDataArr = array();
			if($cnt>0)
			{
				while($rs = mysqli_fetch_array($ex))
				{
					if($rs['overdue']==$rs['credit_period'])
						array_push($tdyOverdues,array('status'=>'success','shopName'=>$rs['party_name'],'pending_amount'=>$rs['pending_amount'],'overdue'=>$rs['overdue']));
					else
					{	
						if($rs['credit_period']<$rs['overdue'] && $rs['credit_period']+3>=$rs['overdue'])
							array_push($pndOverdues,array('status'=>'success','shopName'=>$rs['party_name'],'pending_amount'=>$rs['pending_amount'],'overdue'=>$rs['overdue']));
					}
				}
				array_push($totalArr,array('status'=>'success','today'=>$tdyOverdues,'pendings'=>$pndOverdues));
				echo '{"Result":'.json_encode($totalArr,JSON_UNESCAPED_SLASHES).'}';
			}
			if($cnt=='0')
			{
				$totalArr = array('status'=>'emptySet');
				echo '{"Result":'.json_encode($totalArr,JSON_UNESCAPED_SLASHES).'}';
			}
		}
		if(isset($_GET['orderDisable']))
		{
			$shpName = $_GET['shpName'];
			if (strpos($shpName, '!!') !== false)
				$shpName = str_replace("!!","&",$shpName);
			if (strpos($shpName, '@@') !== false)
				$shpName = str_replace("@@","#",$shpName);
			
			$getRights = "select rights from app_users where id='$app_user' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')	
				$query = "select a.outstanding_date,a.overdue,a.pending_amount,c.credit_period,a.ref_no,c.id as shp_id from outstandings a,shops c where a.party_name='$shpName' and a.party_name=c.Name and a.payment_received='0' and a.overdue!='0' and c.credit_period!='0' and a.pending_amount!='0' and c.Deleted='0'";
			else
				$query = "select a.outstanding_date,a.overdue,a.pending_amount,c.credit_period,a.ref_no,c.id as shp_id from outstandings a,shops c where a.party_name='$shpName' and a.party_name=c.Name and c.fos='$app_user' and a.payment_received='0' and a.overdue!='0' and c.credit_period!='0' and a.pending_amount!='0' and c.Deleted='0'";
			$exe = mysqli_query($con,$query);
			$count = mysqli_num_rows($exe);
			$overduesArr = array();
			$creditValExceededArr = array();
			$chqDays_exceedsArr = array();
			$chqAmt_exceedsArr = array();
			$disable = 'no';
			if($exe)
			{
				if($count>0)
				{
					if($app_user!='7' && $app_user!='9')
					{
						while($res = mysqli_fetch_array($exe))
						{
							/* credit Period Exceeded */
							$outstndDate = '';
							//echo $res['credit_period'].'<'.$res['overdue'].'<br>';
							if($res['credit_period']<$res['overdue'])
							{
								$outstndDate = DateTime::createFromFormat('Y-m-d', $res['outstanding_date'])->format('d-m-Y');	
								array_push($overduesArr,array('disable'=>'yes','outstanding_date'=>$outstndDate,'invoices'=>$res['ref_no'],
								'credit_period'=>$res['credit_period'],'overdue'=>$res['overdue'],'pending_amount'=>$res['pending_amount']));
								$disable = 'yes';
							}
							$getInvCheques = mysqli_query($con,"select amount,cheque_no,cheque_date from invoice_payment where shop_id='".$res['shp_id']."' 
							and invoice_no='".$res['ref_no']."' and cash_type='cheque' and (cheque_status='0' || cheque_status='2')");
							if($getInvCheques)
							{
								if(mysqli_num_rows($getInvCheques)>0)
								{
									while($invData = mysqli_fetch_array($getInvCheques))
									{
										$outstanding_date = date_create($res['outstanding_date']);
										$cheque_date = date_create($invData['cheque_date']);
										$days = date_diff($outstanding_date,$cheque_date)->format("%a");
										//echo $days.'<br>';
                                        if($days>$res['credit_period'])
										{
											$disable = 'yes';
											array_push($chqDays_exceedsArr,array('disable'=>'yes','inv_date'=>$outstndDate,'inv_no'=>$res['ref_no'],'chq_no'=>$invData['cheque_no'],'chq_date'=>$invData['cheque_date'],'days'=>$days));
										}//if
									}//while($invData..
								}//if
							}//if
							/* credit Period End*/
							
							/* credit value Exceeded */
							$sql_cv = mysqli_query($con,"select s.id,sum(o.pending_amount) as ttlAmt_v,s.credit_value from outstandings o left outer join shops s on 
							o.party_name=s.Name where o.party_name='$shpName' and o.payment_received='0' group by o.party_name");
							if($sql_cv)
							{	
								if(mysqli_num_rows($sql_cv)==1)
								{
									$ttlAmt = mysqli_fetch_array($sql_cv);
									//echo $ttlAmt['ttlAmt_v'].'>'.$ttlAmt['credit_value'].'<br>';
                                    if($ttlAmt['ttlAmt_v']>$ttlAmt['credit_value'])
									{
										$creditValExceededArr = array('disable'=>'yes','ttlAmt'=>$ttlAmt['ttlAmt_v'],'credit_value'=>$ttlAmt['credit_value']);
									}
									else
										$creditValExceededArr = array('disable'=>'no');	
									
									$ttlPndngChequesAmt = 0;	
									$ttlPndngChequesAmt_qry = mysqli_query($con,"SELECT sum(i.amount) as ttlPndgChqAmt FROM `outstandings` o left outer join invoice_payment i on o.ref_no=i.invoice_no where o.party_name='$shpName' and i.cash_type='cheque' and i.cheque_status='0'");
									if(mysqli_num_rows($ttlPndngChequesAmt_qry)==1)
									{
										$pndngAmt = mysqli_fetch_array($ttlPndngChequesAmt_qry);
                                        $ttlPndngChequesAmt = $pndngAmt['ttlPndgChqAmt'];
										if($ttlPndngChequesAmt>0)
										{
											$outstndAmt_PndngChqAmt = $ttlAmt['ttlAmt_v']+$ttlPndngChequesAmt;
											//echo $outstndAmt_PndngChqAmt.'>'.$ttlAmt['credit_value'];
                                          	if($outstndAmt_PndngChqAmt>$ttlAmt['credit_value'])
											{
												$getInvCheques1 = mysqli_query($con,"select amount,cheque_no,cheque_date from invoice_payment where 
												shop_id='".$res['shp_id']."' and invoice_no='".$res['ref_no']."' and cash_type='cheque' and (cheque_status='0')");
												if($getInvCheques1)
												{
													if(mysqli_num_rows($getInvCheques1)>0)
													{
														while($invData1 = mysqli_fetch_array($getInvCheques1))
														{
															$disable = 'yes';
															array_push($chqAmt_exceedsArr,array('disable'=>'yes','inv_date'=>$outstndDate,'inv_no'=>$res['ref_no'],'chq_no'=>$invData1['cheque_no'],'chq_date'=>$invData1['cheque_date'],'amount'=>$invData1['amount']));
														}//while
													}//if
												}//if($getInvCheques1)
											}
										}//if($ttlPndngChequesAmt>0)
									}//if(mysqli_num_rows($ttlPndngChequesAmt_qry)..
								}//if
								else
									$creditValExceededArr = array('disable'=>'no');	
							}
							/* credit value end */
			
							/* check shop in unpresented table */
							//$pndngChqCheck_qry = mysqli_query($con,"");	
							/* unpresented end */
							
						}//while($res..
						if($disable == 'no')
							$overduesArr = array('disable'=>'no');
					}
					else
						$overduesArr = array('disable'=>'no');
				}//if($count>0)
				else
					$overduesArr = array('disable'=>'no');
			}//exe
			
			$allDataArr = array('overdue_sts'=>$overduesArr,'creditVal_sts'=>$creditValExceededArr,'chqDays_exceeds_sts'=>$chqDays_exceedsArr,'chqAmt_exceeds_sts'=>$chqAmt_exceedsArr);
			echo '{"Result":'.json_encode($allDataArr,JSON_UNESCAPED_SLASHES).'}';

		}//if(isset)
	}//con
?>