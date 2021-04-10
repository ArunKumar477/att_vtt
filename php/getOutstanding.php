<?php 
	//require_once('config_s.php');
	require_once('config.php');
	$app_userId = $_GET['app_userId'];
	if(isset($_GET['party_name']))
	{
		$party_name = $_GET['party_name'];
		if($_GET['outstnd_res']!=0 || $_GET['outstnd_res']!='')
			$outstnd_res = $_GET['outstnd_res'];
		else
			$outstnd_res = 0;
		if (strpos($party_name, '!!') !== false)
			$party_name = str_replace("!!","&",$party_name);
		if (strpos($party_name, '@@') !== false)
			$party_name = str_replace("@@","#",$party_name);
		$party_name = mysqli_real_escape_string($con,$party_name);	
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
	$currDate = date("Y-m-d");
	if($con)
	{
		if(isset($_GET['party_name']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
			{
				if($outstnd_res==0 || $outstnd_res=='')
					$sql = "select * from outstandings o,shops s where o.party_name='$party_name' and o.party_name=s.Name and s.Deleted='0'";
				else
					$sql = "select * from outstandings o,shops s where o.party_name='$party_name' and o.overdue>$outstnd_res and o.party_name=s.Name and s.Deleted='0'";
			}
			else
			{
				if($outstnd_res==0 || $outstnd_res=='')	
					$sql = "select * from outstandings o,shops s where o.party_name='$party_name' and o.party_name=s.Name and s.fos='$app_userId' and s.Deleted='0'";
				else
					$sql = "select * from outstandings o,shops s where o.party_name='$party_name' and o.overdue>$outstnd_res and o.party_name=s.Name and s.fos='$app_userId' and s.Deleted='0'";
			}
		}
		else if(isset($_GET['srchShpsName']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
			{	
				if($days==0 || $days=='')
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount from outstandings o,shops s where o.party_name='$srchShpsName' and o.party_name=s.Name and s.Deleted='0' group by o.party_name";
				else
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount from outstandings o,shops s where o.overdue>$days and o.party_name='$srchShpsName' and o.party_name=s.Name and s.Deleted='0' group by o.party_name";
			}
			else
			{
				if($days==0 || $days=='')
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount from outstandings o,shops s where o.party_name='$srchShpsName' and o.party_name=s.Name and s.fos='$app_userId' and s.Deleted='0' group by o.party_name";
				else
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount from outstandings o,shops s where o.overdue>$days and o.party_name='$srchShpsName' and o.party_name=s.Name and s.fos='$app_userId' and s.Deleted='0' group by o.party_name";
			}
		}
		else if(isset($_GET['daysWise']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
			{
				if($daysWise==0 || $daysWise=='')
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount,o.overdue from outstandings o,shops s where o.party_name=s.Name and s.Deleted='0' group by o.party_name";//"7/15/30 days	
				else
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount,o.overdue from outstandings o,shops s where o.overdue>$daysWise and o.party_name=s.Name and s.Deleted='0' group by o.party_name";//"7/15/30 days	
			}
			else
			{
				if($daysWise==0 || $daysWise=='')
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount,o.overdue from outstandings o,shops s where o.party_name=s.Name and s.fos='$app_userId' and s.Deleted='0' group by o.party_name";//"7/15/30 days	
				else
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount,o.overdue from outstandings o,shops s where o.overdue>$daysWise and o.party_name=s.Name and s.fos='$app_userId' and s.Deleted='0' group by o.party_name";//"7/15/30 days	
			}
		}
		else if(isset($_GET['shpName']))
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
			{
				if($daysWise_val==0 || $daysWise_val=='')
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount from outstandings o,shops s where o.party_name='$shpName' and o.party_name=s.Name and s.Deleted='0' group by o.party_name";
				else
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount from outstandings o,shops s where o.overdue>$daysWise_val and o.party_name='$shpName' and o.party_name=s.Name and s.Deleted='0' group by o.party_name";
			}
			else
			{
				if($daysWise_val==0 || $daysWise_val=='')
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount from outstandings o,shops s where o.party_name='$shpName' and o.party_name=s.Name and s.fos='$app_userId' and s.Deleted='0' group by o.party_name";
				else
					$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount from outstandings o,shops s where o.overdue>$daysWise_val and o.party_name='$shpName' and o.party_name=s.Name and s.fos='$app_userId' and s.Deleted='0' group by o.party_name";
			}
		}
		else
		{
			$getRights = "select rights from app_users where id='$app_userId' and Active=1";
			$rightsExe = mysqli_query($con,$getRights);
			$rights = mysqli_fetch_array($rightsExe);
			if($rights['rights']=='2')
				$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount from outstandings o,shops s where o.party_name=s.Name and s.Deleted='0' group by o.party_name";
			else
				$sql = "select distinct o.party_name,count(*),sum(o.pending_amount) as pending_amount from outstandings o,shops s where o.party_name=s.Name and s.fos='$app_userId' and s.Deleted='0' group by o.party_name";
		}
		$ex = mysqli_query($con,$sql);
		$cnt = mysqli_num_rows($ex);
		$outstndsArr = array();
		if($cnt>0)
		{
			while($rs = mysqli_fetch_array($ex))
			{
				if(isset($_GET['party_name']))
					array_push($outstndsArr,array('Status'=>'success','outstanding_date'=>$rs['outstanding_date'],'ref_no'=>$rs['ref_no'],'pending_amount'=>$rs['pending_amount'],'overdue'=>$rs['overdue']));
				else
					array_push($outstndsArr,array('Status'=>'success','party_name'=>$rs['party_name'],'pending_amount'=>$rs['pending_amount']));
			}
			echo '{"Result":'.json_encode($outstndsArr,JSON_UNESCAPED_SLASHES).'}';
		}
		else
		{
			array_push($outstndsArr,array('shopName'=>'emptySet'));
			echo '{"Result":'.json_encode($outstndsArr,JSON_UNESCAPED_SLASHES).'}';
		}
	}
?>