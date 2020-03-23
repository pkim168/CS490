<?php
	session_start();
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ./index.php');
	} 
	if ($_SESSION['role'] = '1') {
		header('Location: ./studentView.php');
	}
	$url = "https://web.njit.edu/~jrd62/CS490/teacher_middle_questions.php";
	
	$ch = curl_init($url);
	$data = array()
	$data['requestType'] = 'newQuestion';
	$data['question'] = $_POST['question'];
	$data['functionName'] = $_POST['functionName'];
	$data['difficulty'] = $_POST['difficulty'];
	$data['tag'] = $_POST['tag'];
	$data['testCases'] = $_POST['testCases'];
	$payload = json_encode($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo $result;
	
?>