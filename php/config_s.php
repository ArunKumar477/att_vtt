<?php
/*		$server = "localhost";
		$username = "veeteete_355";
		$password = "53_6iizQ";
		$database = "veeteete_project_t";
		define('UPLOAD_DIR','../images/');
		$con = new mysqli($server, $username, $password, $database);
		$db = $con;
		if ($con->connect_error) {
			$err_db = mysqli_select_db($database) or die("Connection failed: " . $con->connect_error);
		}
*/
		$server = "localhost";
		$username = "root";
		$password = "";
		$database = "samsung_care";
		define('UPLOAD_DIR','../images/');
		$con = new mysqli($server, $username, $password, $database);
		$db = $con;
		if ($con->connect_error) {
			$err_db = mysqli_select_db($database) or die("Connection failed: " . $con->connect_error);
		}
?>