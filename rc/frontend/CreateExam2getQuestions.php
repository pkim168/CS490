<?php
	session_start();
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ./index.php');
	} 
	if ($_SESSION['role'] == '1') {
		header('Location: ./studentView.php');
	}
	
	$difficulty = $tag = "";
	
	if(!empty($_POST["difficulty"])){
		$difficulty = $_POST["difficulty"];
	}
	if(!empty($_POST["tag"])){
		$tag = $_POST["tag"];
	}
	$url = "https://web.njit.edu/~jrd62/CS490/beta/teacher_middle_questions.php";
	
	$ch = curl_init($url);
	$data = array();
	$data['requestType'] = 'getQuestions';
	$data['difficulty'] = $difficulty;
	$data['tag'] = $tag;
	$payload = json_encode($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	echo $result;
	
?>