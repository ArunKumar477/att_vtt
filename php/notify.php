<?php 
	class new_class
	{
		function sendPushNotification($to = '', $data = array(), $con)
                {
			$apiKey = 'AIzaSyC0XLjb2HE_nYuz-302V07i28_IA6iRKE8';
			$fields = array( 'to' => $to, 'notification' => $data);
			
			$headers = array( 'Authorization: key='.$apiKey, 'Content-Type: application/json');
			
			$url = 'https://fcm.googleapis.com/fcm/send';
			
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$url);
			curl_setopt($curl,CURLOPT_POST, true);
			curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
			
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($fields));
			$result = curl_exec($curl);
			curl_close($curl);
			return json_decode($result, true);
		}
	}
	
?>