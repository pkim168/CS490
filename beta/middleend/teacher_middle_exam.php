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
        if(isset($response['ucid'])) $examId = $response['ucid'];

        $res_project=get_exams($requestType,$ucid);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    case 'getExamQuestions':
		//initial setting of variables
		$requestType="getExamQuestions";
        $examId="";

		if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['examId'])) $examId = $response['examId'];

        $res_project=get_exam_questions($requestType,$examId);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    case 'getExamStudents':
		//initial setting of variables
		$requestType="getExamStudents";
        $examId="";

		if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['examId'])) $studentId = $response['examId'];

        $res_project=get_exam_students($requestType,$examId);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
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
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    case 'newExam':
		//initial setting of variables
		$requestType="newExam";
        $ucid="";

		if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['ucid'])) $ucid = $response['ucid'];

        $res_project=new_exam($requestType,$ucid);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    case 'editStudentExam':
		//initial setting of variables
		$requestType="editStudentExam";
        $examId="";
        $ucid="";
        $totalPoints="";

		if(isset($response['requestType'])) $requestType = $response['requestType'];
        if(isset($response['examId'])) $examId = $response['examId'];
        if(isset($response['ucid'])) $ucid = $response['ucid'];
        if(isset($response['totalPoints'])) $totalPoints = $response['totalPoints'];

        $res_project=edit_student_exam($requestType,$examId,$ucid,$totalpoints);	
        $data = array(
            'backend' => $res_project, 
        );
        echo json_encode($data);
        break;
    default: 
        break;
}


// curl backend 
function get_exams($requestType,$ucid){
	//data from json response
	$data = array('requestType' => $requestType, 'ucid' => $ucid);
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
function get_exam_students($requestType,$examId){
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
function get_student_answers($requestType,$examId,$ucid){
	//data from json response
	$data = array('requestType' => $requestType, 'examId' => $examId, 'ucid' => $ucid);
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
function new_exam($requestType,$ucid){
	//data from json response
	$data = array('requestType' => $requestType, 'ucid' => $ucid);
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
function edit_student_exam($requestType,$examId,$ucid,$totalpoints){
	//data from json response
	$data = array('requestType' => $requestType, 'examId' => $examId, 'ucid' => $ucid, 'totalpoints' => $totalpoints);
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