<?php
	//require_once('config_s.php');
	require_once('config.php');



	$shopName    = $_POST['sname'];
	$shopName    = addslashes($shopName);
	$shopAdd1    = $_POST['saddr1'];
	$shopAdd2    = $_POST['saddr2'];
	$shopArea    = $_POST['sarea'];
	$shopCity    = $_POST['scity'];
	$shopState   = $_POST['sstate'];
	$shopPin     = $_POST['spincode'];
	//$shopImgPath = '';
	
	if( isset($_POST['slatitude']))
        $Latitude = $_POST['slatitude'];
    else
		$Latitude = 0;

	if( isset($_POST['slongitude']))
		$Longitude= $_POST['slongitude'];
	else
		$Longitude= 0;
	if(isset($_POST['userId']))
		$userId = $_POST['userId'];	
	$setDateTime = date("Y-m-d H:i:s");
	/*if($_POST['simg'])
	{		
		$str1 = "abcdefgthijklmnopqurstvuwxyz";
		$str2 = "01234567890";
		$str  = str_shuffle($str1.$str2);
		$code = substr($str,4,6);
		$name = substr($shopName,0,3);
		
		$img = $_POST['simg'];
		$data = base64_decode($img);
				
		$shopImgPath = UPLOAD_DIR.$name.'_'.$code.'.jpg';
		$path = 'images/'.$name.'_'.$code.'.jpg';
		file_put_contents($shopImgPath, $data);
	}	
	else
	{
		$shopImgPath = "no img";
	}*/
	
	if($con)
	{
		/*$nl = 'NULL';
		$query = $con->prepare("SELECT Name FROM shops WHERE Name=? AND Address_one=? AND Address_two=? AND Latitude=? AND Longitude=?");
		$query->bind_param('sssdd',$shopName,$nl,$nl,$nl,$nl);
		if($query->execute())
		{
			//$sql =$con->prepare('insert into shops(User_id,Name,Address_one,Address_two,Area,City,State,Pincode,Latitude,Longitude) values(?,?,?,?,?,?,?,?,?,?)');
			//$sql->bind_param("sssssssidd",$userId,$shopName,$shopAdd1,$shopAdd2,$shopArea,$shopCity,$shopState,$shopPin,$Latitude,$Longitude);
			$sql = $con->prepare("UPDATE shops SET User_id=?, Address_one=?, Address_two=?, Area=?, City=?, State=?, Pincode=?, Latitude=?, Longitude=? WHERE Name=?");
			$sql->bind_param('ssssssidds',$userId,$shopAdd1,$shopAdd2,$shopArea,$shopCity,$shopState,$shopPin,$Latitude,$Longitude,$shopName);
			
			if( $sql->execute())
			{
				$insRes = array('Status'=>'Saved');
				echo '{"Result":'.json_encode($insRes ,JSON_UNESCAPED_SLASHES).'}';	
			
			}
			else
			{
				$insRes = array('Status'=>'Failed');
				echo '{"Result":'.json_encode($insRes ,JSON_UNESCAPED_SLASHES).'}';
			}
			$sql->close();
		}
		else
		{
			$insRes = array('Status'=>'exists');
			echo '{"Result":'.json_encode($insRes ,JSON_UNESCAPED_SLASHES).'}';
		}

		$query->close();*/
		/*$query = "SELECT id FROM shops WHERE Name='$shopName' and Deleted='0' AND CONCAT(Address_one,Address_two,Latitude,Longitude) is null";
		$exe = mysqli_query($con,$query);
		$cnt = mysqli_num_rows($exe);
		if($exe)
		{
			if($cnt==1)
			{*/
				$sql = "UPDATE shops SET User_id='$userId',Address_one='$shopAdd1',Address_two='$shopAdd2',Area='$shopArea',
				City='$shopCity',State='$shopState',Pincode='$shopPin',Latitude='$Latitude',Longitude='$Longitude',addedShop_date='$setDateTime' WHERE Name='$shopName' 
				and Deleted='0'";
				$ex = mysqli_query($con,$sql);
				if($ex)
				{
					$insRes = array('Status'=>'Saved');
					echo '{"Result":'.json_encode($insRes ,JSON_UNESCAPED_SLASHES).'}';	
				}
				else
				{
					$insRes = array('Status'=>'Failed');
					echo '{"Result":'.json_encode($insRes ,JSON_UNESCAPED_SLASHES).'}';
				}
			/*}
			else
			{
				$insRes = array('Status'=>'exists');
				echo '{"Result":'.json_encode($insRes ,JSON_UNESCAPED_SLASHES).'}';
			}
		}*/
	}
	
	mysqli_close($con);
?>