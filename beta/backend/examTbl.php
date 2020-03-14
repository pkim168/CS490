<?php
	include("./functions/dbConnect.php");
	include("./functions/supportFunctions.php");
	$str_json = file_get_contents('php://input');
	$json = json_decode($str_json, true);
	$requestType = getData($json['requestType']);

	switch($requestType) {
		case 'getStudentAnswers':
			if(!empty($json['ucid'])) {
				$ucid = getData($json['ucid']);
			}
			if(!empty($json['examId'])) {
				$examId = getData($json['examId']);
			}
			echo getStudentAnswers($ucid, $examId);
			break;
		
		case 'getStudentExams':
			if (!empty($json['ucid'])) {
				$ucid = getData($json['ucid']);
			}
			echo getStudentExams($ucid);
			break;
			
		case 'getExamQuestions':
			if(!empty($json['examId'])) {
				$examId = getData($json['examId']);
			}
			echo getExamQuestions($examId);
			break;
			
		case 'submitStudentExam':
			if(!empty($json)) {
				$json = getData($json['examId']);
			}
			echo submitStudentExam($json);
			break;
			
		case 'submitNewExam':
			if(!empty($json)) {
				$json = getData($json['examId']);
			}
			echo submitNewExam($json);
			break;
		
	}
	
	function getStudentAnswers($ucid, $examId) {
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
				WHERE 490studentExamTbl.490userTbl_ucid = '$ucid' AND 490studentExamTbl.490examTbl_examId = '$examId'
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
			$data["message"] = "Error".mysqli_error();
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
				$temp["answer"] = $row["answer"];
				$temp["comments"] = $row["comments"];
				$temp["pointsEarned"] = $row["pointsEarned"];
				$temp["totalPoints"] = $row["totalPoints"];
				$questionId = $row["questionId"];
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
			WHERE 490userTbl_ucid = '$ucid';
		";
		
		$result = mysqli_query($db, $query);
		if($result->num_rows == 0){
			$data["message"] = "Error".mysqli_error();
			return json_encode($data);
		}
		while($row = mysqli_fetch_array($result)) {
			$temp = array();
			$temp["examId"] = $row["490examTbl_examId"];
			$temp["studentExamId"] = $row["sExamId"];
			$temp["status"] = $row["status"];
			array_push($data, $temp);
		}
		return json_encode($data);
	}
	
	function getExamQuestions($examId) {
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
				WHERE 490studentExamTbl.490userTbl_ucid = '$ucid' AND 490studentExamTbl.490examTbl_examId = '$examId'
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
			$data["message"] = "Error".mysqli_error();
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
				$temp["totalPoints"] = $row["totalPoints"];
				$questionId = $row["questionId"];
				array_push($data, $temp);
				$count++;
			}
		}
		return json_encode($data);
		
	}
	
	//Testing Needed
	function submitNewExam($json) {
		global $db;
		$data = array();
		$ucid = '';
		$totalPoints = '0';
		if(!empty($json['ucid'])) {
			$ucid = getData($json['ucid']);
		}
		if(!empty($json['totalPoints'])) {
			$ucid = getData($json['totalPoints']);
		}
		$query = "INSERT INTO 490examTbl VALUES (DEFAULT, '$ucid', '$totalPoints');";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		
		$query = "
			SELECT DISTINCT 490examTbl.examId
			FROM 490examTbl
			LEFT JOIN
			490studentExamTbl
			ON 490examTbl.examId = 490studentExamTbl.490examTbl_examId
			WHERE 490studentExamTbl.490examTbl_examId IS NULL;
		";
		$result = mysqli_query($db, $query);
		$row = mysqli_fetch_array($result);
		$examId = $row["examId"];
		
		$query = "
			SELECT DISTINCT studentId
			FROM 490classTbl
			WHERE teacherId = '$ucid';
		";
		$result = mysqli_query($db, $query);
		while($row = mysqli_fetch_array($result)) {
			$students = array();
			array_push($students, $row["studentId"]);
		}
		
		$query = "INSERT INTO 490studentExamTbl VALUES "
		foreach($students as &$student) {
			$query .= "(DEFAULT, '$student', '$examId', '0'),"
		}
		unset($student);
		$query = substr($query, 0, -1).";";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		
		$query = "INSERT INTO 490examQuestionTbl VALUES "
		$question = $json["questions"];
		for($i = 0; $i < count($questions); $i++) {
			$questionId = $questions[$i]["questionId"];
			$points = $questions[$i]["points"];
			$query .= "($examId, '$questionId'', '$points'),"
		}
		$query = substr($query, 0, -1).";";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		
		$data["message"] = "Success";
		return json_encode($data);
	}
	
	//Testing Needed
	function submitStudentExam($json) {
		global $db;
		$data = array();
		$ucid = $examId = "";
		if(!empty($json['ucid'])) {
			$ucid = getData($json['ucid']);
		}
		if(!empty($json['examId'])) {
			$ucid = getData($json['examId']);
		}
		$query = "
			UPDATE 490studentExamTbl
			SET status = '1'
			WHERE 490userTbl_ucid = '$ucid' AND 490examTbl_examId = '$examId;
		";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		
		$query = "
			SELECT sExamId
			FROM 490studentExamTbl
			WHERE 490userTbl_ucid = '$ucid' AND 490examTbl_examId = '$examId;
		";
		$result = mysqli_query($db, $query);
		$row = mysqli_fetch_array($result);
		$sExamId = $row["sExamId"];
		
		$query = "INSERT INTO 490examGradesTbl VALUES "
		$question = $json["questions"];
		for($i = 0; $i < count($questions); $i++) {
			$questionId = $questions[$i]["questionId"];
			$answer = $questions[$i]["answer"];
			$pointsEarned = $questions[$i]["pointsEarned"];
			$totalPoints = $questions[$i]["totalPoints"];
			$query .= "($sExamId, '$questionId'', '$pointsEarned', '$totalPoints', '$answer', NULL),"
		}
		$query = substr($query, 0, -1).";";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		
		$data["message"] = "Success";
		return json_encode($data);
	}
?>