<?php
//grabbing json from backend
$str_json = file_get_contents('php://input'); 
//decoding json into response array
$response = json_decode($str_json, true); 
//initial setting of variables
$requestType='';
$ucid="";
$pass="";

if(isset($response['requestType'])) $requestType = $response['requestType'];
if(isset($response['ucid'])) $ucid = $response['ucid'];
if(isset($response['pass'])) $pass = $response['pass'];

$res_project=login_project($requestType,$ucid,$pass);	
echo $res_project;


// curl backend 
function login_project($requestType,$ucid,$pass){
	//data from json response
	$data = array('requestType' => $requestType, 'ucid' => $ucid,'pass' =>$pass);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/userTbl.php";
	//initialize curl session and return a curl handle
	$ch = curl_init($url);
	//options for a curl transfer
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	//execute curl session
	$response = curl_exec($ch);
	//close curl session
	curl_close ($ch);
	//return response
	return $response;
}
?>