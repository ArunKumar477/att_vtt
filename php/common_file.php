<?php
	
	require_once("class_file.php");
	require_once("config.php");
	//require_once("config_s.php");
	$myCls = new class_file();
	if(isset($_POST['app_userId']))
	{
		$app_userId = isset($_POST['app_userId']);
		$res = $myCls->getDelvrdUnDelvrdInfo($app_userId,$con);
		echo '{"Result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
	}
	if(isset($_POST['getAllModels']))
	{
		$res = $myCls->getAllProductModel($con);
		echo '{"Result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
	}
	if(isset($_POST['closeStockData']))
	{
		//$closeStockData = '[{"product_model":"105 DS","color":"Black New","qty":"1","totalQty":1}]';
		$closeStockData = $_POST['closeStockData'];
		$user_id = $_POST['user_id'];
		$jsonData = json_decode($closeStockData,true);
		$res = $myCls->setAllClosingStocks($con,$jsonData,$user_id);
		echo '{"Result":'.json_encode($res,JSON_UNESCAPED_SLASHES).'}';
	}
?>