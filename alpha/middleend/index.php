<?php
//grabbing json from backend
$str_json = file_get_contents('php://input'); 
//decoding json into response array
$response = json_decode($str_json, true); 
//initial setting of variables
$ucid="none";
$pass="none";

if(isset($response['ucid'])) $ucid = $response['ucid'];
if(isset($response['pass'])) $pass = $response['pass'];

$res_project=login_project($ucid,md5($pass));	
$res_njit=login_njit($ucid,$pass);
$data = array($res_project,$res_njit);
echo json_encode($data);


// curl backend 
function login_project($ucid,$pass){
	//data from json response
	$data = array('ucid' => $ucid,'pass' =>$pass);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/alpha/login.php";
	//initialize curl session and return a curl handle
	$ch = curl_init();
	//options for a curl transfer
	curl_setopt($ch, CURLOPT_URL, $url);
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
	//url to njit 
	$url = "http://myhub.njit.edu/vrs/ldapAuthenticateServlet";
	//data from json response
	$data= array("user_name" => 'jrd62',"passwd" =>'JrDom1997');
	//initialize curl session and return a curl handle
	$ch = curl_init();
	//options for a curl transer
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
	//execute curl session
	$response = curl_exec($ch);
	//close curl session
	curl_close ($ch);
	//return response
	return $response;
}
?>