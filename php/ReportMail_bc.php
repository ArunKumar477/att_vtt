<?php

	//require_once('config.php');
	require_once('config_s.php');
	//date_default_timezone_set("Asia/Kolkata");
	$todayDate = date("Y-m-d");
	if($con)
	{
        $query = "select distinct user_name as fos,fos_name,email,id from app_users where active='1' and rights='0'";
		$exe = mysqli_query($con,$query);
		if($exe)
		{
			while($res = mysqli_fetch_array($exe))
			{
				$msg='';
				$email=$res['email'];
				$fos = $res['fos'];
				$fosname = $res['fos_name'];
				$id=$res['id'];
			}
			// $msg     .= '<p style="margin-left:30px;"><strong>Employee Mobile </strong> : '.$fosname.' ('.$fos.')</p>';				


			///////////// Collection Progress////////////////////////////

			$msg 	 .= '<p style="margin-left:30px;"><strong>Collection Progress </strong> </p>';
			$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
            $msg 	 .= '<tr style="background:skyblue;"><th> Name </th><th> >45 Days </th><th> 30-45 Days </th><th> 20 - 29 Days </th><th>10 - 19 Days</th><th> 0 - 9 Days </th><th> Total </th>';
				$sql      = "select 'Outstanding' as Name,a as '>45 Days',b as '30 - 45 Days',c as '20 - 29 Days',d as '10 - 19 Days',e as '0 - 9 Days',ifnull((a+b+c+d+e),0) as Total 
				from ((SELECT ifnull(round(sum(pending_amount)),0) as a FROM outstandings left outer join shops on outstandings.party_name=shops.Name where (outstandings.overdue>45) and shops.deleted='0' )a,
				(SELECT ifnull(round(sum(pending_amount)),0) as b FROM outstandings left outer join shops on outstandings.party_name=shops.Name where (outstandings.overdue between '30' and '45') and shops.deleted='0')b,
				(SELECT ifnull(round(sum(pending_amount)),0) as c FROM outstandings left outer join shops on outstandings.party_name=shops.Name where (outstandings.overdue between '20' and '29') and shops.deleted='0')c,
				(SELECT ifnull(round(sum(pending_amount)),0) as d FROM outstandings left outer join shops on outstandings.party_name=shops.Name where (outstandings.overdue between '10' and '19') and shops.deleted='0')d,
				(SELECT ifnull(round(sum(pending_amount)),0) as e FROM outstandings left outer join shops on outstandings.party_name=shops.Name where (outstandings.overdue between '0' and '9') and shops.deleted='0')e)
				union
				select 'Today Collection' as Name,a,b,c,d,e,ifnull((a+b+c+d+e),0) from (
				(SELECT ifnull(round(sum(a.amount)),0) as a from(SELECT * from  invoice_payment where ifnull(date(invoice_payment.pymnt_date),date(created))=curdate()) a where datediff((ifnull(date(pymnt_date),date(created))),sales_date) > 45 ) a,
				(SELECT ifnull(round(sum(b.amount)),0) as b from(SELECT * from  invoice_payment where ifnull(date(invoice_payment.pymnt_date),date(created))=curdate()) b where (datediff((ifnull(date(pymnt_date),date(created))),sales_date) BETWEEN 30 AND 45) ) b,
				(SELECT ifnull(round(sum(c.amount)),0) as c from(SELECT * from  invoice_payment where ifnull(date(invoice_payment.pymnt_date),date(created))=curdate() ) c where (datediff((ifnull(date(pymnt_date),date(created))),sales_date) BETWEEN 20 AND 29) ) c,
				(SELECT ifnull(round(sum(d.amount)),0) as d from(SELECT * from  invoice_payment where ifnull(date(invoice_payment.pymnt_date),date(created))=curdate() ) d where (datediff((ifnull(date(pymnt_date),date(created))),sales_date) BETWEEN 10 AND 19) ) d,
				(SELECT ifnull(round(sum(e.amount)),0) as e from(SELECT * from  invoice_payment where ifnull(date(invoice_payment.pymnt_date),date(created))=curdate() ) e where (datediff((ifnull(date(pymnt_date),date(created))),sales_date) BETWEEN 0 AND 9) ) e)";
            $ex = mysqli_query($con,$sql);
            if($ex)
            {
                
				while($rs = mysqli_fetch_array($ex))
                {
                    $msg     .= '<tr style="text-align:right;"><td style="padding:10px;text-align:left;">'. $rs['Name'] .'</td><td style="padding:10px;">'. $rs['>45 Days'] .'</td><td style="padding:10px;">'. $rs['30 - 45 Days'] .'</td><td style="padding:10px;">'. $rs['20 - 29 Days'] .'</td><td style="padding:10px;">'. $rs['10 - 19 Days'] .'</td><td style="padding:10px;">'. $rs['0 - 9 Days'] .'</td><td style="padding:10px;">'. $rs['Total'] .'</td></tr>';
                }
            }
			$msg	.= '</table>';
			
			///////////// Sales Progress////////////////	/////

			$msg 	 .= '<p style="margin-left:30px;"><strong>Sales Progress </strong> </p>';
			$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
            $msg 	 .= '<tr style="background:skyblue;"><th> Model </th><th> Target </th><th> TargetValue </th><th> Achieved </th><th> AchievedValue </th><th> Today Sales </th><th> AchievedPerc </th>';
           	$sql = "SELECT  d.product_category, a.Model as Model,d.IGST,d.dp,ifnull(a.Target,0) as Target,ifnull(round(a.target*d.dp),0) as TargetValue, ifnull(b.Achieved,0) as Achieved,ifnull(round((b.Achieved*d.dp)+(b.Achieved*d.dp*d.igst/100)),0) as AchievedValue,ifnull((c.TodaySales),0) as TodaySales, ifnull(round(b.Achieved/a.Target*100),0) as AchievedPerc from (select MT.model as Model,sum(MT.target) as Target,MT.fos from modelwise_target MT where (MT.created between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW() )  group by MT.model) a 
			   left outer join 
			   (SELECT sales.product_model as Model,COUNT(product_model) as Achieved FROM `sales` left outer join shops on sales.particulars=shops.Name where shops.Deleted='0'AND (sales_date between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW())  group by sales.product_model)b on a.model=b.model 
			   left outer join  
			   (select sales.product_model as Model,count(product_model) as TodaySales  from sales left outer join shops on sales.particulars=shops.Name where shops.Deleted='0'AND date(sales_date)=curdate()  group by sales.product_model)c on a.model=c.model
			   left outer join 
			   (SELECT product_master.product_model,product_master.dp,product_master.IGST,product_master.product_category FROM `product_master` group by product_master.product_model)d on a.model=d.product_model order by d.product_category,d.dp";
			$ex = mysqli_query($con,$sql);
			$cnt1 = mysqli_num_rows($ex); 
            if($ex)
            {
				if($cnt1>0)
				{
					$SumOfTargetValue='';
					$SumOfAchievedValue='';
					
					while($rs = mysqli_fetch_array($ex))
					{
						$msg     .= '<tr style="text-align:right;"><td style="padding:10px;text-align:left;">'. $rs['Model'] .'</td><td style="padding:10px;">'. $rs['Target'] .'</td><td style="padding:10px;">'. $rs['TargetValue'] .'</td><td style="padding:10px;">'. $rs['Achieved'] .'</td><td style="padding:10px;">'. $rs['AchievedValue'] .'</td><td style="padding:10px;">'. $rs['TodaySales'] .'</td><td style="padding:10px;">'. $rs['AchievedPerc'] .'</td></tr>';               
						$TV = $rs['TargetValue'];
						$SumOfTargetValue += $TV;

						$AV = $rs['AchievedValue'];
						$SumOfAchievedValue += $AV;
						
					}
				
					$SumOfAchievedPerc=round(($SumOfAchievedValue/$SumOfTargetValue)*100);
					$msg     .= '<tr style="text-align:right;"><td style="padding:10px;text-align:left;">'. $rs['Model'] .'</td><td style="padding:10px;">'. $rs['Target'] .'</td><td style="padding:10px;"><span style="font-weight:bold;">'. $SumOfTargetValue .'</span></td><td style="padding:10px;">'. $rs['Achieved'] .'</td><td style="padding:10px;"><span style="font-weight:bold;">'. $SumOfAchievedValue .'</span></td><td style="padding:10px;">'. $rs['TodaySales'] .'</td><td style="padding:10px;"><span style="font-weight:bold;">'. $SumOfAchievedPerc  .'</span></td></tr>';               
				}
				else
					$msg     .= '<tr style="text-align:center;"><td colspan="5"><strong>No Records Found!</strong></td></tr>';					
            }
			$msg	.= '</table>';
			

			///////////// Sales & Colletions////////////////////

			$msg 	 .= '<p style="margin-left:30px;"><strong>Sales & Colletions </strong> </p>';
			$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
            $msg 	 .= '<tr style="background:skyblue;"><th></th><th> Name </th><th> Target </th><th> Achieved </th><th> Difference </th>';
            $msg	 .= '<th> % </th></tr>';
			$sql      = "SELECT 'FTD' as Name, 
			ifnull(round(sum(b.TotalAmountperModel)/25),0) as Target,
			ifnull(round(sum(c.Achieved)),0) as Achieved,
			ifnull(round(((sum(b.TotalAmountperModel)/25)-ifnull(sum(c.Achieved),0))),0) as Difference, 
			ifnull(round((sum(c.Achieved)/(sum(b.TotalAmountperModel)/25)*100)),0) as AchievedPerc 
			from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,round(((a.Target*pm.dp))) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target   group by model) a left outer join product_master pm on a.model=pm.product_model group BY a.model,a.Target) b 
			LEFT OUTER JOIN 
			(select sales.product_model,ifnull(SUM(sales.debit_amount),0) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where date(sales.sales_date)=(curdate() ) and shops.deleted='0'  group by sales.product_model) c on b.model=c.product_model
			UNION
			select 'MTD' as Name,
			ifnull(round(sum(c.Target)),0) as Target,
			ifnull(round(sum(c.Achieved)),0) as Achieved,
			ifnull(round((sum(c.Target)-sum(c.Achieved))),0) as Difference,
			ifnull(round((sum(c.Achieved)/sum(c.Target))*100),0) as 'In %' 
			FROM (SELECT b.Model,ifnull(b.TotalAmountperModel,0) as Target,sum(s.debit_amount) as Achieved from  (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,round(((a.Target*pm.dp)/25*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() )) and (DAYOFWEEK(date(sales_date))) <> 1))) as TotalAmountperModel from (select model,sum(target) as Target from modelwise_target group by model) a left outer join product_master pm on a.model=pm.product_model group BY a.model,a.Target)  b LEFT OUTER JOIN sales s on b.model=s.product_model where (sales_date between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW() ) group by s.product_model) c
			UNION
			SELECT 'FTD' as Name,
			ifnull(round(a.AmtaboveCPoutb),0) as Target,
			ifnull(round(b.AchievedOverdue),0) as Achieved ,
			ifnull(round(a.AmtaboveCPoutb - b.AchievedOverdue),0) as Difference,
			ifnull(round(b.AchievedOverdue/a.AmtaboveCPoutb*100),0) as AchievedPerc 
			from (select '1' as N ,ifnull(round(sum(case when a.overdue>a.cp then a.pending_amount else 0 end)),0) as AmtaboveCPoutb from (SELECT shops.Name,outstandings_b.overdue,case when shops.credit_period=0 then 7 else shops.credit_period end as CP,outstandings_b.pending_amount  FROM `outstandings_b` left outer join shops on outstandings_b.party_name=shops.Name)a)a
			LEFT OUTER JOIN
			(SELECT '1' as N,SUM(invoice_payment.amount) as AchievedOverdue FROM invoice_payment LEFT OUTER JOIN shops on invoice_payment.shop_id=shops.id WHERE DATEDIFF((ifnull(date(invoice_payment.pymnt_date),date(invoice_payment.created))),date(invoice_payment.sales_date))>(case when (shops.credit_period=0 or shops.credit_period is null) then 7 else shops.credit_period end ) and date(ifnull(invoice_payment.pymnt_date,invoice_payment.created))=CURRENT_DATE())b on a.N=b.N
			UNION
			SELECT 'MTD',
			ifnull(round(c.Tar+b.AchievedOverdue),0) as Target,
			ifnull(round(b.AchievedOverdue),0) as Achieved,
			ifnull(round(c.Tar+b.AchievedOverdue-b.AchievedOverdue),0) as Difference,
			ifnull(round(b.AchievedOverdue/(c.Tar+b.AchievedOverdue)*100),0) as AchievedPerc
			FROM (SELECT a.N,ifnull((a.AmtaboveCPoutb-(SELECT SUM(invoice_payment.amount) as AchievedOverdue FROM invoice_payment LEFT OUTER JOIN shops on invoice_payment.shop_id=shops.id WHERE DATEDIFF(date(ifnull(invoice_payment.pymnt_date,invoice_payment.created)),date(invoice_payment.sales_date))>(case when (shops.credit_period=0 or shops.credit_period is null) then 7 else shops.credit_period end) and date(ifnull(invoice_payment.pymnt_date,invoice_payment.created))=CURRENT_DATE())),0) as Tar
			From (select '1' as N ,ifnull(round(sum(case when a.overdue>a.cp then a.pending_amount else 0 end)),0) as AmtaboveCPoutb from (SELECT shops.Name,outstandings_b.overdue,case when shops.credit_period=0 then 7 else shops.credit_period end as CP,outstandings_b.pending_amount  FROM `outstandings_b` left outer join shops on outstandings_b.party_name=shops.Name group by shops.Name)a)a)c
			LEFT OUTER JOIN
			(SELECT '1' as N,SUM(invoice_payment.amount) as AchievedOverdue FROM invoice_payment LEFT OUTER JOIN shops on invoice_payment.shop_id=shops.id WHERE DATEDIFF(date(ifnull(invoice_payment.pymnt_date,invoice_payment.created)),date(invoice_payment.sales_date))>(case when (shops.credit_period=0 or shops.credit_period is null) then 7 else shops.credit_period end ) and date(ifnull(invoice_payment.pymnt_date,invoice_payment.created)) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW())b on b.N=c.N
			UNION
			SELECT 'FTD' as Name,
			ifnull(round(a.AmtaboveCPoutb),0) as Target,
			ifnull(round(b.AchievedOverdue),0) as Achieved ,
			ifnull(round(a.AmtaboveCPoutb - b.AchievedOverdue),0) as Difference,
			ifnull(round(b.AchievedOverdue/a.AmtaboveCPoutb*100),0) as AchievedPerc 
			from (select '1' as N ,ifnull(round(sum(case when a.overdue<=a.cp then a.pending_amount else 0 end)),0) as AmtaboveCPoutb from (SELECT shops.Name,outstandings_b.overdue,case when shops.credit_period=0 then 7 else shops.credit_period end as CP,outstandings_b.pending_amount  FROM `outstandings_b` left outer join shops on outstandings_b.party_name=shops.Name )a)a
			LEFT OUTER JOIN
			(SELECT '1' as N,SUM(invoice_payment.amount) as AchievedOverdue FROM invoice_payment LEFT OUTER JOIN shops on invoice_payment.shop_id=shops.id WHERE DATEDIFF((ifnull(date(invoice_payment.pymnt_date),date(invoice_payment.created))),date(invoice_payment.sales_date))<=(case when (shops.credit_period=0 or shops.credit_period is null) then 7 else shops.credit_period end ) and date(ifnull(invoice_payment.pymnt_date,invoice_payment.created))=CURRENT_DATE())b on a.N=b.N
			UNION
			SELECT
			'MTD',
			ifnull(round(c.Tar+b.AchievedOverdue),0) as Target,
			ifnull(round(b.AchievedOverdue),0) as Achieved,
			ifnull(round(c.Tar+b.AchievedOverdue-b.AchievedOverdue),0) as Difference,
			ifnull(round(b.AchievedOverdue/(c.Tar+b.AchievedOverdue)*100),0) as AchievedPerc
			FROM (SELECT a.N,ifnull((a.AmtaboveCPoutb-(SELECT SUM(invoice_payment.amount) as AchievedOverdue FROM invoice_payment LEFT OUTER JOIN shops on invoice_payment.shop_id=shops.id WHERE DATEDIFF(date(ifnull(invoice_payment.pymnt_date,invoice_payment.created)),date(invoice_payment.sales_date))<=(case when (shops.credit_period=0 or shops.credit_period is null) then 7 else shops.credit_period end) and date(ifnull(invoice_payment.pymnt_date,invoice_payment.created))=CURRENT_DATE())),0) as Tar
			From (select '1' as N ,ifnull(round(sum(case when a.overdue<=a.cp then a.pending_amount else 0 end)),0) as AmtaboveCPoutb from (SELECT shops.Name,outstandings_b.overdue,case when shops.credit_period=0 then 7 else shops.credit_period end as CP,outstandings_b.pending_amount  FROM `outstandings_b` left outer join shops on outstandings_b.party_name=shops.Name group by shops.Name)a)a)c
			LEFT OUTER JOIN
			(SELECT '1' as N,SUM(invoice_payment.amount) as AchievedOverdue FROM invoice_payment LEFT OUTER JOIN shops on invoice_payment.shop_id=shops.id WHERE DATEDIFF(date(ifnull(invoice_payment.pymnt_date,invoice_payment.created)),date(invoice_payment.sales_date))<=(case when (shops.credit_period=0 or shops.credit_period is null) then 7 else shops.credit_period end ) and date(ifnull(invoice_payment.pymnt_date,invoice_payment.created)) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW())b on b.N=c.N";
			$ex = mysqli_query($con,$sql);
            if($ex)
            {
                $inc = 1;
				while($rs = mysqli_fetch_array($ex))
                {
                    $msg     .= '<tr style="text-align:right;">';
					if($inc==1)
						$msg	 .= '<td rowspan="2" width="8%"  style="text-align:center"><strong>Sales</strong></td>';
					if($inc==3)
						$msg	 .= '<td rowspan="2" width="8%" style="text-align:center"><strong>Overdue Outstanding Collection</strong></td>';
					if($inc==5)
						$msg	 .= '<td rowspan="2" width="8%"  style="text-align:center;"><strong>Current Outstanding Collection</strong></td>';
					$msg	 .= '<td width="5%" style="padding:10px;text-align:left;">'. $rs['Name'] .'</td><td width="10%" style="padding:10px;">'. $rs['Target'] .'</td><td width="10%"  style="padding:10px;">'. $rs['Achieved'] .'</td><td width="10%" style="padding:10px;">'. $rs['Difference'] .'</td><td width="10%"  style="padding:10px;">'. $rs['AchievedPerc'] .'</td></tr>';
                	$inc++;
				}
            }
			$msg	.= '</table>';
			




			
			
			///////////// Store visit//////////////////////////
			
			$msg 	 .= '<p style="margin-left:30px;"><strong> Store Visit </strong> </p>';
            $msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
            $msg 	 .= '<tr><th rowspan="3"> Fos Name </th><th colspan="6">Counter Stock</th><th colspan="4">Store Visit</th></tr>';
			$msg 	 .= '<tr><th colspan="3"> FTD </th><th colspan="3"> MTD </th><th colspan="2"> FTD </th><th colspan="2"> MTD </th></tr>';
			$msg 	 .= '<tr style="background:skyblue;"><th> Target </th><th> Achieved </th><th> Percentage(%) </th><th> Target </th><th> Achieved </th><th> Percentage(%) </th><th> Target </th><th> Achieved </th><th> Target </th><th> Achieved </th></tr>';
			
			$sql_t=mysqli_query($con,"SET SQL_BIG_SELECTS=1");
			$sql="SELECT c.fos_name as  fos_name, c.CS_FTD_Target, c.CS_FTD_Achieved, c.CS_FTD_AchievedPerc,
		d.CS_MTD_Target,
		(d.CS_MTD_Achieved1+ifnull(e.achieved2,0))as CS_MTD_Achieved,
		 round(((d.CS_MTD_Achieved1+ifnull(e.achieved2,0))/d.CS_MTD_Target*100)) as CS_MTD_AchievedPerc,
        '20' as SV_FTD_Target,
        ifnull(f.sv_FTD_Achivd,0) as SV_FTD_Achieved,
		ifnull(ff.sv_MTD_Target,0) as SV_MTD_Target,
		ifnull(fff.sv_MTD_Achieved,0) as SV_MTD_Achieved
		from 
		(select a.fos_name as fos_name,a.Target as CS_FTD_Target,
		ifnull(b.achieved,0) as CS_FTD_Achieved,
		ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as CS_FTD_AchievedPerc 
		from
				(SELECT app_users.fos_name,'15' as Target FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.active=1 and app_users.rights=0 and shops.Deleted='0' and app_users.id <> '15' group by app_users.user_name) a 
			LEFT OUTER JOIN 
				(select app_users.fos_name, ifnull(round(count(shopid)),0) as Achieved from stocks_c left outer join app_users on stocks_c.userid=app_users.id where date(datetime)=curdate()  and app_users.active=1 and app_users.rights=0 group by userid) b
				on a.fos_name=b.fos_name
			LEFT OUTER JOIN
				(SELECT fos_name,count(stocks_c.shopid) as counterstock
				from app_users LEFT outer join stocks_c on app_users.id=stocks_c.userid
				where date(stocks_c.datetime)=curdate()) c on a.fos_name=c.fos_name) c
			LEFT OUTER JOIN
					(select a.fos_name as fos_name,a.Target as CS_MTD_Target,ifnull(b.achieved,0) as CS_MTD_Achieved1 , 
					ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as CS_MTD_AchievedPerc 
					from (SELECT app_users.fos_name,ifnull((15*(SELECT count(distinct date(datetime)) FROM `stocks_h` 
					where (date(datetime) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate()) and (DAYOFWEEK(date(datetime))) <> 1)),0) as Target FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.active=1 and app_users.rights=0 and shops.deleted='0' and app_users.id <> '15' group by app_users.fos_name) a 
			LEFT outer JOIN 
					(select app_users.fos_name, ifnull(round(count(stocks_h.shopid)),0)   as Achieved from stocks_h left outer join app_users on stocks_h.userid=app_users.id 
					where 
					(date(stocks_h.datetime) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.active=1 and app_users.rights=0 group by fos_name) b
					on a.fos_name=b.fos_name
					LEFT OUTER JOIN
					(SELECT fos_name,count(stocks_c.shopid) as counterstock
					from app_users LEFT outer join stocks_c on app_users.id=stocks_c.userid
					where (date(stocks_c.datetime) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate())) c
					on a.fos_name=c.fos_name) d
					on c.fos_name=d.fos_name
					LEFT OUTER JOIN 
					(select app_users.fos_name, ifnull(round(count(stocks_c.shopid)),0) as Achieved2 from stocks_c left outer join app_users on stocks_c.userid=app_users.id 
					where 
					(date(stocks_c.datetime) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.active=1 and app_users.rights=0 group by fos_name) e
					on e.fos_name=d.fos_name
					
					LEFT OUTER JOIN
					
					(select app_users.fos_name,ifnull(round(count(distinct shop_id)),0) as sv_FTD_Achivd from attendance left outer join app_users on attendance.fos=app_users.user_name where date(attendance_date)=curdate() and app_users.active=1 and app_users.rights=0 and app_users.id <> '15' group by fos) f
					on f.fos_name=e.fos_name
					
					LEFT OUTER JOIN
					
					(SELECT app_users.fos_name,ifnull((20*(SELECT count(distinct date(attendance_date)) FROM `attendance` where (date(attendance_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate()) and (DAYOFWEEK(date(attendance_date))) <> 1)),0) as sv_MTD_Target FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.active=1 and app_users.rights=0 and shops.deleted='0' and app_users.id <> '15' group by app_users.user_name) ff 
					on ff.fos_name=e.fos_name
					
					LEFT OUTER JOIN 
					(select app_users.fos_name, ifnull(round(count(shop_id)),0) as sv_MTD_Achieved from attendance left outer join app_users on attendance.fos=app_users.user_name where (date(attendance_date) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.active=1 and app_users.rights=0 and app_users.id <> '15' group by fos) fff
					on fff.fos_name=ff.fos_name";
			/*$sql="SELECT c.Fos_Name as FOS_Name,c.FTD_Target,c.FTD_Achieved,c.FTD_Difference,c.FTD_AchievedPerc,c.FTD_CounterStock,d.MTD_Target,d.MTD_Achieved,d.MTD_Difference,d.MTD_AchievedPerc,d.MTD_CounterStock from (select a.fos_name as FOS_Name,a.Target as FTD_Target,ifnull(b.achieved,0) as FTD_Achieved, ifnull((a.Target-b.achieved),0) as FTD_Difference,ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as FTD_AchievedPerc,ifnull(c.counterstock,0) as FTD_CounterStock   from (SELECT app_users.fos_name,'20' as Target FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.active=1 and app_users.rights=0 and shops.Deleted='0' and app_users.id <> '15'  group by app_users.user_name) a 
			LEFT OUTER JOIN 
			(select app_users.fos_name, ifnull(round(count(shop_id)),0) as Achieved from attendance left outer join app_users on attendance.fos=app_users.user_name where date(attendance_date)=curdate()  and app_users.active=1 and app_users.rights=0 and app_users.id <> '15' group by fos) b
			on a.fos_name=b.fos_name
			LEFT OUTER JOIN
			(SELECT fos_name,count(stocks_c.shopid) as counterstock
			from app_users LEFT outer join stocks_c on app_users.id=stocks_c.userid
			where date(stocks_c.datetime)=curdate()) c on a.fos_name=c.fos_name) c
			LEFT OUTER JOIN
			(select a.fos_name as FOS_Name,a.Target as MTD_Target,ifnull(b.achieved,0) as MTD_Achieved , ifnull((a.Target-b.achieved),0) as MTD_Difference,ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as MTD_AchievedPerc,ifnull(c.counterstock,0) as MTD_CounterStock from (SELECT app_users.fos_name,ifnull((20*(SELECT count(distinct date(attendance_date)) FROM `attendance` where (date(attendance_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate()) and (DAYOFWEEK(date(attendance_date))) <> 1)),0) as Target FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.active=1 and app_users.rights=0 and shops.deleted='0' and app_users.id <> '15' group by app_users.user_name) a 
			LEFT outer JOIN 
			(select app_users.fos_name, ifnull(round(count(shop_id)),0) as Achieved from attendance left outer join app_users on attendance.fos=app_users.user_name where (date(attendance_date) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.active=1 and app_users.rights=0 and app_users.id <> '15' group by fos) b
			on a.fos_name=b.fos_name
			LEFT OUTER JOIN
			(SELECT fos_name,count(stocks_c.shopid) as counterstock
			from app_users LEFT outer join stocks_c on app_users.id=stocks_c.userid
			where (date(stocks_c.datetime) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate())) c
			on a.fos_name=c.fos_name) d
			
			on c.FOS_Name=d.FOS_Name";*/
            $ex = mysqli_query($con,$sql);
            if($ex)
            {
                while($rs = mysqli_fetch_array($ex))
                {
					$msg     .= '<tr style="text-align:right;"><td style="padding:10px;text-align:left">'. $rs['fos_name'] .'</td><td style="padding:10px;">'. $rs['CS_FTD_Target'] .'</td><td style="padding:10px;">'. $rs['CS_FTD_Achieved'] .'</td><td style="padding:10px;">'. $rs['CS_FTD_AchievedPerc'] .'</td><td style="padding:10px;">'. $rs['CS_MTD_Target'] .'</td><td style="padding:10px;">'. $rs['CS_MTD_Achieved'] .'</td><td style="padding:10px;">'. $rs['CS_MTD_AchievedPerc'] .'</td><td style="padding:10px;">'. $rs['SV_FTD_Target'] .'</td><td style="padding:10px;">'. $rs['SV_FTD_Achieved'] .'</td><td style="padding:10px;">'. $rs['SV_MTD_Target'] .'</td><td style="padding:10px;">'. $rs['SV_MTD_Achieved'] .'</td></tr>';
                }
            }
			$msg	.= '</table>';
			
					//////////// Value Target//////////////////////////
			
			// $sql="SELECT  app_users.fos_name,e.TargetB,ifnull(f.Achieved,0) as Achieved,ifnull(round(f.Achieved/e.TargetB*100),0) as TargetBPerc from (select d.fos,d.TargetA,sum(shops.target_b) as TargetB from (select fos,sum(TargetValue) as TargetA FROM (select a.fos,ifnull(round((a.target*b.dp)),0) as TargetValue from (select MT.model as Model,sum(MT.target) as Target,MT.fos from modelwise_target MT where (MT.created between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW() )  group by MT.model ,MT.fos order by MT.fos ) a 			   
			// left outer join (SELECT product_master.product_model,product_master.dp FROM `product_master` GROUP by product_master.product_model ) b on a.model=b.product_model) c group by c.fos) d left outer join shops on d.fos=shops.fos where shops.Deleted='0' and shops.target_b<>'0' group by d.fos) e left outer join (SELECT shops.fos,sum(debit_amount) as Achieved FROM sales LEFT OUTER JOIN shops on sales.particulars=shops.Name WHERE month(sales.sales_date)=month(curdate()) and shops.deleted='0' and shops.target_b<>'0' group by shops.fos) f  on e.fos=f.fos left outer join app_users on e.fos=app_users.id where app_users.active=1 and app_users.rights=0";
			// //$db=mysqli_query($con,"SET SQL_BIG_SELECTS=1");
			// $ex = mysqli_query($con,$sql);
			// $cnt = mysqli_num_rows($ex);
			// // if($cnt>0)
			// // 	$dispNone = "display:none";
			// // else
			// // 	$dispNone = "display:block";
			// $msg 	 .= '<p style="margin-left:30px;"><strong> Value Target </strong> </p>';
			// $msg     .= '<table id="tblvaltargt" border="1" width="60%"  style="border-collapse: collapse;margin-left:30px;">'; 
			// $msg 	 .= '<tr style="background:skyblue;"><th> Fos Name </th><th> Target Value </th><th> Achieved </th><th> Target Value (%) </th></tr>';
		
			
			
			// if($ex)
			// {
			// 	while($rs = mysqli_fetch_array($ex))
			// 	{
			// 		$msg     .= '<tr style="text-align:right;"><td style="padding:10px;text-align:left">'. $rs['fos_name'] .'</td><td style="padding:10px;">'. $rs['TargetB'] .'</td><td style="padding:10px;">'. $rs['Achieved'] .'</td><td style="padding:10px;">'. $rs['TargetBPerc'] .'</td></tr>';
			// 	}
			// }
			// else
			// 	$msg     .= '<tr style="text-align:center;"><td colspan="5"><strong>No Records Found!</strong></td></tr>';					
			// $msg	.= '</table>';
			
			/* 
			//////Rank Table -> Attendance Visit Score Column Query/////
			 
				$FOSRank_query      = "select Name,SalesScore,CollectionScore,VisitScore,OverallScore, @cnt:=@cnt+1 AS row_number from (select app_users.fos_name as Name,round(fs.SFOSPerc+hc.FTDSFOSPerc+hc.MTDSFOSPerc) as SalesScore,round(hc.FTDCFOSPerc+hc.MTDCFOSPerc) as CollectionScore,round(gb.BFOSperc) as VisitScore,round(fs.SFOSPerc+gb.BFOSperc+hc.FTDSFOSPerc+hc.MTDSFOSPerc+hc.FTDCFOSPerc+hc.MTDCFOSPerc) as OverallScore
						from (
										(SELECT FOS,ifnull((((SUM(TotalPerc)*100)*(SELECT percentage from PP_Weightage where type='SP'))/100),0) as SFOSPerc FROM (SELECT 
										a.fos as FOS,
										a.Model as Model,
										ifnull(a.Target,0) as Target,
										ifnull((a.target*d.dp),0) as TargetValue, 
										ifnull(b.Achieved,0) as Achieved,
										ifnull((b.Achieved*d.dp),0) as AchievedValue,
										ifnull((c.TodaySales),0) as TodaySales, 
										ifnull((b.Achieved/a.Target*100),0) as AchievedPerc,
										d.dp as DP,
										d.dp/(select sum(dp) as DPTotal FROM (SELECT modelwise_target.model,product_master.dp as DP FROM modelwise_target LEFT outer join  product_master on modelwise_target.model=product_master.product_model group by modelwise_target.model) a) as DPCalc,
										case when (b.Achieved/a.Target*100)>100 THEN '100' else ifnull((b.Achieved/a.Target*100),0) end as AchievedPercOrg, 
										(case when (b.Achieved/a.Target*100)>100 THEN '100' else ifnull((b.Achieved/a.Target*100),0) end)*((d.dp/(select sum(dp) as DPTotal FROM (SELECT modelwise_target.model,product_master.dp as DP FROM modelwise_target LEFT outer join  product_master on modelwise_target.model=product_master.product_model group by modelwise_target.model) a)))/100 as TotalPerc
										from (SELECT MT.model as Model,sum(MT.target) as Target,MT.fos from modelwise_target MT where (MT.created between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW() )  group by MT.fos,MT.model) a 
										left outer join 
										(SELECT sales.product_model as Model,COUNT(product_model) as Achieved,shops.fos as Fos FROM `sales` left outer join shops on sales.particulars=shops.Name where (sales_date between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW())  group by shops.fos,sales.product_model)b on a.model=b.model and a.fos=b.fos
										left outer join  
										(select sales.product_model as Model,count(product_model) as TodaySales,shops.fos as Fos  from sales left outer join shops on sales.particulars=shops.Name where date(sales_date)=curdate()   group by shops.fos,sales.product_model)c  on b.model=c.model and b.fos=c.fos
										left outer join 
										(SELECT product_master.product_model,product_master.dp FROM `product_master` group by product_master.product_model)d on a.model=d.product_model) Total group by FOS) fs
								
										LEFT OUTER JOIN
								
										(SELECT FOS,
										SUM(CASE WHEN total.Name='FTDB' then ((AchievedPercOrg)*(SELECT Percentage FROM `PP_Weightage` where Type='FTDB')/100) ELSE 0 end) +
										SUM(CASE WHEN total.Name='MTDB' then ((AchievedPercOrg)*(SELECT Percentage FROM `PP_Weightage` where Type='MTDB')/100) else 0 end )as BFOSPerc 
										FROM (select 'FTDB' as Name,a.fos,a.Target as Target,ifnull(b.achieved,0) as Achieved, ifnull((a.Target-b.achieved),0) as Difference,ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0) as AchievedPerc ,CASE WHEN ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0) > 100 THEN 100 else ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0) END  as AchievedPercOrg  from (SELECT app_users.fos_name,'20' as Target,app_users.id as fos FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.Active=1 and app_users.rights=0 and shops.deleted='0'  group by app_users.user_name) a 
										LEFT outer JOIN 
										(select app_users.fos_name, ifnull((count(shop_id)),0) as Achieved,app_users.id as fos from attendance left outer join app_users on attendance.fos=app_users.user_name where date(attendance_date)=curdate()  and app_users.Active=1 and app_users.rights=0 group by fos) b
										on a.fos_name=b.fos_name
										union
										(select 'MTDB',a.fos,a.Target, ifnull(b.achieved,0), ifnull((a.Target-b.achieved),0),ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0), CASE WHEN ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0) > 100 THEN 100 else ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0) END   from (SELECT app_users.fos_name,ifnull((20*(SELECT count(distinct date(attendance_date)) FROM `attendance` where (date(attendance_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate()) and (DAYOFWEEK(date(attendance_date))) <> 1)),0) as Target,app_users.id as fos FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.Active=1 and app_users.rights=0 and shops.deleted='0'    group by app_users.user_name) a 
										LEFT outer JOIN 
						
										(select app_users.fos_name, ifnull((count(shop_id)),0) as Achieved,app_users.id from attendance left outer join app_users on attendance.fos=app_users.user_name where (date(attendance_date) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.Active=1 and app_users.rights=0  group by fos) b
										on a.fos_name=b.fos_name)) total group by FOS) gb on fs.fos=gb.fos
										
										LEFT OUTER JOIN
						
										(SELECT 
										FOSPerc.FOS,
										sum(CASE WHEN FOSPerc.Name='FTDS' THEN AchievedPercOrg*(SELECT Percentage FROM `PP_Weightage` where Type='FTDS')/100 else 0 end) as FTDSFOSPerc,
										sum(CASE WHEN FOSPerc.Name='MTDS' THEN AchievedPercOrg*(SELECT Percentage FROM `PP_Weightage` where Type='MTDS')/100 else 0 end) as MTDSFOSPerc,
										sum(CASE WHEN FOSPerc.Name='FTDC' THEN AchievedPercOrg*(SELECT Percentage FROM `PP_Weightage` where Type='FTDC')/100 else 0 end) as FTDCFOSPerc,
										sum(CASE WHEN FOSPerc.Name='MTDC' THEN AchievedPercOrg*(SELECT Percentage FROM `PP_Weightage` where Type='MTDC')/100 else 0 end) as MTDCFOSPerc FROM(
										SELECT d.Name,d.fos as FOS,ifnull((c.Achieved/d.Target*100),0) as AchievedPerc,CASE when ifnull((c.Achieved/d.Target*100),0)>100 then 100 else ifnull((c.Achieved/d.Target*100),0) END as AchievedPercOrg from (Select 'FTDS' as Name, ifnull((sum(b.TotalAmountperModel)/25),0) as Target, b.fos from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,(a.Target*pm.dp) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target   group by fos,model) a left outer join product_master pm on a.model=pm.product_model group BY a.fos,a.model,a.Target) b group by b.fos) d
										left outer join 
										(SELECT SUM(sales.debit_amount) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where date(sales.sales_date)=curdate()   group by shops.fos)c on d.fos=c.fos
										union 
										select d.Name,d.fos as FOS,ifnull((c.Achieved/d.Target*100),0) as AchievedPerc,CASE when ifnull((c.Achieved/d.Target*100),0)>100 then 100 else ifnull((c.Achieved/d.Target*100),0) END as AchievedPercOrg from (SELECT 'MTDS' as Name, ifnull(((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate() ) and (DAYOFWEEK(date(sales_date))) <> 1)),0) as Target,b.fos from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,(a.Target*pm.dp) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target   group by fos,model) a left outer join product_master pm on a.model=pm.product_model group BY a.fos, a.model,a.Target) b group by b.fos) d 
										LEFT OUTER JOIN
										(select SUM(sales.debit_amount) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where (sales_date between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) group by shops.fos) c on d.fos=c.fos
										union 
										select 'FTDC', a.fos as FOS,ifnull((b.Achieved/(DueAmtOutb+b.Achieved)*100),0) as AchievedPerc, CASE WHEN ifnull((b.Achieved/(DueAmtOutb+b.Achieved)*100),0) > 100 THEN 100 ELSE ifnull((b.Achieved/(DueAmtOutb+b.Achieved)*100),0) END as AchievedPercOrg   from (select a.fos as FOS,ifnull(round(sum(case when a.overdue>a.cp then a.pending_amount else 0 end)),0) as DueAmtOutb from (SELECT shops.fos,shops.Name,outstandings_b.overdue,case when shops.credit_period=0 then 7 else shops.credit_period end as CP,outstandings_b.pending_amount  FROM outstandings_b left outer join shops on outstandings_b.party_name=shops.Name group by shops.Name)a group by a.fos) a 
										left outer join 
										(SELECT user_id as FOS,ifnull((sum(amount)),0) as Achieved FROM `invoice_payment` WHERE ifnull(date(pymnt_date),date(created))=curdate()  group by user_id) b on a.fos=b.fos where a.fos is not null
										union
										select 'MTDC' as Name,d.fos,ifnull((e.Achieved/(d.Tar+e.Achieved)*100),0) as AchievedPerc,CASE when ifnull((e.Achieved/(d.Tar+e.Achieved)*100),0)>100 then 100 else ifnull((e.Achieved/(d.Tar+e.Achieved)*100),0) END as AchievedPercOrg from (select b.fos,ifnull((b.DueAmtOutb-c.FTDCAchieved),0) as Tar  from (SELECT ifnull(round(sum(case when a.overdue>a.cp then a.pending_amount else 0 end)),0) as DueAmtOutb,a.fos from (SELECT shops.fos, shops.Name,outstandings_b.overdue,case when shops.credit_period=0 then 7 else shops.credit_period end as CP,outstandings_b.pending_amount  FROM `outstandings_b` left outer join shops on outstandings_b.party_name=shops.Name group by shops.Name)a group by a.fos) b left outer JOIN (SELECT invoice_payment.user_id as fos, ifnull(round(sum(amount)),0) as FTDCAchieved FROM `invoice_payment` WHERE ifnull(date(pymnt_date),date(created))=curdate() group by invoice_payment.user_id) c on b.fos=c.fos) d
										left OUTER join 
										(SELECT user_id as FOS,ifnull((sum(amount)),0) as Achieved FROM `invoice_payment` WHERE (ifnull(date(pymnt_date),date(created)) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) group by user_id)e on d.fos=e.fos)FOSPerc left outer join app_users on FOSPerc.FOS=app_users.id where app_users.Active=1 and app_users.rights=0 GROUP by FOSPerc.FOS
										)hc on fs.fos=hc.fos left outer join app_users on fs.fos=app_users.id  ) WHERE app_users.active=1 and app_users.rights=0 and fs.fos not in ('7','9')) d order by d.OverallScore desc";
			*/

			///////////// Rank ////////////////////

			$msg 	 .= '<p style="margin-left:30px;"><strong>Rank </strong> </p>';
			$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
            $msg 	 .= '<tr style="background:skyblue;"><th> Name </th><th> Sales Score </th><th> Collection Score </th><th> 
			Counter Stock </th><th> Overall Score </th><th> Rank </th></tr>';
			$FOSRank_query = "select Name,SalesScore,CollectionScore,CounterStock,OverallScore, @cnt:=@cnt+1 AS row_number from (select app_users.fos_name as Name,round(fs.SFOSPerc+hc.FTDSFOSPerc+hc.MTDSFOSPerc) as SalesScore,round(hc.FTDCFOSPerc+hc.MTDCFOSPerc) as CollectionScore,round(gb.BFOSperc) as CounterStock,(fs.SFOSPerc+gb.BFOSperc+hc.FTDSFOSPerc+hc.MTDSFOSPerc+hc.FTDCFOSPerc+hc.MTDCFOSPerc) as OverallScore
from (
	(SELECT FOS,ifnull((((SUM(TotalPerc)*100)*(SELECT percentage from PP_Weightage where type='SP'))/100),0) as SFOSPerc FROM (SELECT 
	a.fos as FOS,
	a.Model as Model,
	ifnull(a.Target,0) as Target,
	ifnull((a.target*d.dp),0) as TargetValue, 
	ifnull(b.Achieved,0) as Achieved,
	ifnull((b.Achieved*d.dp),0) as AchievedValue,
	ifnull((c.TodaySales),0) as TodaySales, 
	ifnull((b.Achieved/a.Target*100),0) as AchievedPerc,
	d.dp as DP,
	d.dp/(select sum(dp) as DPTotal FROM (SELECT modelwise_target.model,product_master.dp as DP FROM modelwise_target LEFT outer join  product_master on modelwise_target.model=product_master.product_model group by modelwise_target.model) a) as DPCalc,
	case when (b.Achieved/a.Target*100)>100 THEN '100' else ifnull((b.Achieved/a.Target*100),0) end as AchievedPercOrg, 
	(case when (b.Achieved/a.Target*100)>100 THEN '100' else ifnull((b.Achieved/a.Target*100),0) end)*((d.dp/(select sum(dp) as DPTotal FROM (SELECT modelwise_target.model,product_master.dp as DP FROM modelwise_target LEFT outer join  product_master on modelwise_target.model=product_master.product_model group by modelwise_target.model) a)))/100 as TotalPerc
	from (SELECT MT.model as Model,sum(MT.target) as Target,MT.fos from modelwise_target MT where (MT.created between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW() )  group by MT.fos,MT.model) a 
	left outer join 
	(SELECT sales.product_model as Model,COUNT(product_model) as Achieved,shops.fos as Fos FROM `sales` left outer join shops on sales.particulars=shops.Name where (sales_date between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW())  group by shops.fos,sales.product_model)b on a.model=b.model and a.fos=b.fos
	left outer join  
	(select sales.product_model as Model,count(product_model) as TodaySales,shops.fos as Fos  from sales left outer join shops on sales.particulars=shops.Name where date(sales_date)=curdate()   group by shops.fos,sales.product_model)c  on b.model=c.model and b.fos=c.fos
	left outer join 
	(SELECT product_master.product_model,product_master.dp FROM `product_master` group by product_master.product_model)d on a.model=d.product_model) Total group by FOS) fs

	LEFT OUTER JOIN

	(SELECT FOS,
	SUM(CASE WHEN total.Name='FTDB' then ((AchievedPercOrg)*(SELECT Percentage FROM `PP_Weightage` where Type='FTDB')/100) ELSE 0 end) +
	SUM(CASE WHEN total.Name='MTDB' then ((AchievedPercOrg)*(SELECT Percentage FROM `PP_Weightage` where Type='MTDB')/100) else 0 end )as BFOSPerc 
	FROM (select 'FTDB' as Name,a.fos,a.Target as Target,ifnull(b.achieved,0) as Achieved, ifnull((a.Target-b.achieved),0) as Difference,ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0) as AchievedPerc ,CASE WHEN ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0) > 100 THEN 100 else ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0) END  as AchievedPercOrg  from (SELECT app_users.fos_name,'15' as Target,app_users.id as fos FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.Active=1 and app_users.rights=0 and shops.deleted='0'  group by app_users.user_name) a 
	LEFT outer JOIN 
	
	(select app_users.fos_name, ifnull((count(shopid)),0) as Achieved,app_users.id as fos
	from stocks_c left outer join app_users 
	on stocks_c.userid=app_users.id 
	where date(datetime)=curdate()  
	and app_users.Active=1 
	and app_users.rights=0 group by fos) b
	on a.fos_name=b.fos_name
	
	union
	(select 'MTDB',a.fos,a.Target, 
	ifnull(b.achieved,0), ifnull((a.Target-b.achieved),0),ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0), 
	CASE WHEN ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0) > 100 THEN 100 else ifnull((((ifnull(b.achieved,0))/(a.Target)*100)),0) END   
	from 
	(SELECT app_users.fos_name,ifnull((15*(SELECT count(distinct date(datetime)) 
	FROM `stocks_h` 
	where (date(datetime) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate()) 
	and (DAYOFWEEK(date(datetime))) <> 1)),0) as Target,app_users.id as fos 
	FROM app_users LEFT OUTER JOIN shops 
	on app_users.id=shops.fos 
	where app_users.Active=1 
	and app_users.rights=0 
	and shops.deleted='0'    group by app_users.fos_name) a 
	LEFT outer JOIN 
	(select app_users.fos_name, ifnull((count(shopid)),0) as Achieved,app_users.id 
	from stocks_c left outer join app_users on stocks_c.userid=app_users.id 
	where (date(datetime) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.Active=1 
	and app_users.rights=0  group by userid) b
	on a.fos_name=b.fos_name)) total group by fos) gb on fs.fos=gb.fos
	
	
	LEFT OUTER JOIN

	(SELECT 
	FOSPerc.FOS,
	sum(CASE WHEN FOSPerc.Name='FTDS' THEN AchievedPercOrg*(SELECT Percentage FROM `PP_Weightage` where Type='FTDS')/100 else 0 end) as FTDSFOSPerc,
	sum(CASE WHEN FOSPerc.Name='MTDS' THEN AchievedPercOrg*(SELECT Percentage FROM `PP_Weightage` where Type='MTDS')/100 else 0 end) as MTDSFOSPerc,
	sum(CASE WHEN FOSPerc.Name='FTDC' THEN AchievedPercOrg*(SELECT Percentage FROM `PP_Weightage` where Type='FTDC')/100 else 0 end) as FTDCFOSPerc,
	sum(CASE WHEN FOSPerc.Name='MTDC' THEN AchievedPercOrg*(SELECT Percentage FROM `PP_Weightage` where Type='MTDC')/100 else 0 end) as MTDCFOSPerc FROM(
	SELECT d.Name,d.fos as FOS,ifnull((c.Achieved/d.Target*100),0) as AchievedPerc,CASE when ifnull((c.Achieved/d.Target*100),0)>100 then 100 else ifnull((c.Achieved/d.Target*100),0) END as AchievedPercOrg from (Select 'FTDS' as Name, ifnull((sum(b.TotalAmountperModel)/25),0) as Target, b.fos from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,(a.Target*pm.dp) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target   group by fos,model) a left outer join product_master pm on a.model=pm.product_model group BY a.fos,a.model,a.Target) b group by b.fos) d
	left outer join 
	(SELECT SUM(sales.debit_amount) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where date(sales.sales_date)=curdate()   group by shops.fos)c on d.fos=c.fos
	union 
	select d.Name,d.fos as FOS,ifnull((c.Achieved/d.Target*100),0) as AchievedPerc,CASE when ifnull((c.Achieved/d.Target*100),0)>100 then 100 else ifnull((c.Achieved/d.Target*100),0) END as AchievedPercOrg from (SELECT 'MTDS' as Name, ifnull(((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate() ) and (DAYOFWEEK(date(sales_date))) <> 1)),0) as Target,b.fos from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,(a.Target*pm.dp) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target   group by fos,model) a left outer join product_master pm on a.model=pm.product_model group BY a.fos, a.model,a.Target) b group by b.fos) d 
	LEFT OUTER JOIN
	(select SUM(sales.debit_amount) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where (sales_date between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) group by shops.fos) c on d.fos=c.fos
	union 
	select 'FTDC', a.fos as FOS,ifnull((b.Achieved/(DueAmtOutb+b.Achieved)*100),0) as AchievedPerc, CASE WHEN ifnull((b.Achieved/(DueAmtOutb+b.Achieved)*100),0) > 100 THEN 100 ELSE ifnull((b.Achieved/(DueAmtOutb+b.Achieved)*100),0) END as AchievedPercOrg   from (select a.fos as FOS,round(sum(case when a.overdue>a.cp then a.pending_amount else 0 end)) as DueAmtOutb from (SELECT shops.fos,shops.Name,outstandings_b.overdue,case when shops.credit_period=0 then 7 else shops.credit_period end as CP,outstandings_b.pending_amount  FROM outstandings_b left outer join shops on outstandings_b.party_name=shops.Name group by shops.Name)a group by a.fos) a 
	left outer join 
	(SELECT user_id as FOS,ifnull((sum(amount)),0) as Achieved FROM `invoice_payment` WHERE ifnull(date(pymnt_date),date(created))=curdate()  group by user_id) b on a.fos=b.fos where a.fos is not null
	union
	select 'MTDC' as Name,d.fos,ifnull((e.Achieved/(d.Tar+e.Achieved)*100),0) as AchievedPerc,CASE when ifnull((e.Achieved/(d.Tar+e.Achieved)*100),0)>100 then 100 else ifnull((e.Achieved/(d.Tar+e.Achieved)*100),0) END as AchievedPercOrg from (select b.fos,ifnull((b.DueAmtOutb-c.FTDCAchieved),0) as Tar  from (SELECT round(sum(case when a.overdue>a.cp then a.pending_amount else 0 end)) as DueAmtOutb,a.fos from (SELECT shops.fos, shops.Name,outstandings_b.overdue,case when shops.credit_period=0 then 7 else shops.credit_period end as CP,outstandings_b.pending_amount  FROM `outstandings_b` left outer join shops on outstandings_b.party_name=shops.Name group by shops.Name)a group by a.fos) b left outer JOIN (SELECT invoice_payment.user_id as fos, ifnull(round(sum(amount)),0) as FTDCAchieved FROM `invoice_payment` WHERE ifnull(date(pymnt_date),date(created))=curdate() group by invoice_payment.user_id) c on b.fos=c.fos) d
	left OUTER join 
	(SELECT user_id as FOS,ifnull((sum(amount)),0) as Achieved FROM `invoice_payment` WHERE (ifnull(date(pymnt_date),date(created)) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) group by user_id)e on d.fos=e.fos)FOSPerc left outer join app_users on FOSPerc.FOS=app_users.id where app_users.Active=1 and app_users.rights=0 GROUP by FOSPerc.FOS
	)hc on fs.fos=hc.fos left outer join app_users on fs.fos=app_users.id  ) WHERE app_users.active=1 and app_users.rights=0 and fs.fos not in ('7','9')) d order by d.OverallScore desc";
				$db=mysqli_query($con,"SET @cnt=0");
				$db=mysqli_query($con,"SET SQL_BIG_SELECTS=1");
				
	
				$FOSRankexe = mysqli_query($con,$FOSRank_query) or die("Error: ".mysqli_error($con));
				
				
				while($RankNumber=mysqli_fetch_array($FOSRankexe))
				{
					
					$msg     .= '<tr style="text-align:right;"><td style="padding:10px;text-align:left">'. $RankNumber['Name'] .'</td><td style="padding:10px;">'. $RankNumber['SalesScore'] .'</td><td style="padding:10px;">'. $RankNumber['CollectionScore'] .'</td><td style="padding:10px;">'. $RankNumber['CounterStock'] .'</td><td style="padding:10px;">'. round($RankNumber['OverallScore']) .'</td><td style="padding:10px;">'. $RankNumber['row_number'] .'</td></tr>';
						
				}
                
			$msg	.= '</table>';
			
		}
		
		//$toEmails = array("thiru@vttech.in","venkat@vttech.in","srini@vttrading.in","sathiya@vttrading.in","zia@vttrading.in","arunit93@gmail.com");	
		//foreach($toEmails as $toMail)
		//{
			//$emailto     = "thiru@vttech.in, venkat@vttech.in, srini@vttrading.in, sathiya@vttrading.in, zia@vttrading.in, arunit93@gmail.com";
			/*$emailto     = "vtzss@vttrading.in";
            //$emailto     = 'arun@vttech.in';
			$toname      = 'VeeTee Trading';
			$emailfrom   = 'vt.sales@outlook.com';
			$fromname    = 'Web Admin';
			$subject     = 'Total Report';
			$messagebody = $msg;
			
			$headers = 
			'Return-Path: ' . $emailfrom . "\r\n" . 
			'From: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" . 
			'X-Priority: 3' . "\r\n" . 
			'X-Mailer: PHP ' . phpversion() .  "\r\n" . 
			'Reply-To: ' . $fromname . ' <' . $emailfrom . '>' . "\r\n" .
			'Cc: prasanth@vttech.in' . "\r\n".
			//'Bcc: arunit93@gmail.com' . "\r\n" .
			'MIME-Version: 1.0' . "\r\n" . 
			'Content-Transfer-Encoding: 8bit' . "\r\n" . 	
			'Content-Type: text/html; charset=UTF-8' . "\r\n";
			$params = '-f ' . $emailfrom;
			$test = mail($emailto, $subject, $messagebody, $headers, $params);// $test should be TRUE if the mail function is called correctly
		
			if($test)
			{
				echo('Total Report sent successfully.');
			}
			else
			{
				echo('Total Report sending error!.');
			}*/
			echo $msg;
		//}
	}

?>