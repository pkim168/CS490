<?php
	$ucidErr = $passErr = "";
	$ucid = $password = "";
	if(empty($_POST["ucid"])){
		header('Location: ./index.php');
	}
	if(empty($_POST["pass"])){
		header('Location: ./index.php');
	}
	$ucid = $_POST["ucid"];
	$pass = $_POST["pass"];
	$url = "https://web.njit.edu/~jrd62/CS490/login.php";
	
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
	
	echo $result;
	
?>