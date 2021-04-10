<?php

	require_once('config.php');
	//require_once('config_s.php');
	//date_default_timezone_set("Asia/Kolkata");
	$todayDate = date("Y-m-d");
	if($con)
	{
		$query = "select distinct user_name as fos,fos_name,email,id from app_users where active='1' and rights='0'";
		$exe = mysqli_query($con,$query);
		if($exe)
		{
            $msg 	  = '<p style="color:green;text-align:center;width:60%"><strong>Individual Report</strong></p>';
			$cnt = mysqli_num_rows($exe);
			if($cnt>0)

			{
				$emailto="";
				$x=0;
				
				while($res = mysqli_fetch_array($exe))
				{
					
					$msg='';
					$email=$res['email'];
					$fos = $res['fos'];
					$fosname = $res['fos_name'];
					$id=$res['id'];
					//echo $id.'<br>';
				
			
				//echo $_SESSION[$id].'<br>';
				    ///////////////////////////////////////////////////
		
		$FOSRank_query = "select @cnt:=@cnt+1 AS row_number,FOS,FOSPercentage from (select FOS,round((sum(FOSPerc)/((SELECT count(DISTINCT model) FROM `modelwise_target`)+2+4))) as FOSPercentage from (
			(
				select d.FOS as FOS,ifnull(round(sum(d.AchievedPerc)),0) as FOSPerc from (select a.fos as FOS,a.Model as Model,ifnull(a.Target,0) as Target, ifnull(b.Achieved,0) as Achieved,ifnull(round(b.Achieved/a.Target*100),0) as AchievedPerc ,ifnull((c.TodaySales),0) as TodaySales from (select MT.model as Model,sum(MT.target) as Target,MT.fos from modelwise_target MT where (MT.created between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW() )  group by MT.fos,MT.model) a 
								left outer join 
								(SELECT sales.product_model as Model,COUNT(product_model) as Achieved,shops.fos as Fos FROM `sales` left outer join shops on sales.particulars=shops.Name where (sales_date between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW())  group by shops.fos,sales.product_model)b on a.model=b.model and a.fos=b.fos
								left outer join  
								(select sales.product_model as Model,count(product_model) as TodaySales,shops.fos as Fos  from sales left outer join shops on sales.particulars=shops.Name where date(sales_date)=(curdate() - INTERVAL 1 DAY)  group by shops.fos,sales.product_model)c on b.model=c.model and b.fos=c.fos)d group by d.fos
			)
			UNION
			(select c.FOS,ifnull(round(((c.FTD_AchievedPerc+d.MTD_AchievedPerc))),0) as FOSPerc from (select a.fos, a.fos_name as FOS_Name,a.Target as FTD_Target,ifnull(b.achieved,0) as FTD_Achieved, ifnull((a.Target-b.achieved),0) as FTD_Difference,ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as FTD_AchievedPerc  from (SELECT app_users.fos_name,ifnull(round(count(shops.id)/3),0) as Target,app_users.id as fos FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.active=1   and month(shops.created)=month(curdate())    group by app_users.user_name) a 
								LEFT outer JOIN 
								(select app_users.fos_name, ifnull(round(count(shop_id)),0) as Achieved,app_users.id as fos from attendance left outer join app_users on attendance.fos=app_users.user_name where date(attendance_date)=(curdate() - INTERVAL 1 DAY)  and app_users.active=1 group by fos) b
								on a.fos_name=b.fos_name and a.fos=b.fos) c
								LEFT OUTER JOIN
								(select a.fos,a.fos_name as FOS_Name,a.Target as MTD_Target,ifnull(b.achieved,0) as MTD_Achieved , ifnull((a.Target-b.achieved),0) as MTD_Difference,ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as MTD_AchievedPerc from (SELECT app_users.fos_name,ifnull(round((count(shops.id)/3)*(SELECT count(distinct date(attendance_date)) FROM `attendance` where (date(attendance_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() - INTERVAL 1 DAY)) and (DAYOFWEEK(date(attendance_date))) <> 1)),0) as Target,app_users.id as fos FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.active=1 and month(shops.created)=month(curdate())    group by app_users.user_name) a 
								LEFT outer JOIN 
								(select app_users.fos_name, ifnull(round(count(shop_id)),0) as Achieved,app_users.id as fos from attendance left outer join app_users on attendance.fos=app_users.user_name where (date(attendance_date) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.active=1  group by fos) b
								on a.fos_name=b.fos_name and a.fos=b.fos) d
			
								on c.FOS_Name=d.FOS_Name and c.fos=d.fos)
			UNION
			(select FOS,round((sum(FOSPerc))) as FOSPerc from (SELECT d.Name,d.fos as FOS,ifnull(round(c.Achieved/d.Target*100),0) as FOSPerc from (Select 'FTD Sales' as Name, ifnull(round(sum(b.TotalAmountperModel)/25),0) as Target, b.fos from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,round(((a.Target*pm.dp))) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target   group by fos,model) a left outer join product_master pm on a.model=pm.product_model group BY a.fos,a.model,a.Target) b group by b.fos) d
			left outer join 
			(SELECT SUM(sales.debit_amount) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where date(sales.sales_date)=(curdate() - INTERVAL 1 DAY)  group by shops.fos)c on d.fos=c.fos
			 union 
			select d.Name,d.fos as FOS,ifnull(round(c.Achieved/d.Target*100),0) as FOSPerc from (SELECT 'MTD Sales' as Name, ifnull(round((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() - INTERVAL 1 DAY)) and (DAYOFWEEK(date(sales_date))) <> 1)),0) as Target,b.fos from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,round(a.Target*pm.dp) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target   group by fos,model) a left outer join product_master pm on a.model=pm.product_model group BY a.fos, a.model,a.Target) b group by b.fos) d 
			LEFT OUTER JOIN
			(select SUM(sales.debit_amount) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where (sales_date between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) group by shops.fos) c on d.fos=c.fos
			union 
			select 'FTD Collection', a.fos as FOS,ifnull(round(b.Achieved/a.Target*100),0) as FOSPerc from (SELECT shops.fos as FOS,ifnull(round(sum(debit_amount)/14),0) as Target FROM sales left outer join shops on sales.particulars=shops.Name where date(sales_date) between (curdate() - INTERVAL 16 DAY) and (curdate() - INTERVAL 1 DAY) GROUP by shops.fos) a 
			left outer join 
			(SELECT user_id as FOS,ifnull(round(sum(amount)),0) as Achieved FROM `invoice_payment` WHERE date(created)=(curdate() - INTERVAL 1 DAY) group by user_id) b on a.fos=b.fos where a.fos is not null
			 union
			select d.Name,d.fos as FOS,ifnull(round(c.Achieved/d.Target*100),0) as FOSPerc  from (SELECT 'MTD Collection' as Name, ifnull(round((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() - INTERVAL 1 DAY)) and (DAYOFWEEK(date(sales_date))) <> 1)),0) as Target,b.fos from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,round(a.Target*pm.dp) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target   group by fos,model) a left outer join product_master pm on a.model=pm.product_model group BY a.fos, a.model,a.Target) b group by b.fos) d
			left OUTER join 
			(SELECT user_id as FOS,ifnull(round(sum(amount)),0) as Achieved FROM `invoice_payment` WHERE (created between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) group by user_id)c on d.fos=c.fos) fulltable group by FOS
			)) Rank group by FOS )FOSRank where FOS not in ('7','9')  ORDER by FOSPercentage DESC";

			
			$db=mysqli_query($con,"SET @cnt=0");
			$db=mysqli_query($con,"SET SQL_BIG_SELECTS=1");
			

			$FOSRankexe = mysqli_query($con,$FOSRank_query) or die("Error: ".mysqli_error($con));
			$FOSRankres = mysqli_fetch_array($FOSRankexe);
			
			//$FOSRank=$FOSRankres['row_number'];
			$RankArray='';
			
			$rankFinal = 1;
			//$RankNumber1=mysqli_fetch_array($FOSRankexe);
			//var_dump($FOSRankexe);
			while($RankNumber=mysqli_fetch_array($FOSRankexe))
			{
				$Rank=$RankNumber["row_number"];
				$FOS=$RankNumber["FOS"];
				//echo $FOS.'<br>';
				if($RankNumber["FOS"]==$id )
				{
					//echo $FOS.'=>'.$RankNumber["row_number"].'<br>';
					$rankFinal = $RankNumber["row_number"];
				}
			}
			
