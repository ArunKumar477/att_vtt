<?php 
	$lat1 = '12.9909444';
	$lon1 = '80.218332';
	$lat2 = '12.9048';
	$lon2 = '80.0891';
	$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$lon1."&destinations=".$lat2.",".$lon2."&mode=driving&language=pl-PL";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	curl_close($ch);
	echo var_dump($response);
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
	echo $kilo_meter;
?>