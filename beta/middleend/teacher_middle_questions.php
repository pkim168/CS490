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
        $tag="";

        if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['difficulty'])) $difficulty = $response['difficulty'];
        if(isset($response['tag'])) $tag = $response['tag'];

        $res_project=get_questions($requestType,$difficulty,$tag);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    case 'getTags':
        //initial setting of variables
        $requestType="getTags";

        if(isset($response['requestType'])) $requestType = $response['requestType'];

        $res_project=get_tags($requestType);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    case 'newQuestion':
        //initial setting of variables
        $requestType="newQuestion";
        $question="";
        $functionName="";
        $difficulty="";
		$tag="";
		$testCases=$response['testCases'];

        if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['question'])) $question = $response['question'];
        if(isset($response['functionName'])) $functionName = $response['functionName'];
        if(isset($response['difficulty'])) $difficulty = $response['difficulty'];
		if(isset($response['tag'])) $tag = $response['tag'];
		if(isset($response['testCases'])) $testCases = $response['testCases'];

        $res_project=new_question($requestType,$question,$functionName,$difficulty,$tag,$testCases);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    default: 
        break;
}

// curl backend 
function get_questions($requestType,$difficulty,$tag){
	//data from json response
	$data = array('requestType' => $requestType, 'difficulty' => $difficulty, 'tag' => $tag);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/beta/questionTbl.php";
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

// curl backend 
function get_tags($requestType){
	//data from json response
	$data = array('requestType' => $requestType);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/beta/questionTbl.php";
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

// curl backend 
function new_question($requestType,$question,$functionName,$difficulty,$tag,$testCases){
	//data from json response
	$data = array('requestType' => $requestType, 'question' => $question, 'functionName' => $functionName, 'difficulty' => $difficulty, 'tag' => $tag, 'testCases' => $testCases);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/beta/questionTbl.php";
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
?>