<?php
	include("./dbConnect.php");
	include("./supportFunctions.php");
	
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
	$requestType = $json->{'requestType'};
	
	switch($requestType) {
		case 'login':
			$ucid = getData($json['ucid']);
			$pass = getData($json['pass']);
			echo check_user($ucid, $pass);
			break;
		
		case 'getUser':
			$ucid = getData($json['ucid']);
			echo getUser($ucid);
			break;
	}
	
	function check_user($ucid, $pass){
		$data = array();
		
		if(empty($ucid) || empty($pass)){
			echo "<script>console.log('Error. ucid or password is empty')</script>";
			$data[] = 'Rejected';
			return json_encode($data);
		}
		
		global $db;
		$query = "
			SELECT * FROM 490userTbl 
			WHERE ucid = '$ucid' AND password = SHA1('$pass');
		";
		
		if(mysqli_query($db, $query)){
			echo "<script>console.log('Verified')</script>";
			$data[] = 'Verified';
			return json_encode($data);
		}
		echo "<script>console.log('Not verified')</script>";
		$data[] = 'Rejected';
		return json_encode($data);
	}
	
	function getUser($ucid){
		$data = array();
		
		if(empty($ucid) || empty($pass)){
			echo "<script>console.log('Error. ucid or password is empty')</script>";
			return json_encode($data);
		}
		
		global $db;
		$query = "
			SELECT * FROM 490userTbl 
			WHERE ucid = '$ucid';
		";
		
		$result = mysqli_query($db, $query);
		if($result->num_rows == 0){
			echo "<script> console.log('User does not exist') </script>";
			return json_encode($data);
		}

		$row = mysqli_fetch_array($result)
			$ucid = $row['ucid'];
			$role = $row['role'];
		)
		array_push($data, $ucid, $role);
		
		return json_encode($data);
	}
?>