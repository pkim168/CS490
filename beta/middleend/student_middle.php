<?php
//grabbing json from backend
$str_json = file_get_contents('php://input'); 
//decoding json into response array
$response = json_decode($str_json, true); 

$requestType = $response['requestType'];

//checking request type
switch($requestType) {
    case 'getExamQuestions':
        //initial setting of variables
        $examId="";

        if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['examId'])) $examId = $response['examId'];

        $res_project=get_exam_questions($requestType,$examId);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    case 'getStudentAnswers':
        //initial setting of variables
        $examId="";
        $studentId="";

        if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['examId'])) $examId = $response['examId'];
        if(isset($response['studentId'])) $studentId = $response['studentId'];

        $res_project=get_student_exam_answers($requestType,$examId,$studentId);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    case 'getStudentExams':
        //initial setting of variables
        $studentId="";

        if(isset($response['studentId'])) $studentId = $response['studentId'];

        $res_project=get_student_exams($requestType,$studentId);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    default: 
        break;
}


// curl backend 
function get_exam_questions($requestType,$examId){
	//data from json response
	$data = array('requestType' => $requestType, 'examId' => $examId);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/beta/examTbl.php";
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
function get_student_exam_answers($requestType,$examId,$studentId){
	//data from json response
	$data = array('requestType' => $requestType, 'examId' => $examId, 'studentId' => $studentId);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/beta/examTbl.php";
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
function get_student_exams($requestType,$studentId){
	//data from json response
	$data = array('requestType' => $requestType, 'studentId' => $studentId);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/beta/examTbl.php";
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