////////////////////////////////////////////////////

					$msg     .= '<p style="margin-left:30px;"><strong>Employee Mobile </strong> : '.$fosname.' ('.$fos.')';
						if($id!='7' && $id!='9')
						$msg     .= '<strong>Rank </strong> : '.$rankFinal;
					$msg     .= '</p>';



					///////////// Collection Progress////////////////////////////

			$msg 	 .= '<p style="margin-left:30px;"><strong>Collection Progress </strong> </p>';
			$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
            $msg 	 .= '<tr style="background:skyblue;"><th> Name </th><th> >60 Days </th><th> 30 - 60 Days </th><th> 20 - 29 Days </th><th>10 - 19 Days</th><th> 0 - 9 Days </th><th> Total </th>';
           	$sql = "select 'Outstanding' as Name,a as '>30 Days',b as '20 - 30 Days',c as '10 - 19 Days',d as '0 - 9 Days',ifnull((a+b+c+d),0) as Total 
			   from ((SELECT ifnull(round(sum(pending_amount)),0) as a FROM outstandings left outer join invoice_payment on outstandings.ref_no=invoice_payment.invoice_no where (outstandings.overdue>30) and invoice_payment.user_id=$id)a,
			   (SELECT ifnull(round(sum(pending_amount)),0) as b FROM outstandings left outer join invoice_payment on outstandings.ref_no=invoice_payment.invoice_no where (outstandings.overdue between '20' and '30') and invoice_payment.user_id=$id)b,
			   (SELECT ifnull(round(sum(pending_amount)),0) as c FROM outstandings left outer join invoice_payment on outstandings.ref_no=invoice_payment.invoice_no where (outstandings.overdue between '10' and '19') and invoice_payment.user_id=$id)c,
			   (SELECT ifnull(round(sum(pending_amount)),0) as d FROM outstandings left outer join invoice_payment on outstandings.ref_no=invoice_payment.invoice_no where (outstandings.overdue between '0' and '9') and invoice_payment.user_id=$id)d) 
			   union
			    select 'Today Collection' as Name,a,b,c,d,ifnull((a+b+c+d),0) from (
                (SELECT ifnull(round(sum(a.amount)),0) as a from(SELECT * from  invoice_payment where ifnull(date(pymnt_date),date(created))=(curdate() - INTERVAL 1 DAY) and invoice_payment.user_id=$id) a where (datediff((ifnull(date(a.pymnt_date),date(a.created))),a.sales_date) > 30)  ) a,
			   (SELECT ifnull(round(sum(b.amount)),0) as b from(SELECT * from  invoice_payment where ifnull(date(pymnt_date),date(created))=(curdate() - INTERVAL 1 DAY) and invoice_payment.user_id=$id) b where (datediff((ifnull(date(b.pymnt_date),date(b.created))),b.sales_date) BETWEEN 20 AND 30)) b,
			   (SELECT ifnull(round(sum(c.amount)),0) as c from(SELECT * from  invoice_payment where ifnull(date(pymnt_date),date(created))=(curdate() - INTERVAL 1 DAY) and invoice_payment.user_id=$id) c where (datediff((ifnull(date(c.pymnt_date),date(c.created))),c.sales_date) BETWEEN 10 AND 19)) c,
             (SELECT ifnull(round(sum(d.amount)),0) as d from(SELECT * from  invoice_payment where ifnull(date(pymnt_date),date(created))=(curdate() - INTERVAL 1 DAY) and invoice_payment.user_id=$id) d where (datediff((ifnull(date(d.pymnt_date),date(d.created))),d.sales_date) BETWEEN 0 AND 9)) d)";
            $ex = mysqli_query($con,$sql);
            if($ex)
            {
                
				while($rs = mysqli_fetch_array($ex))
                {
                    $msg     .= '<tr><td style="padding:10px;">'. $rs['Name'] .'</td><td style="padding:10px;">'. $rs['>30 Days'] .'</td><td style="padding:10px;">'. $rs['20 - 30 Days'] .'</td><td style="padding:10px;">'. $rs['10 - 19 Days'] .'</td><td style="padding:10px;">'. $rs['0 - 9 Days'] .'</td><td style="padding:10px;">'. $rs['Total'] .'</td></tr>';
                }
            }
			$msg	.= '</table>';

					/////////////////////////////////Sales Progress///////////////////////////
					$msg     .= '<p style="margin-left:30px;"><strong>Sales Progress</strong> </p>';
					$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
					$msg 	 .= '<tr style="background:skyblue;"><th> Model </th><th> Target </th><th> Achieved </th><th> Today Sales </th>';
					$sql = "select a.Model as Model,ifnull(a.Target,0) as Target, ifnull(b.Achieved,0) as Achieved,ifnull((c.TodaySales),0) as TodaySales from (select MT.model as Model,sum(MT.target) as Target,MT.fos from modelwise_target MT where (MT.created between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW() ) and MT.fos=$id group by MT.fos,MT.model) a 
                    left outer join 
                    (SELECT sales.product_model as Model,COUNT(product_model) as Achieved,shops.fos as Fos FROM `sales` left outer join shops on sales.particulars=shops.Name where (sales_date between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and shops.fos=$id group by shops.fos,sales.product_model)b on a.model=b.model
                    left outer join  
                    (select sales.product_model as Model,count(product_model) as TodaySales,shops.fos as Fos  from sales left outer join shops on sales.particulars=shops.Name where date(sales_date)=(curdate() - INTERVAL 1 DAY) and shops.fos=$id group by shops.fos,sales.product_model)c on b.model=c.model";
					$ex = mysqli_query($con,$sql);
					$cnt1 = mysqli_num_rows($ex); 
					if($ex)
					{
						if($cnt1>0)
						{
							while($rs = mysqli_fetch_array($ex))
							{
								$msg     .= '<tr><td style="padding:10px;">'. $rs['Model'] .'</td><td style="padding:10px;">'. $rs['Target'] .'</td><td style="padding:10px;">'. $rs['Achieved'] .'</td><td style="padding:10px;">'. $rs['TodaySales'] .'</td></tr>';
							}
						}
						else
							$msg     .= '<tr style="text-align:center;"><td colspan="5"><strong>No Records Found!</strong></td></tr>';					
					}
					
					$msg	.= '</table>';

					
					
					
					/////////////////////////////sales & collection/////////////////////////////
					$msg     .= '<p style="margin-left:30px;"><strong>Sales & Collections</strong> </p>';
					$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
					$msg 	 .= '<tr style="background:skyblue;"><th> Name </th><th> Target </th><th> Achieved </th><th> Difference </th>';
					$msg	 .= '<th>AchievedPerc</th></tr>';
					$sql = "Select 'FTD Sales' as Name, ifnull(round(sum(b.TotalAmountperModel)/25),0) as Target,ifnull(sum(c.Achieved),0) as Achieved,ifnull(round(((sum(b.TotalAmountperModel)/25)-sum(c.Achieved))),0) as Difference, ifnull(round((sum(c.Achieved)/(sum(b.TotalAmountperModel)/25)*100)),0) as AchievedPerc from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,round(((a.Target*pm.dp))) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target where fos=$id  group by model) a left outer join product_master pm on a.model=pm.product_model group BY a.model,a.Target) b 
					LEFT OUTER JOIN 
					(select sales.product_model,SUM(sales.debit_amount) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where date(sales.sales_date)=(curdate() - INTERVAL 1 DAY) and shops.fos=$id group by shops.fos,sales.product_model) c on b.model=c.product_model
					UNION
					SELECT 'MTD Sales' as Name, ifnull(round((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() - INTERVAL 1 DAY)) and (DAYOFWEEK(date(sales_date))) <> 1)),0) as Target,ifnull(sum(c.Achieved),0) as Achieved,ifnull(round((((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() - INTERVAL 1 DAY)) and (DAYOFWEEK(date(sales_date))) <> 1))-sum(c.Achieved))),0) as Difference, ifnull(round((sum(c.Achieved)/((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() - INTERVAL 1 DAY)) and (DAYOFWEEK(date(sales_date))) <> 1))*100)),0) as AchievedPerc from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,round(a.Target*pm.dp) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target where fos=$id  group by model) a left outer join product_master pm on a.model=pm.product_model group BY a.model,a.Target) b 
					LEFT OUTER JOIN 
					(select sales.product_model,SUM(sales.debit_amount) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where (sales_date between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and shops.fos=$id group by shops.fos,sales.product_model) c on b.model=c.product_model
					UNION
					select 'FTD Collection' as Name,Target,Achieved,(Target-Achieved) as Difference,ifnull(round((Achieved/Target*100)),0) as AchievedPerc from ((SELECT ifnull(round(sum(debit_amount)/14),0) as Target FROM sales left outer join shops on sales.particulars=shops.Name where date(sales_date) between (curdate() - INTERVAL 16 DAY) and (curdate() - INTERVAL 1 DAY) and shops.fos=$id)b,(SELECT ifnull(round(sum(amount)),0) as Achieved FROM `invoice_payment` WHERE date(created)=(curdate() - INTERVAL 1 DAY) and invoice_payment.user_id=$id)a)
					UNION
					select 'MTD Collection' as Name,Target,Achieved,round(Target-Achieved) as Difference, ifnull(round(Achieved/Target*100),0) as AchievedPerc from (SELECT( SELECT ifnull(round((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() - INTERVAL 1 DAY)) and (DAYOFWEEK(date(sales_date))) <> 1)),0) from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,round(a.Target*pm.dp) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target where fos=$id  group by model) a left outer join product_master pm on a.model=pm.product_model group BY a.model,a.Target) b 
					LEFT OUTER JOIN 
					(select sales.product_model,SUM(sales.debit_amount) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where (sales_date between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and shops.fos=$id group by shops.fos,sales.product_model) c on b.model=c.product_model) as Target,ifnull(round(sum(amount)),0) as Achieved FROM `invoice_payment` WHERE (created between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and invoice_payment.user_id=$id)d";
					$ex = mysqli_query($con,$sql);
					$cnt1 = mysqli_num_rows($ex); 
					if($ex)
					{
						if($cnt1>0)
						{
							while($rs = mysqli_fetch_array($ex))
							{
								$msg     .= '<tr><td style="padding:10px;">'. $rs['Name'] .'</td><td style="padding:10px;">'. $rs['Target'] .'</td><td style="padding:10px;">'. $rs['Achieved'] .'</td><td style="padding:10px;">'. $rs['Difference'] .'</td><td style="padding:10px;">'. $rs['AchievedPerc'] .'</td></tr>';
							}
						}
						else
							$msg     .= '<tr style="text-align:center;"><td colspan="5"><strong>No Records Found!</strong></td></tr>';					
					}
					
					$msg	.= '</table>';

					/////////////////////////////Beat Plan Adhereance/////////////////////////////

					$msg     .= '<p style="margin-left:30px;"><strong>Beat Plan Adhereance</strong> </p>';
					$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
					$msg 	 .= '<tr style="background:skyblue;"><th> Fos Name </th><th> FTD Target </th><th> FTD Achieved </th><th> FTD Difference </th><th> FTD AchievedPerc </th><th> MTD Target </th><th> MTD Achieved </th><th> MTD Difference </th><th> MTD AchievedPerc </th>';
					$sql = "select c.Fos_Name,c.FTD_Target,c.FTD_Achieved,c.FTD_Difference,c.FTD_AchievedPerc,d.MTD_Target,d.MTD_Achieved,d.MTD_Difference,d.MTD_AchievedPerc from (select a.fos_name as FOS_Name,a.Target as FTD_Target,ifnull(b.achieved,0) as FTD_Achieved, ifnull((a.Target-b.achieved),0) as FTD_Difference,ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as FTD_AchievedPerc  from (SELECT app_users.fos_name,ifnull(round(count(shops.id)/3),0) as Target FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.un_fos<>1 and app_users.id=$id  and month(shops.created)=month(curdate())    group by app_users.user_name) a 
					LEFT outer JOIN 
					(select app_users.fos_name, ifnull(round(count(shop_id)),0) as Achieved from attendance left outer join app_users on attendance.fos=app_users.user_name where date(attendance_date)=(curdate() - INTERVAL 1 DAY) and app_users.id=$id and app_users.un_fos<>1 group by fos) b
					on a.fos_name=b.fos_name) c
					LEFT OUTER JOIN
					(select a.fos_name as FOS_Name,a.Target as MTD_Target,ifnull(b.achieved,0) as MTD_Achieved , ifnull((a.Target-b.achieved),0) as MTD_Difference,ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as MTD_AchievedPerc from (SELECT app_users.fos_name,ifnull(round((count(shops.id)/3)*(SELECT count(distinct date(attendance_date)) FROM `attendance` where (date(attendance_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() - INTERVAL 1 DAY)) and (DAYOFWEEK(date(attendance_date))) <> 1)),0) as Target FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.id=$id and app_users.un_fos<>1 and month(shops.created)=month(curdate())    group by app_users.user_name) a 
					LEFT outer JOIN 
					(select app_users.fos_name, ifnull(round(count(shop_id)),0) as Achieved from attendance left outer join app_users on attendance.fos=app_users.user_name where (date(attendance_date) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.un_fos<>1 and app_users.id=$id group by fos) b
					on a.fos_name=b.fos_name) d

					on c.FOS_Name=d.FOS_Name ";
					$ex1 = mysqli_query($con,$sql);
					$cnt1 = mysqli_num_rows($ex1); 
					if($ex1)
					{
						if($cnt1>0)
						{
							while($rs = mysqli_fetch_array($ex1))
							{
								$msg     .= '<tr><td style="padding:10px;">'. $rs['Fos_Name'] .'</td><td style="padding:10px;">'. $rs['FTD_Target'] .'</td><td style="padding:10px;">'. $rs['FTD_Achieved'] .'</td><td style="padding:10px;">'. $rs['FTD_Difference'] .'</td><td style="padding:10px;">'. $rs['FTD_AchievedPerc'] .'</td><td style="padding:10px;">'. $rs['MTD_Target'] .'</td><td style="padding:10px;">'. $rs['MTD_Achieved'] .'</td><td style="padding:10px;">'. $rs['MTD_Difference'] .'</td><td style="padding:10px;">'. $rs['MTD_AchievedPerc'] .'</td></tr>';
							}
						}
						else
							$msg     .= '<tr style="text-align:center;"><td colspan="5"><strong>No Records Found!</strong></td></tr>';					
					}

					$msg	.= '</table>';



					$emailto     = $email.',prasanth@vttech.in,vtzss@vttrading.in';
					//$emailto     = 'prasanth@vttech.in';
					$toname      = 'VeeTee Trading';
					$emailfrom   = 'vt.sales@outlook.com';
					$fromname    = 'Web Admin';
					$subject     = 'Full Report '.$fosname;
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
					'Content-Transfer-Encoding: 8bit' . "\r\n" . 	
					'Content-Type: text/html; charset=UTF-8' . "\r\n";
					$params = '-f ' . $emailfrom;
					$test = mail($emailto, $subject, $messagebody, $headers, $params);// $test should be TRUE if the mail function is called correctly
				
					
				}
					if($test)
					{
						echo('Individual Report sent successfully.');
					}
					else
					{
						echo('Error for Individual Report.');
					}
			}
			else
			{
				$msg     .= '<p><strong>No Records Found!</strong></p>';
			}
			
		}
		
		//$toEmails = array("thiru@vttech.in","venkat@vttech.in","srini@vttrading.in","sathiya@vttrading.in","zia@vttrading.in","arunit93@gmail.com");	
		//foreach($toEmails as $toMail)
		//{
			//$emailto     = "vtzss@vttrading.in,thiru@vttech.in, venkat@vttech.in, srini@vttrading.in, sathiya@vttrading.in, zia@vttrading.in, arunit93@gmail.com";
			
		}
	

?>