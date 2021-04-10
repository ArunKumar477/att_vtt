<?php 
	require_once('config.php');
	date_default_timezone_set('Asia/Kolkata');
	$current_time =  date("Y-m-d H:i:s");
	$current =  date("Y-m-d");
	if($con)
	{
		if($_REQUEST['type'] == "checked_in" )
		{
			$userMobile = $_REQUEST['workerName'];
			$lat = $_REQUEST['attndsLat'];
			$lng = $_REQUEST['attndsLong'];
			$shopId = $_REQUEST['shopAttendsTxt'];
			$getUserId = mysqli_query($con,"SELECT `id` FROM `app_users` WHERE `user_name` = ".$userMobile);
			$data = mysqli_fetch_array($getUserId);
			$user_id = $data['id'];
			if(mysqli_num_rows($getUserId) > 0 ){
				$get_attendance = mysqli_query($con,"INSERT INTO `attendance_log`( `user_id`, `shop_id`, `start_time`, `lat`, `long`, `create_date`) values('$user_id','$shopId','$current_time','$lat','$lng','$current_time')");
				if($get_attendance ==1 ){
						$last_id = mysqli_query($con,"SELECT start_time FROM attendance_log ORDER BY id DESC LIMIT 0 , 1" );
						$row = mysqli_fetch_array($last_id);
						$start_time = $row['start_time'];
					echo json_encode(array('status'=>'success','action'=>'checked_in','start_time'=>$start_time));
					exit;
				}else{
					echo json_encode(array('status'=>'failed'));
					exit;
				}
			}
			// else{
			// 	$get_attendance = mysqli_query($con,"UPDATE `attendance_log` SET `start_time` = ".$current_time." WHERE `user_id` = ".$user_id );

			// 	if($get_attendance ==1 ){
			// 		echo json_encode(array('status'=>'success','action'=>'checked_in'));
			// 		exit;
			// 	}else{
			// 		echo json_encode(array('status'=>'failed'));
			// 		exit;
			// 	}

			// }

		}//check in

		if($_REQUEST['type'] == "checked_out" )
		{
			$userMobile = $_REQUEST['workerName'];
			$lat = $_REQUEST['attndsLat'];
			$lng = $_REQUEST['attndsLong'];
			$shopId = $_REQUEST['shopAttendsTxt'];
			$getUserId = mysqli_query($con,"SELECT `id` FROM `app_users` WHERE `user_name` = ".$userMobile);
			$data = mysqli_fetch_array($getUserId);
			$user_id = $data['id'];

			$logTime = mysqli_query($con,"SELECT s.id as shopId,al.id as updateId,al.start_time,al.end_time FROM attendance_log al
				JOIN shops s on al.shop_id = s.id
				WHERE al.user_id = ".$user_id." ORDER BY al.id DESC LIMIT 1" );
			$logData = mysqli_fetch_array($logTime);
			$cnt = mysqli_num_rows($logTime);
			$update_id = $logData['updateId'];
				if($cnt  > 0 ){
					$get_attendance = mysqli_query($con,"UPDATE `attendance_log` SET `end_time` = '".$current_time."' WHERE `id` = ".$update_id );
					if($get_attendance == true){
						$checklog = mysqli_query($con,"SELECT al.start_time,al.end_time,al.create_date FROM attendance_log al
										WHERE al.user_id = ".$user_id." ORDER BY al.id DESC LIMIT 1" );
						$checkData = mysqli_fetch_array($checklog);
						$start_tm =  new DateTime($checkData['start_time']);
						$end_tm =  new DateTime($checkData['end_time']);
						$interval = $start_tm->diff($end_tm);
						//echo $interval->format('%Y Y %m M %d D %H Hr %i Min %s Sec');
						$calc_tm = $interval->format('%H : %i : %s');
						$createdDate = $checkData['create_date'];
						echo json_encode(array('status'=>'success','action'=>'checked_out','data'=>$calc_tm,'create_date'=>$createdDate));

					}
					exit;
				}else{

					// $logTime = mysqli_query($con,"SELECT id,start_time,end_time FROM `app_users` WHERE `user_id` = ".$user_id);
					// $logData = mysqli_fetch_array($logTime);
					// $start_tm = $logData['start_time'];
					// $end_tm = $logData['end_time'];
					// $calc_tm = $start_tm - $end_tm;					
					echo json_encode(array('status'=>'failed'));
					exit;
				}
		}//check out

		//log time calculation
		if($_REQUEST['type'] == "logTimeData" )
		{
			$userId = $_REQUEST['userId'];
			$from_date = $_REQUEST['from_date'];
			$to_date = $_REQUEST['to_date'];
			$getData = mysqli_query($con,"SELECT id,user_id,shop_id,start_time,end_time,create_date,update_date FROM attendance_log WHERE user_id = $userId AND (`start_time` BETWEEN '$from_date' AND '$to_date')");
			$cnt = mysqli_num_rows($getData);
			$data = [];
			while ($data_val = mysqli_fetch_array($getData)) {
				array_push($data, $data_val);
			}
			if($cnt > 0){
				echo json_encode(array('status'=>'success','data'=>$data));
				exit;
			}else{
				echo json_encode(array('status'=>'failed'));
				exit;
			}
		}//	logTimeData
	}//$con
?>