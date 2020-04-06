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
			$constraints = "";
			$keyword = "";
			if(!empty($json['difficulty'])) {
				$difficulty = getData($json['difficulty']);
			}
			if(!empty($json['tag'])) {
				$tag = getData($json['tag']);
			}
			if(!empty($json['constraints'])) {
				$constraints = getData($json['constraints']);
			}
			if(!empty($json['keyword'])) {
				$keyword = getData($json['keyword']);
			}
			echo getQuestions($difficulty, $tag, $constraints, $keyword);
			break;
		
		case 'getTags':
			echo getTags();
			break;
		
		case 'newQuestion':
			echo newQuestion($json);
			break;
			
	}
	
	function getQuestions($difficulty, $tag, $constraints, $keyword) {
		global $db;
		$data = array();
		$flag = 0;
		if (!empty($difficulty)) {
			$difficulty = " WHERE difficultyLvl = '".$difficulty."'";
			$flag++;
		}
		if (!empty($tag)) {
			if ($flag == 0) {
				$tag = " WHERE tag = '".$tag."'";
				$flag++;
			}
			else {
				$tag = " AND tag = '".$tag."'";
			}
		}
		if (!empty($constraints)) {
			if ($flag == 0) {
				$constraints = " WHERE constraints = '".$constraints."'";
				$flag++;
			}
			else {
				$constraints = " AND constraints = '".$constraints."'";
			}
		}
		$query = "SELECT * FROM 
			490questionTbl
			JOIN
			490testCaseTbl
			ON 490questionTbl.questionId = 490testCaseTbl.490questionTbl_questionId".$difficulty.$tag.$constraints." ORDER BY 490questionTbl.questionId;";
		
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
				if (!empty($keyword)) {
					if (preg_match("/".$keyword."/", $row["question"])) {
						$temp["questionId"] = $row["questionId"];
						$temp["question"] = $row["question"];
						$temp["functionName"] = $row["functionName"];
						$temp["difficulty"] = $row["difficultyLvl"];
						$temp["tag"] = $row["tag"];
						$temp["constraints"] = $row["constraints"];
						$temp["testCases"] = array($row["testCase"]);
						$temp["state"] = $constraints;
						$questionId = $row["questionId"];
						array_push($data, $temp);
						$count++;
					}
				}
				else {
					$temp["questionId"] = $row["questionId"];
					$temp["question"] = $row["question"];
					$temp["functionName"] = $row["functionName"];
					$temp["difficulty"] = $row["difficultyLvl"];
					$temp["tag"] = $row["tag"];
					$temp["constraints"] = $row["constraints"];
					$temp["testCases"] = array($row["testCase"]);
					$temp["state"] = $constraints;
					$questionId = $row["questionId"];
					array_push($data, $temp);
					$count++;
				}
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
		if (!empty($json['constraints'])) {
			$constraints = getData($json['constraints']);
		}
		$query = "INSERT INTO 490questionTbl VALUES (DEFAULT, '$fName', '$question', '$diff', '$tag', '$constraints');";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = "questionTbl".mysqli_error().$fName.$question.$diff.$tag.$constraints;
			return json_encode($data);
		}
		$questionId = mysqli_insert_id($db);
		
		$query = "";
		$tCases = $json['testCases'];
		foreach ($tCases as $test) {
			$tData = json_encode($test["data"]);
			$query .= "INSERT INTO 490testCaseTbl VALUES (DEFAULT, '$questionId', '$tData'); ";
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