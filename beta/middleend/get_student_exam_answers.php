<?php
//grabbing json from backend
$str_json = file_get_contents('php://input'); 
//decoding json into response array
$response = json_decode($str_json, true); 
//initial setting of variables
$requestType='getStudentAnswers';
$examId="";
$studentId="";

if(isset($response['requestType'])) $requestType = $response['requestType'];
if(isset($response['examId'])) $studentId = $response['examId'];
if(isset($response['studentId'])) $studentId = $response['studentId'];

$res_project=get_student_exam_answers($requestType,$examId,$studentId);	
$data = array(
	'backend' => $res_project, 
);
echo json_encode($data);


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
?>
