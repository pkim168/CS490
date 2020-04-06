<?php
//grabbing json from backend
$str_json = file_get_contents('php://input'); 
//decoding json into response array
$response = json_decode($str_json, true); 

$requestType = $response['requestType'];

//checking request type
switch($requestType) {
    case 'getQuestions':
        //initial setting of variables
        $requestType="getQuestions";
		$difficulty="";
		$constraints="";
		$tag="";
		$keyword="";

        if(isset($response['requestType'])) $requestType = $response['requestType'];
		if(isset($response['difficulty'])) $difficulty = $response['difficulty'];
		if(isset($response['constraints'])) $constraints = $response['constraints'];
        if(isset($response['tag'])) $tag = $response['tag'];
        if(isset($response['keyword'])) $keyword = $response['keyword'];

        $res_project=get_questions($requestType,$difficulty,$constraints,$tag,$keyword);	
        echo $res_project;
        break;
    case 'getTags':
        //initial setting of variables
        $requestType="getTags";

        if(isset($response['requestType'])) $requestType = $response['requestType'];

        $res_project=get_tags($requestType);	
        echo $res_project;
        break;
    case 'newQuestion':
        $res_project=new_question($response);	
        echo $res_project;
        break;
    default: 
        break;
}

// curl backend 
function get_questions($requestType,$difficulty,$constraints,$tag,$keyword){
	//data from json response
	$data = array('requestType' => $requestType, 'difficulty' => $difficulty, 'constraints' => $constraints, 'tag' => $tag, 'keyword' => $keyword);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/questionTbl.php";
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

// curl backend 
function get_tags($requestType){
	//data from json response
	$data = array('requestType' => $requestType);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/questionTbl.php";
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

// curl backend 
function new_question($response){
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/questionTbl.php";
	//initialize curl session and return a curl handle
	$ch = curl_init($url);
	//options for a curl transfer	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
	//execute curl session
	$response = curl_exec($ch);
	//close curl session
	curl_close ($ch);
	//return response
	return $response;
}
?>