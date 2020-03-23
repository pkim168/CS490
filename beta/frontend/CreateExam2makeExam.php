<?php
	session_start();
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ./index.php');
	} 
	if ($_SESSION['role'] = '1') {
		header('Location: ./studentView.php');
	}
	
	$ucid = $examId = "";
	if(empty($_POST["ucid"])){
		header('Location: ./index.php');
	}
	if(empty($_POST["examId"])){
		header('Location: ./index.php');
	}
	$ucid = $_POST["ucid"];
	$examId = $_POST["examId"];
	$questions = $_POST["questions"];
	
	$url = "https://web.njit.edu/~jrd62/CS490/teacher_middle_exam.php";
	
	$ch = curl_init($url);
	$data = array()
	$data['requestType'] = 'submitStudentExam';
	$data['ucid'] = $ucid;
	$data['examId'] = $examId;
	$data['questions'] = $questions;
	$payload = json_encode($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo $result;
	
?>