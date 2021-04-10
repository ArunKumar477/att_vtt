<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$app_user = $_GET['app_user'];
	$currDate = date("Y-m-d");
	if($con)
	{
		$getRights = "select rights from app_users where id='$app_user' and Active=1";
		$rightsExe = mysqli_query($con,$getRights);
		$rights = mysqli_fetch_array($rightsExe);
		if($rights['rights']=='2')
		{	
			$tdyChq_sql = "select b.shop_id,sh.Name,b.cheque_date,b.cheque_no,round(b.amount) as amount,b.pymnt_date from (select p.shop_id,p.cheque_date,p.cheque_no,sum(p.amount) as amount,Date(p.pymnt_date) as pymnt_date from invoice_payment p where p.cheque_date=curdate() and p.cash_type='cheque' group by p.cheque_date,p.cheque_no) b left outer join shops sh on b.shop_id=sh.id where sh.Deleted='0'";
			$ex  = mysqli_query($con,$tdyChq_sql); 
			
			$pndgChq_sql = "select s.Name,p.cheque_date,round(sum(p.amount)) as amount,p.cheque_no,Date(p.pymnt_date) as pymnt_date,p.id,p.invoice_no,p.shop_id from invoice_payment p,shops s where  p.shop_id=s.id and p.cheque_date>'$currDate' and cash_type='cheque' and s.Deleted='0' group by p.shop_id,p.cheque_date,p.cheque_no order by p.cheque_date asc";
			$ex1 = mysqli_query($con,$pndgChq_sql);
		}
		else
		{				
			$tdyChq_sql = "select b.shop_id,sh.Name,b.cheque_date,b.cheque_no,round(b.amount) as amount,b.pymnt_date from (select p.shop_id,p.cheque_date,p.cheque_no,sum(p.amount) as amount,Date(p.pymnt_date) as pymnt_date from invoice_payment p where p.cheque_date=curdate() and p.cash_type='cheque' group by p.cheque_date,p.cheque_no) b left outer join shops sh on b.shop_id=sh.id where sh.Deleted='0' and sh.fos='$app_user'";
			$ex  = mysqli_query($con,$tdyChq_sql); 
			
			$pndgChq_sql = "select s.Name,p.cheque_date,sum(p.amount) as amount,p.cheque_no,Date(p.pymnt_date) as pymnt_date,p.id,p.invoice_no,p.shop_id from invoice_payment p,shops s where p.user_id='$app_user' and p.shop_id=s.id and p.cheque_date>'$currDate' and cash_type='cheque' and s.Deleted='0' group by p.shop_id,p.cheque_date,p.cheque_no order by p.cheque_date asc";
			$ex1 = mysqli_query($con,$pndgChq_sql);
		}
		$allDataArr = array();
		$tdyChequesArr = array();
		$pndgChequesArr = array();
		$unPrsntChequesArr = array();
		if($ex)
		{
			$cnt = mysqli_num_rows($ex);
			if($cnt>0)
			{
				while($rs = mysqli_fetch_array($ex))
					array_push($tdyChequesArr,array('status'=>'success','shopName'=>$rs['Name'],'cheque_no'=>$rs['cheque_no'],'cheque_date'=>$rs['cheque_date'],'amount'=>$rs['amount'],'pymnt_date'=>$rs['pymnt_date']));
			}
		}
		if($ex1)
		{
			$cnt1 = mysqli_num_rows($ex1);
			if($cnt1>0)
			{
				while($rs1 = mysqli_fetch_array($ex1))
					array_push($pndgChequesArr,array('status'=>'success','shopName'=>$rs1['Name'],'cheque_no'=>$rs1['cheque_no'],'cheque_date'=>$rs1['cheque_date'],'amount'=>$rs1['amount'],'pymnt_date'=>$rs1['pymnt_date']));
			}
		}
		/* unpresented table data put to frontend */
		if($rights['rights']=='2')
			$chequesQuery = "select u.id,sh.Name,u.cheque_no,u.cheque_date,u.amount,Date(u.pymnt_date) as pymnt_date from unpresented_cheques u,shops sh where sh.id=u.shop_id and sh.Deleted='0'";	
		else
			$chequesQuery = "select u.id,sh.Name,u.cheque_no,u.cheque_date,u.amount,Date(u.pymnt_date) as pymnt_date from unpresented_cheques u,shops sh where sh.id=u.shop_id and sh.fos='$app_user' and sh.Deleted='0'";
		$queryExe = mysqli_query($con,$chequesQuery);
		$cnt3 = mysqli_num_rows($queryExe);
		if($cnt3>0)
		{
			while($res=mysqli_fetch_array($queryExe))
			{
				$cheque_no = $res['cheque_no'];
				$cheque_date = $res['cheque_date'];
				$amount = $res['amount'];	
				$pymnt_date = $res['pymnt_date'];
				array_push($unPrsntChequesArr ,array('status'=>'success','shopName'=>$res['Name'],'cheque_no'=>$cheque_no,'cheque_date'=>$cheque_date,'amount'=>$amount,'pymnt_date'=>$pymnt_date));
			}
		}
		if(sizeof($tdyChequesArr)!=0 || sizeof($pndgChequesArr)!=0 || sizeof($unPrsntChequesArr)!=0)
		{
			array_push($allDataArr,array('todayCheques' =>$tdyChequesArr,'pendingCheques'=>$pndgChequesArr,'unpresentedCheques'=>$unPrsntChequesArr));
			echo '{"Result":'.json_encode($allDataArr,JSON_UNESCAPED_SLASHES).'}';
		}
		else
		{
			$allDataArr = array('status'=>'emptySet');
			echo '{"Result":'.json_encode($allDataArr,JSON_UNESCAPED_SLASHES).'}';
		}
	}
?>