<?php

	require_once('config.php');
	//require_once('config_s.php');
	//date_default_timezone_set("Asia/Kolkata");
	$todayDate = date("Y-m-d");
	if($con)
	{
		$query = "SELECT distinct user_name as fos,fos_name,email,id from app_users where active='1' and rights='0' and id <> '15'";
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
				
			/*
				$FOSRank_query = "SELECT FOS,FOSPercentage, @cnt:=@cnt+1 AS row_number from (select app_users.id as FOS,(fs.SFOSPerc+gb.BFOSperc+hc.FTDSFOSPerc+hc.MTDSFOSPerc+hc.FTDCFOSPerc+hc.MTDCFOSPerc) as FOSPercentage
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
												)hc on fs.fos=hc.fos left outer join app_users on fs.fos=app_users.id  ) WHERE fs.fos not in ('7','9','15')) d order by d.FOSPercentage desc"
			*/
			
				//echo $_SESSION[$id].'<br>';
				    /////////////////////// Rank ////////////////////////////
		
		$FOSRank_query = "SELECT FOS,FOSPercentage, @cnt:=@cnt+1 AS row_number from (select app_users.id as FOS,(fs.SFOSPerc+gb.BFOSperc+hc.FTDSFOSPerc+hc.MTDSFOSPerc+hc.FTDCFOSPerc+hc.MTDCFOSPerc) as FOSPercentage
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
												FROM stocks_c 
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
												)hc on fs.fos=hc.fos left outer join app_users on fs.fos=app_users.id  ) WHERE fs.fos not in ('7','9','15')) d order by d.FOSPercentage desc";

			
			$db=mysqli_query($con,"SET @cnt=0");
			$db=mysqli_query($con,"SET SQL_BIG_SELECTS=1");
			

			$FOSRankexe = mysqli_query($con,$FOSRank_query) or die("Error: ".mysqli_error($con));
			
			$RankArray='';
			
			$rankFinal = 1;
			
			while($RankNumber=mysqli_fetch_array($FOSRankexe))
			{
				$Rank=$RankNumber["row_number"];
				$FOS=$RankNumber["FOS"];
				
				if($RankNumber["FOS"]==$id)
				{
					
					$rankFinal = $RankNumber["row_number"];
				}
			}
			


					$msg     .= '<p style="margin-left:30px;"><strong>c </strong> : '.$fosname.' ('.$fos.')     ';
						if($id!='7' && $id!='9' && $id!='15')
					$msg     .= '<strong>Rank </strong> : '.$rankFinal;
					$msg     .= '</p>';



					///////////// Collection Progress////////////////////////////

			$msg 	 .= '<p style="margin-left:30px;"><strong>Collection Progress </strong> </p>';
			$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
            $msg 	 .= '<tr style="background:skyblue;"><th> Name </th><th> >45 Days </th><th> 30 - 45 Days </th><th> 20 - 29 Days </th><th>10 - 19 Days</th><th> 0 - 9 Days </th><th> Total </th>';
           	$sql = "SELECT 'Outstanding' as Name,a as '>45 Days',b as '30 - 45 Days',c as '20 - 29 Days',d as '10 - 19 Days',e as '0 - 9 Days',ifnull((a+b+c+d+e),0) as Total 
			   from ((SELECT ifnull(round(sum(pending_amount)),0) as a FROM outstandings left outer join shops on outstandings.party_name=shops.Name where (outstandings.overdue>45) and shops.deleted='0' and shops.fos=$id)a,
			   (SELECT ifnull(round(sum(pending_amount)),0) as b FROM outstandings left outer join shops on outstandings.party_name=shops.Name where (outstandings.overdue between '30' and '45') and shops.deleted='0' and shops.fos=$id)b,
			   (SELECT ifnull(round(sum(pending_amount)),0) as c FROM outstandings left outer join shops on outstandings.party_name=shops.Name where (outstandings.overdue between '20' and '29') and shops.deleted='0' and shops.fos=$id)c,
			   (SELECT ifnull(round(sum(pending_amount)),0) as d FROM outstandings left outer join shops on outstandings.party_name=shops.Name where (outstandings.overdue between '10' and '19') and shops.deleted='0' and shops.fos=$id)d,
			   (SELECT ifnull(round(sum(pending_amount)),0) as e FROM outstandings left outer join shops on outstandings.party_name=shops.Name where (outstandings.overdue between '0' and '9') and shops.deleted='0' and shops.fos=$id)e) 
			   union
			   select 'Today Collection' as Name,a,b,c,d,e,ifnull((a+b+c+d+e),0) from (
                (SELECT ifnull(round(sum(a.amount)),0) as a from(SELECT * from  invoice_payment where ifnull(date(pymnt_date),date(created))=curdate() and invoice_payment.user_id=$id) a where (datediff((ifnull(date(a.pymnt_date),date(a.created))),a.sales_date) > 45)  ) a,
			   (SELECT ifnull(round(sum(b.amount)),0) as b from(SELECT * from  invoice_payment where ifnull(date(pymnt_date),date(created))=curdate() and invoice_payment.user_id=$id) b where (datediff((ifnull(date(b.pymnt_date),date(b.created))),b.sales_date) BETWEEN 30 AND 45)) b,
			   (SELECT ifnull(round(sum(c.amount)),0) as c from(SELECT * from  invoice_payment where ifnull(date(pymnt_date),date(created))=curdate()and invoice_payment.user_id=$id) c where (datediff((ifnull(date(c.pymnt_date),date(c.created))),c.sales_date) BETWEEN 20 AND 29)) c,
             (SELECT ifnull(round(sum(d.amount)),0) as d from(SELECT * from  invoice_payment where ifnull(date(pymnt_date),date(created))=curdate() and invoice_payment.user_id=$id) d where (datediff((ifnull(date(d.pymnt_date),date(d.created))),d.sales_date) BETWEEN 10 AND 19)) d,
			 (SELECT ifnull(round(sum(e.amount)),0) as e from(SELECT * from  invoice_payment where ifnull(date(pymnt_date),date(created))=curdate() and invoice_payment.user_id=$id) e where (datediff((ifnull(date(e.pymnt_date),date(e.created))),e.sales_date) BETWEEN 0 AND 10)) e)";
            $ex = mysqli_query($con,$sql);
            if($ex)
            {
                
				while($rs = mysqli_fetch_array($ex))
                {
                    $msg     .= '<tr style="text-align:right;"><td style="padding:10px;text-align:left;">'. $rs['Name'] .'</td><td style="padding:10px;">'. $rs['>45 Days'] .'</td><td style="padding:10px;">'. $rs['30 - 45 Days'] .'</td><td style="padding:10px;">'. $rs['20 - 29 Days'] .'</td><td style="padding:10px;">'. $rs['10 - 19 Days'] .'</td><td style="padding:10px;">'. $rs['0 - 9 Days'] .'</td><td style="padding:10px;">'. $rs['Total'] .'</td></tr>';
                }
            }
			$msg	.= '</table>';

					/////////////////////////////////Sales Progress///////////////////////////
					$msg     .= '<p style="margin-left:30px;"><strong>Sales Progress</strong> </p>';
					$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
					$msg 	 .= '<tr style="background:skyblue;"><th> Model </th><th> Target </th><th> TargetValue </th><th> Achieved 
                                                      </th><th> AchievedValue </th><th> Today Sales </th><th> AchievedPerc </th>';					

					$sql = "SELECT d.product_category,a.Model as Model,ifnull(a.Target,0) as Target, ifnull(round(a.target*d.dp),0) as TargetValue,ifnull(b.Achieved,0) as Achieved,ifnull(round(b.Achieved*d.dp),0) as AchievedValue ,ifnull((c.TodaySales),0) as TodaySales,ifnull(round(b.Achieved/a.Target*100),0) as AchievedPerc  from (select MT.model as Model,sum(MT.target) as Target,MT.fos from modelwise_target MT where (MT.created between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW() ) and MT.fos=$id group by MT.fos,MT.model) a 
							left outer join 
							(SELECT sales.product_model as Model,COUNT(product_model) as Achieved,shops.fos as Fos FROM `sales` left outer join shops on sales.particulars=shops.Name where (sales_date between DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and shops.deleted='0' and shops.fos=$id group by shops.fos,sales.product_model)b on a.model=b.model
							left outer join  
							(select sales.product_model as Model,count(product_model) as TodaySales,shops.fos as Fos  from sales left outer join shops on sales.particulars=shops.Name where date(sales_date)=curdate()  and shops.deleted='0' and shops.fos=$id group by shops.fos,sales.product_model)c on a.model=c.model
							left OUTER JOIN
							(SELECT modelwise_target.fos,modelwise_target.model,product_master.dp,product_master.product_category  FROM `product_master` LEFT OUTER JOIN modelwise_target on product_master.product_model=modelwise_target.model where modelwise_target.fos=$id  group by modelwise_target.fos,modelwise_target.model)d on d.model=a.model and d.fos=a.fos order by d.product_category,d.dp";
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

					
					
					
					/////////////////////////////sales & collection/////////////////////////////
					$msg     .= '<p style="margin-left:30px;"><strong>Sales & Collections</strong> </p>';
					$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
					$msg 	 .= '<tr style="background:skyblue;"><th> Name </th><th> Target </th><th> Achieved </th><th> Difference </th>';
					$msg	 .= '<th>AchievedPerc</th></tr>';
					$sql = "SELECT 'FTD Sales' as Name, ifnull(round(sum(b.TotalAmountperModel)/25),0) as Target,ifnull(sum(c.Achieved),0) as Achieved,ifnull(round(((sum(b.TotalAmountperModel)/25)-ifnull(sum(c.Achieved),0))),0) as Difference, ifnull(round((sum(c.Achieved)/(sum(b.TotalAmountperModel)/25)*100)),0) as AchievedPerc from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,round(((a.Target*pm.dp))) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target where fos=$id  group by model) a left outer join product_master pm on a.model=pm.product_model group BY a.model,a.Target) b 
					LEFT OUTER JOIN 
					(select sales.product_model,ifnull(SUM(sales.debit_amount),0) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where date(sales.sales_date)=(curdate() ) and shops.deleted='0' and shops.fos=$id group by shops.fos,sales.product_model) c on b.model=c.product_model
					UNION
					SELECT 'MTD Sales' as Name, ifnull(round((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() )) and (DAYOFWEEK(date(sales_date))) <> 1)),0) as Target,ifnull(sum(c.Achieved),0) as Achieved,ifnull(round((((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() )) and (DAYOFWEEK(date(sales_date))) <> 1))-sum(c.Achieved))),0) as Difference, ifnull(round((sum(c.Achieved)/((sum(b.TotalAmountperModel)/25)*(SELECT count(distinct date(sales_date)) FROM `sales` where (date(sales_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() )) and (DAYOFWEEK(date(sales_date))) <> 1))*100)),0) as AchievedPerc from (select a.model,a.Target,max(ifnull(pm.dp,0)) as AmountperModel,round(a.Target*pm.dp) as TotalAmountperModel,a.fos from (select model,sum(target) as Target,fos from modelwise_target where fos=$id  group by model) a left outer join product_master pm on a.model=pm.product_model group BY a.model,a.Target) b 
					LEFT OUTER JOIN 
					(select sales.product_model,ifnull(SUM(sales.debit_amount),0) as Achieved,shops.fos from sales left outer join shops on sales.particulars=shops.Name where (sales_date between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and shops.deleted='0' and shops.fos=$id group by shops.fos,sales.product_model) c on b.model=c.product_model
					UNION
					select 'FTD Collection', ifnull(round(DueAmtOutb+b.Achieved),0) as Target,b.Achieved,ifnull(round(DueAmtOutb+b.Achieved-b.Achieved),0) as Difference, ifnull(round(b.Achieved/(DueAmtOutb+b.Achieved)*100),0) as AchievedPerc from (select a.fos as FOS,ifnull(round(sum(case when a.overdue>a.cp then a.pending_amount else 0 end)),0) as DueAmtOutb from (SELECT shops.fos,shops.Name,outstandings_b.overdue,case when shops.credit_period=0 then 7 else shops.credit_period end as CP,outstandings_b.pending_amount  FROM outstandings_b left outer join shops on outstandings_b.party_name=shops.Name group by shops.Name)a group by a.fos) a 
					left outer join 
					(SELECT user_id as FOS,ifnull((sum(amount)),0) as Achieved FROM `invoice_payment` WHERE ifnull(date(pymnt_date),date(created))=curdate()  group by user_id) b on a.fos=b.fos where a.fos=$id
					UNION
					select 'MTD Collection' as Name,ifnull(round(d.Tar+e.Achieved),0) as Target,ifnull(round(e.Achieved),0) as Achieved, ifnull(round(d.Tar+e.Achieved-e.Achieved),0) as Difference,ifnull(round(e.Achieved/(d.Tar+e.Achieved)*100),0) as AchievedPerc from (select b.fos,ifnull((b.DueAmtOutb-c.FTDCAchieved),0) as Tar  from (SELECT ifnull(round(sum(case when a.overdue>a.cp then a.pending_amount else 0 end)),0) as DueAmtOutb,a.fos from (SELECT shops.fos, shops.Name,outstandings_b.overdue,case when shops.credit_period=0 then 7 else shops.credit_period end as CP,outstandings_b.pending_amount  FROM `outstandings_b` left outer join shops on outstandings_b.party_name=shops.Name group by shops.Name)a group by a.fos) b left outer JOIN (SELECT invoice_payment.user_id as fos, ifnull(round(sum(amount)),0) as FTDCAchieved FROM `invoice_payment` WHERE ifnull(date(pymnt_date),date(created))=curdate() group by invoice_payment.user_id) c on b.fos=c.fos) d
					left OUTER join 
					(SELECT user_id as FOS,ifnull((sum(amount)),0) as Achieved FROM `invoice_payment` WHERE (ifnull(date(pymnt_date),date(created)) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) group by user_id)e on d.fos=e.fos where d.fos=$id";
					$ex = mysqli_query($con,$sql);
					$cnt1 = mysqli_num_rows($ex); 
					if($ex)
					{
						if($cnt1>0)
						{
							while($rs = mysqli_fetch_array($ex))
							{
								$msg     .= '<tr style="text-align:right;"><td style="padding:10px;text-align:left;">'. $rs['Name'] .'</td><td style="padding:10px;">'. $rs['Target'] .'</td><td style="padding:10px;">'. $rs['Achieved'] .'</td><td style="padding:10px;">'. $rs['Difference'] .'</td><td style="padding:10px;">'. $rs['AchievedPerc'] .'</td></tr>';
							}
						}
						else
							$msg     .= '<tr style="text-align:center;"><td colspan="5"><strong>No Records Found!</strong></td></tr>';					
					}
					
					$msg	.= '</table>';

					/////////////////////////////Beat Plan Adhereance/////////////////////////////

					$msg     .= '<p style="margin-left:30px;"><strong> Counter Stock Taken </strong> </p>';
					$msg     .= '<table border="1" width="60%" style="border-collapse: collapse;margin-left:30px;">'; 
					$msg 	 .= '<tr style="background:skyblue;"><th> Name </th><th> Target </th><th> Achieved </th><th> Difference </th><th> AchievedPerc </th><th> Attendance </th></tr>';
					// $sql = "select 'FTD' as Name,a.Target as Target,ifnull(b.achieved,0) as Achieved, ifnull((a.Target-b.achieved),0) as Difference,ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as AchievedPerc  from (SELECT app_users.fos_name,ifnull(round(count(shops.id)/3),0) as Target  FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.id=$id and app_users.Active=1 and app_users.rights=0 and shops.deleted='0'  group by app_users.user_name) a 
					// 		LEFT outer JOIN 
					// 		(select app_users.fos_name, ifnull(round(count(shop_id)),0) as Achieved  from attendance left outer join app_users on attendance.fos=app_users.user_name where date(attendance_date)=(curdate() - INTERVAL 1 DAY) and app_users.id=$id and app_users.Active=1 and app_users.rights=0 group by fos) b
					// 		on a.fos_name=b.fos_name
					// 		union
					// 		(select 'MTD',a.Target, ifnull(b.achieved,0), ifnull((a.Target-b.achieved),0),ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0)  from (SELECT app_users.fos_name,ifnull(round((count(shops.id)/3)*(SELECT count(distinct date(attendance_date)) FROM `attendance` where (date(attendance_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND (curdate() - INTERVAL 1 DAY)) and (DAYOFWEEK(date(attendance_date))) <> 1)),0) as Target  FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.Active=1 and app_users.rights=0 and app_users.id=$id and shops.deleted='0'    group by app_users.user_name) a 
					// 		LEFT outer JOIN 
					// 		(select app_users.fos_name, ifnull(round(count(shop_id)),0) as Achieved from attendance left outer join app_users on attendance.fos=app_users.user_name where (date(attendance_date) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.id=$id and app_users.Active=1 and app_users.rights=0  group by fos) b
					// 		on a.fos_name=b.fos_name)";
					// $sql = "select 'FTD' as Name,a.Target as Target,ifnull(b.achieved,0) as Achieved, ifnull((a.Target-b.achieved),0) as Difference,ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as AchievedPerc  from (SELECT app_users.fos_name, '20' as Target  FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.id=$id and app_users.Active=1 and app_users.rights=0 and shops.deleted='0'  group by app_users.user_name) a 
					// LEFT outer JOIN 
					// (select app_users.fos_name, ifnull(round(count(shop_id)),0) as Achieved  from attendance left outer join app_users on attendance.fos=app_users.user_name where date(attendance_date)=curdate() and app_users.id=$id and app_users.Active=1 and app_users.rights=0 group by fos) b
					// on a.fos_name=b.fos_name
					// union
					// (select 'MTD',a.Target, ifnull(b.achieved,0), ifnull((a.Target-b.achieved),0),ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0)  from (SELECT app_users.fos_name,ifnull((20*(SELECT count(distinct date(attendance_date)) FROM `attendance` where (date(attendance_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate()) and (DAYOFWEEK(date(attendance_date))) <> 1)),0) as Target  FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.Active=1 and app_users.rights=0 and app_users.id=$id and shops.deleted='0'    group by app_users.user_name) a 
					// LEFT outer JOIN 
					// (select app_users.fos_name, ifnull(round(count(shop_id)),0) as Achieved from attendance left outer join app_users on attendance.fos=app_users.user_name where (date(attendance_date) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.id=$id and app_users.Active=1 and app_users.rights=0  group by fos) b
					// on a.fos_name=b.fos_name)";
					
					
					$sql="SELECT 'FTD' as Name,a.Target as Target,ifnull(b.achieved,0) as Achieved, ifnull((a.Target-b.achieved),0) as Difference,ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0) as AchievedPerc,ifnull(c.attendanceCount,0) as attendanceCount   from (SELECT app_users.fos_name, '15' as Target  FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.id=$id and app_users.Active=1 and app_users.rights=0 and shops.deleted='0'  group by app_users.user_name) a 
	LEFT outer JOIN 
	(select app_users.fos_name, ifnull(round(count(shopid)),0) as Achieved  from stocks_c left outer join app_users on stocks_c.userid=app_users.id where date(datetime)=curdate() and app_users.id=$id and app_users.Active=1 and app_users.rights=0 group by userid) b
	on a.fos_name=b.fos_name
	LEFT OUTER JOIN
	(SELECT app_users.fos_name,count(attendance.shop_id) as attendanceCount
	from app_users RIGHT outer join attendance on app_users.user_name=attendance.fos
	where date(attendance.attendance_date)=curdate() and app_users.id=$id group by attendance.fos) c
	on a.fos_name=c.fos_name
	
	union
	
	(select 'MTD',a.Target, ifnull(b.achieved,0), ifnull((a.Target-b.achieved),0),ifnull(round(((ifnull(b.achieved,0))/(a.Target)*100)),0),ifnull(c.attendanceCount,0)  from (SELECT app_users.fos_name,ifnull((15*(SELECT count(distinct date(datetime)) FROM `stocks_c` where (date(datetime) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate()) and (DAYOFWEEK(date(datetime))) <> 1)),0) as Target  FROM app_users LEFT OUTER JOIN shops on app_users.id=shops.fos where app_users.Active=1 and app_users.rights=0 and app_users.id=$id and shops.deleted='0' group by app_users.user_name) a 
	LEFT outer JOIN 
	(select app_users.fos_name, ifnull(round(count(shopid)),0) as Achieved from stocks_c left outer join app_users on stocks_c.userid=app_users.id where (date(datetime) between  DATE_FORMAT(NOW() ,'%Y-%m-01') AND NOW()) and app_users.id=$id and app_users.Active=1 and app_users.rights=0  group by userid) b
	on a.fos_name=b.fos_name
	LEFT OUTER JOIN
	(SELECT app_users.fos_name,count(attendance.shop_id) as attendanceCount
	from app_users RIGHT outer join attendance on app_users.user_name=attendance.fos
	where (date(attendance.attendance_date) BETWEEN DATE_FORMAT(curdate() ,'%Y-%m-01') AND curdate()) and app_users.id=$id group by attendance.fos) c
	on a.fos_name=c.fos_name)";
					
					
					$ex1 = mysqli_query($con,$sql);
					$cnt1 = mysqli_num_rows($ex1); 
					if($ex1)
					{
						if($cnt1>0)
						{
							while($rs = mysqli_fetch_array($ex1))
							{
								$msg     .= '<tr style="text-align:right;"><td style="padding:10px;text-align:left;">'. $rs['Name'] .'</td><td style="padding:10px;">'. $rs['Target'] .'</td><td style="padding:10px;">'. $rs['Achieved'] .'</td><td style="padding:10px;">'. $rs['Difference'] .'</td><td style="padding:10px;">'. $rs['AchievedPerc'] .'</td><td style="padding:10px;">'. $rs['attendanceCount'] .'</td></tr>';
							}
						}
						else
							$msg     .= '<tr style="text-align:center;"><td colspan="5"><strong>No Records Found!</strong></td></tr>';					
					}

					$msg	.= '</table>';


                    //$emailto     = 'arun@vttech.in';
					$emailto     = $email.',vtzss@vttrading.in';
					$toname      = 'VeeTee Trading';
					$emailfrom   = 'vt.sales@outlook.com';
					$fromname    = 'Web Admin';
					$subject     = 'Individual FOS Performance Report - '.$fosname;
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
			//$emailto     = "thiru@vttech.in, venkat@vttech.in, srini@vttrading.in, sathiya@vttrading.in, zia@vttrading.in, arunit93@gmail.com";
			
		}
	

?>