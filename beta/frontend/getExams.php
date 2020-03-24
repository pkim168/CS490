<?php	
	session_start([
        'use_only_cookies' => 1,
        'cookie_lifetime' => 0,
        'cookie_secure' => 1,
        'cookie_httponly' => 1
    ]);
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ./index.php');
	} 
	if ($_SESSION['role'] == '1') {
		header('Location: ./studentView.php');
	}
	
	$ucid = "";
	
	if(!empty($_POST["ucid"])){
		$ucid = $_POST["ucid"];
	}
	
	$url = "https://web.njit.edu/~jrd62/CS490/beta/teacher_middle_exam.php";
	$ch = curl_init($url);
	$data = array();
	$data['requestType'] = 'getExams';
	$data['ucid'] = $ucid;
	$payload = json_encode($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo $result;
	
?>