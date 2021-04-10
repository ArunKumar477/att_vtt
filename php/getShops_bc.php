<?php 
	require_once('config.php');
	//require_once('config_s.php');
	$userMbl  = $_GET['userMbl'];
	$app_userId = $_GET['app_userId'];
	if(isset($_GET['srchDateWise']))
		$currDate = $_GET['srchDateWise'];
	else
		$currDate = date("Y-m-d");
	if($con)
	{
			$getRights = mysqli_query($con,"select user_name,rights,Latitude,Longitude from app_users where id='$app_userId' and Active='1'");
			$home_lat = 0;
			$home_lng = 0;
			if($getRights)
			{
				$userMbl_ex = mysqli_fetch_array($getRights);
				$userMbl = $userMbl_ex['user_name'];
				$h_lat = $userMbl_ex['Latitude'];
				$h_lng = $userMbl_ex['Longitude'];
				if($h_lat!=0 && $h_lng!=0)
				{
					$home_lat = $h_lat;
					$home_lng = $h_lng;
					//echo $home_lat.','.$home_lng;
				}
			}
			$sql = "select a.shop_id,s.Name,a.latitude,a.longitude,Time(a.attendance_date) as attnds_time from attendance a,shops s where a.fos='$userMbl' and a.shop_id=s.id and DATE(a.attendance_date)='$currDate' and s.Deleted='0'";
			$fromToDate = "SELECT min(`attendance_date`) as first_attendance,max(`attendance_date`) as last_attendance,min(Time(`attendance_date`)) as fromTime,max(Time(`attendance_date`)) as toTime FROM `attendance` where fos='$userMbl' and Date(attendance_date)='$currDate'";
			$ex = mysqli_query($con,$sql);
			$cnt = mysqli_num_rows($ex);
			$shops = array();
			if($cnt>0)
			{
				while($rs = mysqli_fetch_array($ex))
				{
					array_push($shops,array('Status'=>'success','shopId'=>$rs['shop_id'],'shopName'=>$rs['Name'],'lat'=>$rs['latitude'],'long'=>$rs['longitude'],'attnds_time'=>$rs['attnds_time']));
				}
					
					$dlat = '12.990939';
					$dlong = '80.2182998';
					$lat2 = 0;
					$lon2 = 0;
					$shp = array();
					$totalKm = 0;
					for($i=0;$i<sizeof($shops);$i++)
					{
						if($i==0)
						{
							if($shops[$i]['lat']!=0 && $shops[$i]['long']!=0)	
							{
								$lat1 = $dlat;
								$lon1 = $dlong;
								$lat2 = $shops[$i]['lat'];
								$lon2 = $shops[$i]['long'];							
							}
							else
							{
								$shpId = $shops[$i]['shopId'];
								$ssql = "select Latitude,Longitude from shops where id='$shpId' and Deleted='0'";
								$exe = mysqli_query($con,$ssql);
								$cnt = mysqli_num_rows($exe);
								if($cnt==1)
								{
									$res = mysqli_fetch_array($exe);
									$lat1 = $dlat;
									$lon1 = $dlong;
									$lat2 = $res['Latitude'];
									$lon2 = $res['Longitude'];	
								}
							}
						}//if($i==0)
						if($i>0)
						{
							if($shops[$i]['lat']!=0 && $shops[$i]['long']!=0)	
							{
								$lat1 = $lat2;
								$lon1 = $lon2;
								$lat2 = $shops[$i]['lat'];
								$lon2 = $shops[$i]['long'];
							}
							else
							{
								$shpId = $shops[$i]['shopId'];
								$ssql = "select Latitude,Longitude from shops where id='$shpId' and Deleted='0'";
								$exe = mysqli_query($con,$ssql);
								$cnt = mysqli_num_rows($exe);
								if($cnt==1)
								{
									$res = mysqli_fetch_array($exe);
									$lat1 = $dlat;
									$lon1 = $dlong;
									$lat2 = $res['Latitude'];
									$lon2 = $res['Longitude'];	
								}
							}
						}//if($i>0)
						/*$theta = $lon1 - $lon2;
						$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
						$dist = acos($dist);
						$dist = rad2deg($dist);
						$miles = $dist * 60 * 1.1515;
						//$unit = strtoupper($unit);
						$kilo_meter = $miles * 1.609344;*/
						/* Get Driving Distance(3) */
							$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$lon1."&destinations=".$lat2.",".$lon2."&mode=driving&language=pl-PL";
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
							$response = curl_exec($ch);
							curl_close($ch);
							//echo var_dump($response);
							$response_a = json_decode($response, true);
							$kilo_meter = $response_a['rows'][0]['elements'][0]['distance']['text'];
							$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
							if(strpos($kilo_meter,','))
								$kilo_meter = str_replace(',','.',$kilo_meter);
							if(strpos($kilo_meter,'km'))
								$kilo_meter = str_replace('km','',$kilo_meter);
							if(strpos($kilo_meter,' '))
								$kilo_meter = str_replace(' ','',$kilo_meter);
							if(strpos($kilo_meter,'m'))
							{
								$kilo_meter = str_replace('m','',$kilo_meter);	
								$kilo_meter = $kilo_meter/1000;
								$kilo_meter = round($kilo_meter, 1, PHP_ROUND_HALF_UP);
							}
						/* End Driving Distance */
						//var_dump($i.'    '.$shops[$i]['shopId'].'    '.$lat1.','.$lon1 . '<-->' .  $lat2.','.$lon2. 'km - '. round($kilo_meter));
						array_push($shp,array('Status'=>'success','shopId'=>$shops[$i]['shopId'],'shopName'=>$shops[$i]['shopName'],'km'=>$kilo_meter,'attnds_time'=>$shops[$i]['attnds_time'],'lat'=>$shops[$i]['lat'],'long'=>$shops[$i]['long']));
						$totalKm += $kilo_meter; 
					}//for
					//var_dump($i.'        '.$lat2.','.$lon2 . '<-->' .  $dlat.','.$dlong);
					if($home_lat!=0 && $home_lng!=0)
					{
						$lat1 = $lat2;
						$lon1 = $lon2;
						$lat2 = $home_lat;
						$lon2 = $home_lng;
						
						/*$theta = $lon1 - $lon2;
						$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
						$dist = acos($dist);
						$dist = rad2deg($dist);
						$miles = $dist * 60 * 1.1515;
						//$unit = strtoupper($unit);
						$kilo_meter = $miles * 1.609344;*/
						/* Get Driving Distance(3) */
							$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$lon1."&destinations=".$lat2.",".$lon2."&mode=driving&language=pl-PL";
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
							$response = curl_exec($ch);
							curl_close($ch);
							//echo var_dump($response);
							$response_a = json_decode($response, true);
							$kilo_meter = $response_a['rows'][0]['elements'][0]['distance']['text'];
							$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
							if(strpos($kilo_meter,','))
								$kilo_meter = str_replace(',','.',$kilo_meter);
							if(strpos($kilo_meter,'km'))
								$kilo_meter = str_replace('km','',$kilo_meter);
							if(strpos($kilo_meter,' '))
								$kilo_meter = str_replace(' ','',$kilo_meter);
							if(strpos($kilo_meter,'m'))
							{
								$kilo_meter = str_replace('m','',$kilo_meter);	
								$kilo_meter = $kilo_meter/1000;
								$kilo_meter = round($kilo_meter, 1, PHP_ROUND_HALF_UP);
							}
						/* End Driving Distance */
						array_push($shp,array('Status'=>'success','shopId'=>'v5','shopName'=>'Your home','km'=>$kilo_meter,'lat'=>$lat2,'long'=>$lon2));
					}
					$totalKm += $kilo_meter;
					$mileage = 0;
					$Price = 0;		
					$query = mysqli_query($con,"SELECT ifnull(Price,0) as Price,(SELECT mileage FROM `fixed_mileage` WHERE ((`From_date`<='$currDate' and `To_date`>='$currDate') or (`From_date`<='$currDate' and `To_date`='0000-00-00'))) as mileage FROM rates WHERE ((`From_date`<='$currDate' and `To_date`>='$currDate') or (`From_date`<='$currDate' and `To_date`='0000-00-00'))");
					if(mysqli_num_rows($query)>0)
					{
						$rate = mysqli_fetch_array($query);
						$mileage = $rate['mileage'];
						$Price = $rate['Price'];
					}
					//$petrolAllowance = $ratePerKm*round($totalKm);
					$first_attendance = '';
					$last_attendance = '';
					$spentTime = '';
					$fromTime = '';
					$toTime = '';
					if($fromToDate)
					{
						$fromToDate_exe = mysqli_query($con,$fromToDate);
						$fromToDate_res = mysqli_fetch_array($fromToDate_exe);
						$first_attendance = $fromToDate_res['first_attendance'];
						$last_attendance = $fromToDate_res['last_attendance'];
						$date_a = new DateTime($first_attendance);
						$date_b = new DateTime($last_attendance);
						$interval = date_diff($date_a,$date_b);
						$spentTime = $interval->format('%h:%i:%s');
						$fromTime = explode(":",$fromToDate_res['fromTime']);
						$toTime = explode(":",$fromToDate_res['toTime']);
						$fromTime = date("g:i a", strtotime($fromTime[0].':'.$fromTime[1]));
						$toTime = date("g:i a", strtotime($toTime[0].':'.$toTime[1]));
					}
					array_push($shp,array('totalKm'=>round($totalKm),'first_attendance'=>$first_attendance,'last_attendance'=>$last_attendance,'spentTime'=>$spentTime,'fromTime'=>$fromTime,'toTime'=>$toTime,'mileage'=>$mileage,'Price'=>$Price));
					//echo round($totalKm);
					echo '{"Result":'.json_encode($shp,JSON_UNESCAPED_SLASHES).'}';
			}
			else
			{
				$shops=array('shopName'=>'emptySet');
				echo '{"Result":'.json_encode($shops,JSON_UNESCAPED_SLASHES).'}';
			}
	}
?>