<?php 
	require_once('config.php');
	//require_once('config_s.php');
	$userMbl  = $_GET['userMbl'];
	$currDate = date("Y-m-d");
	if($con)
	{
			$sql = "select a.shop_id,s.Name,a.latitude,a.longitude from attendance a,shops s where a.fos='$userMbl' and a.shop_id=s.id and DATE(a.created)='$currDate'";
			$ex = mysqli_query($con,$sql);
			$cnt = mysqli_num_rows($ex);
			$shops = array();
			if($cnt>0)
			{
				while($rs = mysqli_fetch_array($ex))
				{
					array_push($shops,array('Status'=>'success','shopId'=>$rs['shop_id'],'shopName'=>$rs['Name'],'lat'=>$rs['latitude'],'long'=>$rs['longitude']));
				}
					//var_dump($shops);
					$dlat = '12.9811652';
					$dlong = '80.235419';
					$lat2 = 0;
					$lon2 = 0;
					$shp = array();
					$totalKm = 0;
					for($i=0;$i<sizeof($shops);$i++)
					{
						if($i==0)
						{
							$lat1 = $dlat;
							$lon1 = $dlong;
							$lat2 = $shops[$i]['lat'];
							$lon2 = $shops[$i]['long'];							
						}
						if($i>0)
						{
							$lat1 = $lat2;
							$lon1 = $lon2;
							$lat2 = $shops[$i]['lat'];
							$lon2 = $shops[$i]['long'];
						}
						$theta = $lon1 - $lon2;
						$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
						$dist = acos($dist);
						$dist = rad2deg($dist);
						$miles = $dist * 60 * 1.1515;
						//$unit = strtoupper($unit);
						$kilo_meter = $miles * 1.609344;
						//var_dump($i.'        '.$lat1.','.$lon1 . '<-->' .  $lat2.','.$lon2. 'km - '. round($kilo_meter));
						array_push($shp,array('Status'=>'success','shopId'=>$shops[$i]['shopId'],'shopName'=>$shops[$i]['shopName'],'km'=>round($kilo_meter)));
						$totalKm += $kilo_meter; 
					}
					//var_dump($i.'        '.$lat2.','.$lon2 . '<-->' .  $dlat.','.$dlong);
					$lat1 = $lat2;
					$lon1 = $lon2;
					$lat2 = $dlat;
					$lon2 = $dlong;
					
					$theta = $lon1 - $lon2;
					$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
					$dist = acos($dist);
					$dist = rad2deg($dist);
					$miles = $dist * 60 * 1.1515;
					//$unit = strtoupper($unit);
					$kilo_meter = $miles * 1.609344;
					array_push($shp,array('Status'=>'success','shopId'=>'v5','shopName'=>'veetee communications','km'=>round($kilo_meter)));
					$totalKm += $kilo_meter;
								
					$query = "select Rate_per_kilometer from rates where From_date<='$currDate' and To_date>='$currDate'";
					$exe = mysqli_query($con,$query);
					$rate = mysqli_fetch_array($exe);
					$ratePerKm = $rate['Rate_per_kilometer'];
					$petrolAllowance = $ratePerKm*round($totalKm);
					array_push($shp,array('totalKm'=>round($totalKm),'petrolAllowance'=>$petrolAllowance));
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