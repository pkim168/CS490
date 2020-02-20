<?php
$str_json = file_get_contents('php://input'); 
$response = json_decode($str_json, true); // decoding received JSON to array
$ucid="none";$pass="none";
if(isset($response['ucid'])) $ucid = $response['ucid'];
if(isset($response['pass'])) $pass = $response['pass'];

$res_proejct=login_project($ucid,md5($pass));	
$res_njit=login_njit($ucid,$pass);
print "<center><h2>".$res_proejct.' '.$res_njit."</h2></center>";

// curl backend 
function login_project($ucid,$pass){
	$data = array('ucid' => $ucid,'pass' =>$pass);
	$url = "https://web.njit.edu/~pk549/490/alpha/login.php";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	$response = curl_exec($ch);
	curl_close ($ch);
	return $response;
}


// curl njit
function login_njit($ucid,$pass){
	$url = "http://myhub.njit.edu/vrs/";
	$data= array("user" => $ucid,"pass" =>$pass,"uuid" => "0xACA021");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
	$response = curl_exec($ch);
	curl_close ($ch);

	if (strpos($response,"Rejected")==false) return "NJIT Accept";
	return "NJIT Reject";
}
?>