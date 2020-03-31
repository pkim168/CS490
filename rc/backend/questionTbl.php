<?php
	include("./functions/dbConnect.php");
	include("./functions/supportFunctions.php");
	$str_json = file_get_contents('php://input');
	$json = json_decode($str_json, true);
	$requestType = getData($json['requestType']);

	switch($requestType) {
		case 'getQuestions':
			$difficulty = "";
			$tag = "";
			if(!empty($json['difficulty'])) {
				$difficulty = getData($json['difficulty']);
			}
			if(!empty($json['tag'])) {
				$tag = getData($json['tag']);
			}
			echo getQuestions($difficulty, $tag);
			break;
		
		case 'getTags':
			echo getTags();
			break;
		
		case 'newQuestion':
			echo newQuestion($json);
			break;
			
	}
	
	function getQuestions($difficulty, $tag) {
		global $db;
		$data = array();
		if(!empty($difficulty) && !empty($tag)) {
			$difficulty = " WHERE difficultyLvl = '".$difficulty."'";
			$tag = " AND tag = '".$tag."'";
		}
		else if(!empty($difficulty)) {
			$difficulty = " WHERE difficultyLvl = '".$difficulty."'";
		}
		else if(!empty($tag)) {
			$tag = " WHERE tag = '".$tag."'";
		}
		$query = "SELECT * FROM 
			490questionTbl
			JOIN
			490testCaseTbl
			ON 490questionTbl.questionId = 490testCaseTbl.490questionTbl_questionId".$difficulty.$tag." ORDER BY 490questionTbl.questionId;";
		
		$count = -1;
		$questionId = "";
		$result = mysqli_query($db, $query);
		if($result->num_rows == 0){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		while($row = mysqli_fetch_array($result)) {
			$temp = array();
			if ($questionId == $row["questionId"]) {
				array_push($data[$count]["testCases"], $row["testCase"]);
			}
			else {
				$temp["questionId"] = $row["questionId"];
				$temp["question"] = $row["question"];
				$temp["functionName"] = $row["functionName"];
				$temp["difficulty"] = $row["difficultyLvl"];
				$temp["tag"] = $row["tag"];
				$temp["testCases"] = array($row["testCase"]);
				$questionId = $row["questionId"];
				array_push($data, $temp);
				$count++;
			}
		}
		return json_encode($data);
	}
	
	function getTags() {
		global $db;
		$data = array();
		$query = "SELECT * FROM 490tagTbl;";
		$result = mysqli_query($db, $query);
		if($result->num_rows == 0){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		while($row = mysqli_fetch_array($result)) {
			array_push($data, $row["tagName"]);
		}
		return json_encode($data);
	}
	
	// Testing Needed
	function newQuestion($json) {
		global $db;
		$data = array();
		$fName = $question = $diff = $tag = "";
		if (!empty($json['functionName'])) {
			$fName = getData($json['functionName']);
		}
		if (!empty($json['question'])) {
			$question = getData($json['question']);
		}
		if (!empty($json['difficulty'])) {
			$diff = getData($json['difficulty']);
		}
		if (!empty($json['tag'])) {
			$tag = getData($json['tag']);
		}
		$query = "INSERT INTO 490questionTbl VALUES (DEFAULT, '$fName', '$question', '$diff', '$tag');";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = "questionTbl".mysqli_error().$fName.$question.$diff.$tag;
			return json_encode($data);
		}
		$questionId = mysqli_insert_id($db);
		
		$query = "";
		$tCases = $json['testCases'];
		foreach ($tCases as $test) {
			$case = $test["case"];
			$tData = json_encode($test["data"]);
			$query .= "INSERT INTO 490testCaseTbl VALUES (DEFAULT, '$questionId', '$case', '$tData'); ";
		}
		unset($test);
		
		if (mysqli_multi_query($db, $query)){
			do {
				$result = mysqli_store_result($db);
				if (!$result) {
					$data["message"] = "Failure";
					$data["error"] = mysqli_error();
					return json_encode($data);
				}
			} while (mysqli_next_result($db));
		} else {
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		/* $query = substr($query, 0, -1).";";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error().$query;
			return json_encode($data);
		} */
		$data["message"] = "Success";
		return json_encode(data);
	}
?>