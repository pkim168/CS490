<?php
	include("./functions/dbConnect.php");
	include("./functions/supportFunctions.php");
	
	$str_json = file_get_contents('php://input');
	$json = json_decode($str_json, true);
/*	preg_match_all('/".*?"/', $json, $matches);
	$jsonArray = array(); 
	for($i=0; $i < count($matches[0]); $i+=2) {
		$jsonArray[getData($matches[0][$i])] = getData($matches[0][$i+1]);
	}
	
	$requestType = $jsonArray[type];
*/
	// Switch statement here, should check based on request type

	$requestType = getData($json['requestType']);

	switch($requestType) {
		case 'login':
			$ucid = getData($json['ucid']);
			$pass = getData($json['pass']);
			echo checkUser($ucid, $pass);
			break;
		
		case 'getUser':
			$ucid = getData($json['ucid']);
			echo getUser($ucid);
			break;
	}
	
	function checkUser($ucid, $pass){		
		if(empty($ucid) || empty($pass)){
			$data[] = "Rejected";
			return json_encode($data);
		}
		
		global $db;
		$query = "
			SELECT * FROM 490userTbl 
			WHERE ucid = '$ucid' AND password = SHA1('$pass');
		";
		$result = mysqli_query($db, $query);
		if($result->num_rows == 0){
			$data[] = "Rejected";
			return json_encode($data);
		}

		$data[] = "Verified";
		return json_encode($data);
	}
	
	function getUser($ucid){
		$data = array();
		
		if(empty($ucid) || empty($pass)){
			return json_encode($data);
		}
		
		global $db;
		$query = "
			SELECT * FROM 490userTbl 
			WHERE ucid = '$ucid';
		";
		
		$result = mysqli_query($db, $query);
		if($result->num_rows == 0){
			return json_encode($data);
		}
		
		$row = mysqli_fetch_array($result);
		$ucid = $row['ucid'];
		$role = $row['role'];
		
		$data['ucid'] = $ucid;
		$data['role'] = $role;
		
		return json_encode($data);
	}
?>