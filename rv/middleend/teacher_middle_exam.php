<?php
//grabbing json from backend
$str_json = file_get_contents('php://input'); 
//decoding json into response array
$response = json_decode($str_json, true); 

$requestType = $response['requestType'];

//checking request type
switch($requestType) {
    case 'getExams':
		//initial setting of variables
		$requestType="getExams";
        $ucid="";

		if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['ucid'])) $ucid = $response['ucid'];

        $res_project=get_exams($requestType,$ucid);	
        echo $res_project;
        break;
    case 'getExamQuestions':
		//initial setting of variables
		$requestType="getExamQuestions";
        $examId="";

		if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['examId'])) $examId = $response['examId'];

        $res_project=get_exam_questions($requestType,$examId);	
        echo $res_project;
        break;
    case 'getExamStatuses':
		//initial setting of variables
		$requestType="getExamStatuses";
        $examId="";

		if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['examId'])) $examId = $response['examId'];

        $res_project=get_exam_statuses($requestType,$examId);	
        echo $res_project;
        break;
    case 'getStudentAnswers':
		//initial setting of variables
		$requestType="getStudentAnswers";
        $examId="";
        $ucid="";

		if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['examId'])) $examId = $response['examId'];
        if(isset($response['ucid'])) $ucid = $response['ucid'];

        $res_project=get_student_answers($requestType,$examId,$ucid);	
        echo $res_project;
        break;
    case 'createNewExam':
        $res_project=create_new_exam($response);	
        echo $res_project;
        break;
    case 'editStudentExam':
        $res_project=edit_student_exam($response);	
        echo $res_project;
		break;
	case 'releaseExam':
		//initial setting of variables
		$requestType="releaseExam";
        $examId="";

		if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['examId'])) $examId = $response['examId'];

        $res_project=release_exam($requestType,$examId);	
        echo $res_project;
        break;
    default: 
        break;
}


// curl backend 
function get_exams($requestType,$ucid){
	//data from json response
	$data = array('requestType' => $requestType, 'ucid' => $ucid);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/examTbl.php";
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
function get_exam_questions($requestType,$examId){
	//data from json response
	$data = array('requestType' => $requestType, 'examId' => $examId);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/examTbl.php";
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
function get_exam_statuses($requestType,$examId){
	//data from json response
	$data = array('requestType' => $requestType, 'examId' => $examId);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/examTbl.php";
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
function get_student_answers($requestType,$examId,$ucid){
	//data from json response
	$data = array('requestType' => $requestType, 'examId' => $examId, 'ucid' => $ucid);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/examTbl.php";
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
function create_new_exam($response){
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/examTbl.php";
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

// curl backend 
function edit_student_exam($response){
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/examTbl.php";
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

// curl backend 
function release_exam($requestType,$examId){
	//data from json response
	$data = array('requestType' => $requestType, 'examId' => $examId);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/examTbl.php";
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