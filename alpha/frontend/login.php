<?php
	$ucidErr = $passErr = "";
	$ucid = $password = "";
	if(empty($_POST["ucid"])){
		header('Location: ./front.php');
	}
	if(empty($_POST["pass"])){
		header('Location: ./front.php');
	}
	$ucid = $_POST["ucid"];
	$pass = $_POST["pass"];
	$url = "https://web.njit.edu/~jrd62/CS490/index.php";
	
	$ch = curl_init($url);
	$data = array(
				"requestType" => "login",
				"ucid" => $ucid,
				"pass" => $pass);
				
	$payload = json_encode($data);
	
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$result = curl_exec($ch);
	curl_close($ch);
	var_dump($result);
	$result = json_decode($result, true);
	echo json_last_error();
	var_dump($result);
	echo "Database: ".$result[0];
	echo "NJIT: ".$result[1];
	
?>