<?php
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	date_default_timezone_set("Asia/Kolkata");
	$server="localhost";
	$username="root";
	$password="";
	$database="samsung_care";

	$con = mysqli_connect($server,$username,$password,$database);
// Check connection
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}
//echo "Connected successfully";
header('Access-Control-Allow-Origin: *');


/*
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
*/
?>