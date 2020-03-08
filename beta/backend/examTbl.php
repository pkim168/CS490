<?php
	include("./functions/dbConnect.php");
	include("./functions/supportFunctions.php");
	$str_json = file_get_contents('php://input');
	$json = json_decode($str_json, true);
	$requestType = getData($json['requestType']);

	switch($requestType) {
		case 'getStudentAnswers':
			if(!empty($json['examId'])) {
				$examId = getData($json['examId']);
			}
			if(!empty($json['ucid'])) {
				$ucid = getData($json['ucid']);
			}
			echo getStudentAnswers($examId, $ucid);
			break;
		
		case 'getStudentExams':
			if (!empty($json['ucid'])) {
				$ucid = getData($json['ucid']);
			}
			echo getStudentExams($ucid);
			break;
			
	}
	
	function getStudentAnswers($examId, $ucid) {
		global $db;
		$data = array();
		$query = "
			SELECT * FROM 
			(
				SELECT * FROM
				490studentExamTbl
				JOIN
				490examGradesTbl
				ON 490studentExamTbl.sExamId = 490examGradesTbl.490studentExamTbl_sExamId
				WHERE 490studentExamTbl.490userTbl_ucid = $ucid AND 490studentExamTbl.490examTbl_examId = $examId
			) as A
			JOIN
			(
				SELECT * FROM 
				490questionTbl
				JOIN
				490testCaseTbl
				ON 490questionTbl.questionId = 490testCaseTbl.490questionTbl_questionId
			) as B
			ON A.490questionTbl_questionId = B.questionId
			ORDER BY B.questionId;
		";
		
		$count = -1;
		$questionId = "";
		$result = mysqli_query($db, $query);
		if($result->num_rows == 0){
			$data["message"] = "Error";
			return json_encode($data);
		}
		while($row = mysqli_fetch_array($result)) {
			temp = array();
			if ($questionId == $row["questionId"]) {
				array_push($data[$count]["testCases"], $row["testCase"]);
			}
			else {
				temp["status"] = $row["status"];
				temp["questionId"] = $row["questionId"];
				temp["question"] = $row["question"];
				temp["functionName"] = $row["functionName"];
				temp["difficulty"] = $row["difficultyLvl"];
				temp["tag"] = $row["tag"];
				temp["testCases"] = array($row["testCase"]);
				temp["answer"] = $row["answer"];
				temp["comments"] = $row["comments"];
				temp["pointsEarned"] = $row["pointsEarned"];
				temp["totalqPoints"] = $row["totalPoints"];
				array_push($data, $temp);
				$count++;
			}
		}
		return json_encode($data);
	}
	
	function getStudentExams($ucid) {
		global $db;
		$data = array();
		$query = "
			SELECT * FROM 490studentExamTbl
			WHERE 490userTbl_ucid = $ucid;
		";
		
		$result = mysqli_query($db, $query);
		if($result->num_rows == 0){
			$data["message"] = "Error";
			return json_encode($data);
		}
		while($row = mysqli_fetch_array($result)) {
			$temp = array();
			
	}
?>