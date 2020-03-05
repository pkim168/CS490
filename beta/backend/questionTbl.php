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
				$tag = getData()json['tag']);
			}
			echo getQuestions($difficulty, $tag);
			break;
	}
	
	function getQuestions($difficulty, $tag) {
		global $db;
		$data = array();
		if(!empty($difficulty) && !empty($tag)) {
			$difficulty = "WHERE difficulty = '".$difficulty."'";
			$tag = " AND tag = '".$tag."'";
		}
		else if(!empty($difficulty)) {
			$difficulty = "WHERE difficulty = '".$difficulty."'";
		}
		else if(!empty($tag)) {
			$tag = "WHERE tag = '".$tag."'";
		}
		$query = "SELECT * FROM 
			490questionTbl
			JOIN
			490testCaseTbl
			ON 490questionTbl.questionId = 490testCaseTbl.490questionTbl_questionId".$difficulty.$tag."ORDER BY 490questionTbl.questionId";
		
		$count = -1;
		$questionId = "";
		$result = mysqli_query($db, $query);
		if($result->num_rows == 0){
			return json_encode($data);
		}
		while($row = mysqli_fetch_array($result)) {
			temp = array();
			if ($questionId == $row["questionId"]) {
				array_push($data[$count]["testCases"], $row["testCase"]);
			}
			else {
				temp["question"] = $row["question"];
				temp["functionName"] = $row["functionName"];
				temp["difficulty"] = $row["difficultyLvl"];
				temp["tag"] = $row["tag"];
				temp["testCases"] = array($row["testCase"]);
				$count++;
			}
		}
		return json_encode($data);
	}
?>