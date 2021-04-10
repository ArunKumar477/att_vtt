<?php 
	//require_once('config.php');
	require_once('config_s.php');
	$userMbl  = $_GET['userMbl'];
	$app_userId = $_GET['app_userId'];
	if(isset($_GET['srchDateWise']))
		$currDate = $_GET['srchDateWise'];
	else
		$currDate = date("Y-m-d");
	if($con)
	{
			$getRights = mysqli_query($con,"select user_name,rights,Latitude,Longitude,Address from app_users where id='$app_userId' and Active='1'");
			$home_lat = 0;
			$home_lng = 0;
			if($getRights)
			{
				$userMbl_ex = mysqli_fetch_array($getRights);
				$userMbl = $userMbl_ex['user_name'];
				$h_lat = $userMbl_ex['Latitude'];
				$h_lng = $userMbl_ex['Longitude'];
				$Address = $userMbl_ex['Address'];
				if($h_lat!=0 && $h_lng!=0)
				{
					$home_lat = $h_lat;
					$home_lng = $h_lng;
					//echo $home_lat.','.$home_lng;
				}
			}
			$sql = "select a.shop_id,s.Name,CONCAT(s.Address_one,s.Address_two) as Address,a.latitude,a.longitude,Time(a.attendance_date) as attnds_time from attendance a,shops s where a.fos='$userMbl' and a.shop_id=s.id and DATE(a.attendance_date)='$currDate' and s.Deleted='0'";
			$fromToDate = "SELECT min(`attendance_date`) as first_attendance,max(`attendance_date`) as last_attendance,min(Time(`attendance_date`)) as fromTime,max(Time(`attendance_date`)) as toTime FROM `attendance` where fos='$userMbl' and Date(attendance_date)='$currDate'";
			$ex = mysqli_query($con,$sql);
			$cnt = mysqli_num_rows($ex);
			$shops = array();
			if($cnt>0)
			{
				while($rs = mysqli_fetch_array($ex))
				{
					array_push($shops,array('Status'=>'success','shopId'=>$rs['shop_id'],'shopName'=>$rs['Name'],'Address'=>$rs['Address'],'lat'=>$rs['latitude'],'long'=>$rs['longitude'],'attnds_time'=>$rs['attnds_time']));
				}
				if($home_lat!=0)	
					array_push($shops,array('Status'=>'success','shopId'=>'','shopName'=>'Your home','lat'=>$home_lat,'long'=>$home_lng,'attnds_time'=>'00:00','Address'=>$Address));
					
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
						}//if($i>0)
						$theta = $lon1 - $lon2;
						$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
						$dist = acos($dist);
						$dist = rad2deg($dist);
						$miles = $dist * 60 * 1.1515;
						//$unit = strtoupper($unit);
						$kilo_meter = $miles * 1.609344;
						
						/* Get Distance(2) AC */
							$lat1_A = $lat1;
							$lon1_A = $lon1;
							$lat2_C = $lat1;
							$lon2_C = $lon2;
							$theta1 = $lon1_A - $lon2_C;
							$dist1 = sin(deg2rad($lat1_A)) * sin(deg2rad($lat2_C)) +  cos(deg2rad($lat1_A)) * cos(deg2rad($lat2_C)) * cos(deg2rad($theta1));
							$dist1 = acos($dist1);
							$dist1 = rad2deg($dist1);
							$miles1 = $dist1 * 60 * 1.1515;
							$kilo_meter1 = $miles1 * 1.609344;
							
						/* End AC */
						
						/* Get Distance(2) BC */
							$lat1_B = $lat2;
							$lon1_B = $lon2;
							$lat2_C = $lat1;
							$lon2_C = $lon2;
							$theta2 = $lon1_B - $lon2_C;
							$dist2 = sin(deg2rad($lat1_B)) * sin(deg2rad($lat2_C)) +  cos(deg2rad($lat1_B)) * cos(deg2rad($lat2_C)) * cos(deg2rad($theta2));
							$dist2 = acos($dist2);
							$dist2 = rad2deg($dist2);
							$miles2 = $dist2 * 60 * 1.1515;
							$kilo_meter2 = $miles2 * 1.609344;
							
						/* End BC */
						
						/* AC+BC Start */
							$AcBc_distance = $kilo_meter1+$kilo_meter2;
						/* End AC+BC */
						
						
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
							$dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
							$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
						/* End Driving Distance */
						
						array_push($shp,array('Status'=>'success','shopId'=>$shops[$i]['shopId'],'shopName'=>$shops[$i]['shopName'],'km'=>$kilo_meter,'attnds_time'=>$shops[$i]['attnds_time'],'lat'=>$shops[$i]['lat'],'long'=>$shops[$i]['long'],'AcBc_distance'=>$AcBc_distance,'Driving_distance'=>$dist,'Address'=>$shops[$i]['Address']));
						$totalKm += $kilo_meter; 
					}//for
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