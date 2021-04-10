<?php 
	//require_once('config_s.php');
	require_once('config.php');
	
	$app_userId = $_GET['app_userId'];
	if(isset($_GET['particulars']))
	{
		$particulars = $_GET['particulars'];
		$days = $_GET['days'];
		if (strpos($particulars, '!!') !== false)
			$particulars = str_replace("!!","&",$particulars);
		if (strpos($particulars, '@@') !== false)
			$particulars = str_replace("@@","#",$particulars);
		$particulars = mysqli_real_escape_string($con,$particulars);
		$mnthN = $_GET['mnthN']; 
	}
	if(isset($_GET['srchShpsName']))
	{
		$srchShpsName = $_GET['srchShpsName'];
		if (strpos($srchShpsName, '!!') !== false)
			$srchShpsName = str_replace("!!","&",$srchShpsName);
		if (strpos($srchShpsName, '@@') !== false)
			$srchShpsName = str_replace("@@","#",$srchShpsName);
		if(isset($_GET['days']))
			$days = $_GET['days'];
		else
			$days = 0;
		$mnthM = $_GET['mnthM']; 
	}
	if(isset($_GET['daysWise']))
		$daysWise = $_GET['daysWise'];
	if(isset($_GET['shpName']))
	{
		$shpName = $_GET['shpName'];
		if (strpos($shpName, '!!') !== false)
			$shpName = str_replace("!!","&",$shpName);
		if (strpos($shpName, '@@') !== false)
			$shpName = str_replace("@@","#",$shpName);
		$shpName = mysqli_real_escape_string($con,$shpName);
		$daysWise_val = $_GET['daysWise_val'];
	}
	if(isset($_GET['mnthVal']))
	{
		$shpNameSalesTxt = $_GET['shpNameSalesTxt'];
		$mnthVal = $_GET['mnthVal'];
		if (strpos($shpNameSalesTxt, '!!') !== false)
			$shpNameSalesTxt = str_replace("!!","&",$particulars);
		if (strpos($shpNameSalesTxt, '@@') !== false)
			$shpNameSalesTxt = str_replace("@@","#",$particulars);
		$shpNameSalesTxt = mysqli_real_escape_string($con,$shpNameSalesTxt);
	}
	$currDate = date("Y-m-d");
	if($con)
	{
		if(isset($_GET['particulars']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if(date("m")<$mnthN)
				$yr = date("Y",strtotime("-1 year"));
			else
				$yr = 'Year(curdate())';

			if($mnthN=='empty' || $mnthN=='null')
			{
				if($days==0 || $days=='0')
				{
					if($rights['rights']=='2')
						$sql = "select distinct(s.vch_no),s.sales_date,sum(s.debit_amount) as debit_amount,s.vch_type from sales s,shops h where s.particulars='$particulars' and s.particulars=h.Name and h.Deleted='0' and month(s.sales_date)=month(curdate()) and year(s.sales_date)=$yr group by s.vch_no";
					else
						$sql = "select distinct(s.vch_no),s.sales_date,sum(s.debit_amount) as debit_amount,s.vch_type from sales s,shops h where s.particulars='$particulars' and s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and month(s.sales_date)=month(curdate()) and year(s.sales_date)=$yr group by s.vch_no";
				}
				else
				{
					if($rights['rights']=='2')
						$sql = "select distinct(s.vch_no),s.sales_date,sum(s.debit_amount) as debit_amount,s.vch_type from sales s,shops h where s.particulars='$particulars' and s.particulars=h.Name and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$days." DAY) and '$currDate' group by s.vch_no";
					else
						$sql = "select distinct(s.vch_no),s.sales_date,sum(s.debit_amount) as debit_amount,s.vch_type from sales s,shops h where s.particulars='$particulars' and s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$days." DAY) and '$currDate' group by s.vch_no";
				}
			}
			else
			{
				if($days==0 || $days=='0')
				{
					if($rights['rights']=='2')
						$sql = "select distinct(s.vch_no),s.sales_date,sum(s.debit_amount) as debit_amount,s.vch_type from sales s,shops h where s.particulars='$particulars' and s.particulars=h.Name and h.Deleted='0' and Month(s.sales_date)='$mnthN' and Year(s.sales_date)=$yr group by s.vch_no";
					else
						$sql = "select distinct(s.vch_no),s.sales_date,sum(s.debit_amount) as debit_amount,s.vch_type from sales s,shops h where s.particulars='$particulars' and s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and Month(s.sales_date)='$mnthN' and Year(s.sales_date)=$yr group by s.vch_no";
				}
				else
				{
					if($rights['rights']=='2')
						$sql = "select distinct(s.vch_no),s.sales_date,sum(s.debit_amount) as debit_amount,s.vch_type from sales s,shops h where s.particulars='$particulars' and s.particulars=h.Name and Year(s.sales_date)=$yr and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$days." DAY) and '$currDate' group by s.vch_no";
					else
						$sql = "select distinct(s.vch_no),s.sales_date,sum(s.debit_amount) as debit_amount,s.vch_type from sales s,shops h where s.particulars='$particulars' and s.particulars=h.Name and h.fos='$app_userId' and Year(s.sales_date)=$yr and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$days." DAY) and '$currDate' group by s.vch_no";
				}
			}
		}
		else if(isset($_GET['srchShpsName']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if(date("m")<$mnthM)
				$yr = date("Y",strtotime("-1 year"));
			else
				$yr = 'Year(curdate())';

			if($mnthM=='empty' || $mnthM=='null')
			{
				if($days==0 || $days=='0')
				{
					if($rights['rights']=='2')
						$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars='$srchShpsName' and s.particulars=h.Name and h.Deleted='0' and month(s.sales_date)=month(curdate()) and year(s.sales_date)=$yr group by s.particulars";
					else
						$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars='$srchShpsName' and s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and month(s.sales_date)=month(curdate()) and year(s.sales_date)=$yr group by s.particulars";
				}
				else
				{
					if($rights['rights']=='2')
						$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars='$srchShpsName' and s.particulars=h.Name and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$days." DAY) and '$currDate' group by s.particulars";
					else
						$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars='$srchShpsName' and s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$days." DAY) and '$currDate' group by s.particulars";
				}
			}
			else
			{
				if($days==0 || $days=='0')
				{
					if($rights['rights']=='2')
						$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars='$srchShpsName' and s.particulars=h.Name and h.Deleted='0' and Month(s.sales_date)='$mnthM' and Year(s.sales_date)=$yr group by s.particulars";
					else
						$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars='$srchShpsName' and s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and Month(s.sales_date)='$mnthM' and Year(s.sales_date)=$yr group by s.particulars";
				}
				else
				{
					if($rights['rights']=='2')
						$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars='$srchShpsName' and s.particulars=h.Name and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$days." DAY) and Month(s.sales_date)='$mnthM' and Year(s.sales_date)=$yr and '$currDate' group by s.particulars";
					else
						$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars='$srchShpsName' and s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$days." DAY) and Month(s.sales_date)='$mnthM' and Year(s.sales_date)=$yr and '$currDate' group by s.particulars";
				}
			}
		}
		else if(isset($_GET['daysWise']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$sql = "select distinct s.particulars,s.id,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars=h.Name and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$daysWise." DAY) and '$currDate' group by s.particulars";
			else
				$sql = "select distinct s.particulars,s.id,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$daysWise." DAY) and '$currDate' group by s.particulars";
		}
		else if(isset($_GET['shpName']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars='$shpName' and s.particulars=h.Name and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$daysWise_val." DAY) and '$currDate' group by s.particulars";
			else
				$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount,s.sales_date from sales s,shops h where s.particulars='$shpName' and s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and s.sales_date between DATE_SUB('$currDate',INTERVAL ".$daysWise_val." DAY) and '$currDate' group by s.particulars";
		}
		else if(isset($_GET['mnthVal']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if(date("m")<$mnthVal)
				$yr = date("Y",strtotime("-1 year"));
			else
				$yr = 'Year(curdate())';

			if($shpNameSalesTxt=='empty')
			{
				if($rights['rights']=='2')
					$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount from sales s,shops h where s.particulars=h.Name and h.Deleted='0'  and Month(s.sales_date)='$mnthVal' and Year(s.sales_date)=$yr group by s.particulars";
				else
					$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount from sales s,shops h where s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and Month(s.sales_date)='$mnthVal' and Year(s.sales_date)=$yr group by s.particulars";
			}
			else
			{
				if($rights['rights']=='2')
					$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount from sales s,shops h where s.particulars=h.Name and h.Deleted='0' and Month(s.sales_date)='$mnthVal' and Year(s.sales_date)=$yr and s.particulars='$shpNameSalesTxt' group by s.particulars";
				else
					$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount from sales s,shops h where s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and Month(s.sales_date)='$mnthVal' and Year(s.sales_date)=$yr and s.particulars='$shpNameSalesTxt' group by s.particulars";
			}
		}
		else
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount from sales s,shops h where s.particulars=h.Name and h.Deleted='0' and month(s.sales_date)=month(curdate()) and year(s.sales_date)=year(curdate()) group by s.particulars";
			else
				$sql = "select distinct s.particulars,count(*),sum(s.debit_amount) as debit_amount from sales s,shops h where s.particulars=h.Name and h.fos='$app_userId' and h.Deleted='0' and month(s.sales_date)=month(curdate()) and year(s.sales_date)=year(curdate()) group by s.particulars";
				
		}
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		$salesArr = array();
		if($cnt>0)
		{
			while($rs = mysqli_fetch_array($ex))
			{
				if(isset($_GET['particulars']))
				{
					$x = $rs['sales_date'];
					$salesDate = DateTime::createFromFormat('Y-m-d', $x)->format('d-m-Y');
					array_push($salesArr,array('Status'=>'success','sales_date'=>$salesDate,'vch_no'=>$rs['vch_no'],'debit_amount'=>$rs['debit_amount'],'vch_type'=>$rs['vch_type']));
				}
				else if(isset($_GET['srchShpsName']))
				{
					/*$a = $rs['sales_date'];
					$a = DateTime::createFromFormat('m-d-y', $a)->format('Y-m-d');*/
					array_push($salesArr,array('Status'=>'success','particulars'=>$rs['particulars'],'debit_amount'=>$rs['debit_amount']));
				}
				else if(isset($_GET['daysWise']))
				{
					array_push($salesArr,array('Status'=>'success','particulars'=>$rs['particulars'],'debit_amount'=>$rs['debit_amount']));
				}
				else
					array_push($salesArr,array('Status'=>'success','particulars'=>$rs['particulars'],'debit_amount'=>$rs['debit_amount']));
			}
			echo '{"Result":'.json_encode($salesArr,JSON_UNESCAPED_SLASHES).'}';
		}
		else
		{
			array_push($salesArr,array('shopName'=>'emptySet'));
			echo '{"Result":'.json_encode($salesArr,JSON_UNESCAPED_SLASHES).'}';
		}
	}
?>