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
$res_njit=login_njit($ucid,$pass);
$data = array(
	'backend' => $res_project, 
	'njit' => $res_njit
);
echo json_encode($data);


// curl backend 
function login_project($requestType,$ucid,$pass){
	//data from json response
	$data = array('requestType' => $requestType, 'ucid' => $ucid,'pass' =>$pass);
	//url to backend
	$url = "https://web.njit.edu/~dn236/CS490/beta/tempBackend/userTbl.php";
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
	//decoding response
	$response_decode = json_decode($response);
	//return response
	return $response_decode[0];
}


// curl njit
function login_njit($ucid,$pass){
	$url = "http://myhub.njit.edu/vrs/ldapAuthenticateServlet";
	$data= array("user_name" => $ucid,"passwd" =>$pass);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
	$response = curl_exec($ch);
	curl_close ($ch);
	if($response == "") {
		return "Verified";
	}
	else {
		return "Rejected";
	}
}
?>