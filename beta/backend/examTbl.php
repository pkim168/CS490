<?php
	include("./functions/dbConnect.php");
	include("./functions/supportFunctions.php");
	$str_json = file_get_contents('php://input');
	$json = json_decode($str_json, true);
	$requestType = getData($json['requestType']);

	switch($requestType) {
		case 'getStudentAnswers':
			$ucid = $examId = '';
			if (!empty($json['ucid'])) {
				$ucid = getData($json['ucid']);
			}
			if (!empty($json['examId'])) {
				$examId = getData($json['examId']);
			}
			echo getStudentAnswers($ucid, $examId);
			break;
		
		case 'getStudentExams':
			$ucid = '';
			if (!empty($json['ucid'])) {
				$ucid = getData($json['ucid']);
			}
			echo getStudentExams($ucid);
			break;
			
		case 'getExamQuestions':
			$examId = '';
			if (!empty($json['examId'])) {
				$examId = getData($json['examId']);
			}
			echo getExamQuestions($examId);
			break;
			
		case 'submitStudentExam':
			echo submitStudentExam($json);
			break;
			
		case 'createNewExam':
			echo createNewExam($json);
			break;
		
		case 'getExams':
			$ucid = getData($json['ucid']);
			echo getExams($ucid);
			break;
			
		case 'getExamStatuses':
			$examId = '';
			if (!empty($json['examId'])) {
				$examId = getData($json['examId']);
			}
			echo getExamStatuses($examId);
			break;
			
		case 'editStudentExam':
			echo editStudentExam($json);
			break;
			
		case 'releaseExam':
			$examId = '';
			if (!empty($examId)) {
				$examId = getData($json['examId']);
			}
			echo releaseExam($examId);
			break;
		
		default:
			$data = array();
			$data["message"] = "Invalid Request Type";
			echo json_encode($data);
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
		if ($result->num_rows == 0){
			$data["message"] = "Error".mysqli_error();
			return json_encode($data);
		}
		while ($row = mysqli_fetch_array($result)) {
			$temp = array();
			$test = array();
			if ($questionId == $row["questionId"]) {
				$test["case"] = $row["testCase"];
				$test["data"] = $row["testData"];
				array_push($data[$count]["testCases"], $test);
			}
			else {
				$temp["questionId"] = $row["questionId"];
				$temp["question"] = $row["question"];
				$temp["functionName"] = $row["functionName"];
				$temp["difficulty"] = $row["difficultyLvl"];
				$temp["tag"] = $row["tag"];
				$test["case"] = $row["testCase"];
				$test["data"] = $row["testData"];
				$temp["testCases"] = array($test);
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
		if ($result->num_rows == 0){
			$data["message"] = "Error".mysqli_error();
			return json_encode($data);
		}
		while ($row = mysqli_fetch_array($result)) {
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
				490examQuestionTbl
				WHERE 490examTbl_examId = '$examId'
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
		if ($result->num_rows == 0){
			$data["message"] = "Error".mysqli_error();
			return json_encode($data);
		}
		while ($row = mysqli_fetch_array($result)) {
			$temp = array();
			$test = array();
			if ($questionId == $row["questionId"]) {
				$test["case"] = $row["testCase"];
				$test["data"] = $row["testData"];
				array_push($data[$count]["testCases"], $test);
			}
			else {
				$temp["questionId"] = $row["questionId"];
				$temp["question"] = $row["question"];
				$temp["functionName"] = $row["functionName"];
				$temp["difficulty"] = $row["difficultyLvl"];
				$temp["tag"] = $row["tag"];
				$test["case"] = $row["testCase"];
				$test["data"] = $row["testData"];
				$temp["testCases"] = array($test);
				$temp["totalPoints"] = $row["totalPoints"];
				$questionId = $row["questionId"];
				array_push($data, $temp);
				$count++;
			}
		}
		return json_encode($data);
		
	}
	
	//Testing Needed
	function createNewExam($json) {
		global $db;
		global $requestType;
		$data = array();
		$ucid = '';
		$totalPoints = '0';
		if (!empty($json['ucid'])) {
			$ucid = getData($json['ucid']);
		}
		if (!empty($json['totalPoints'])) {
			$totalPoints = getData($json['totalPoints']);
		}
		$query = "INSERT INTO 490examTbl VALUES (DEFAULT, '$ucid', '$totalPoints');";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = "examTbl insert ".mysqli_error();
			return json_encode($data);
		}
		$examId = mysqli_insert_id($db);
		
		// If previous line doesn't work, uncomment this block
		/* $query = "
			SELECT DISTINCT 490examTbl.examId
			FROM 490examTbl
			LEFT JOIN
			490studentExamTbl
			ON 490examTbl.examId = 490studentExamTbl.490examTbl_examId
			WHERE 490studentExamTbl.490examTbl_examId IS NULL;
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Error".mysqli_error();
			return json_encode($data);
		}
		$row = mysqli_fetch_array($result);
		$examId = $row["examId"]; */
		
		$query = "
			SELECT DISTINCT studentId
			FROM 490classTbl
			WHERE teacherId = '$ucid';
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Failure";
			$data["error"] = $requestType."students get ".mysqli_error();
			return json_encode($data);
		}
		
		$students = array();
		while ($row = mysqli_fetch_array($result)) {
			array_push($students, $row["studentId"]);
		}
		
		$query = "INSERT INTO 490studentExamTbl VALUES ";
		foreach ($students as $student) {
			$query .= "(DEFAULT, '$student', '$examId', '0'),";
		}
		unset($student);
		$query = substr($query, 0, -1).";";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = "student exam insert ".mysqli_error();
			return json_encode($data);
		}
		
		$query = "INSERT INTO 490examQuestionTbl VALUES ";
		$questions = $json["questions"];
		for ($i = 0; $i < count($questions); $i++) {
			$questionId = $questions[$i]["questionId"];
			$points = $questions[$i]["points"];
			$query .= "('$examId', '$questionId', '$points'),";
		}
		$query = substr($query, 0, -1).";";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = "exam question insert ".mysqli_error();
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
		if (!empty($json['ucid'])) {
			$ucid = getData($json['ucid']);
		}
		if (!empty($json['examId'])) {
			$ucid = getData($json['examId']);
		}
		$query = "
			UPDATE 490studentExamTbl
			SET status = '1'
			WHERE 490userTbl_ucid = '$ucid' AND 490examTbl_examId = '$examId';
		";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		
		$query = "
			SELECT sExamId
			FROM 490studentExamTbl
			WHERE 490userTbl_ucid = '$ucid' AND 490examTbl_examId = '$examId';
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Error".mysqli_error();
			return json_encode($data);
		}
		$row = mysqli_fetch_array($result);
		$sExamId = $row["sExamId"];
		
		$query = "INSERT INTO 490examGradesTbl VALUES ";
		$questions = $json["questions"];
		for ($i = 0; $i < count($questions); $i++) {
			$questionId = $questions[$i]["questionId"];
			$answer = $questions[$i]["answer"];
			$pointsEarned = $questions[$i]["pointsEarned"];
			$totalPoints = $questions[$i]["totalPoints"];
			$query .= "('$sExamId', '$questionId', '$pointsEarned', '$totalPoints', '$answer', NULL),";
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
	
	function getExams($ucid) {
		global $db;
		$data = array();
		$query = "
			SELECT * FROM 490examTbl
			WHERE teacherId = '$ucid';
		";
		
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Failure";
			$data["error"] = $ucid."getExams".mysqli_error();
			return json_encode($data);
		}
		while ($row = mysqli_fetch_array($result)) {
			array_push($data, $row["examId"]);
		}
		return json_encode($data);
	}
	
	function getExamStatuses($examId) {
		global $db;
		$data = array();
		$query = "
			SELECT 490userTbl_ucid, status FROM 490studentExamTbl
			WHERE 490examTbl_examId = '$examId';
		";
		
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		while ($row = mysqli_fetch_array($result)) {
			$temp = array();
			$temp["ucid"] = $row["490userTbl_ucid"];
			$temp["status"] = $row["status"];
			array_push($data, $temp);
		}
		return json_encode($data);
	}
	
	// Testing Needed
	function editStudentExam($json) {
		global $db;
		$data = array();
		$ucid = $examId = "";
		if (!empty($json['ucid'])) {
			$ucid = getData($json['ucid']);
		}
		if (!empty($json['examId'])) {
			$ucid = getData($json['examId']);
		}
		$query = "
			SELECT sExamId FROM 490studentExamTbl
			WHERE 490userTbl_ucid = '$ucid' AND 490examTbl_examId = '$examId';
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		$row = mysqli_fetch_array($result);
		$sExamId = $row["sExamId"];
		$query = "";
		$questions = $json["questions"];
		for ($i = 0; $i < count($questions); $i++) {
			$questionId = $questions[$i]["questionId"];
			$pointsEarned = $questions[$i]["points"];
			$comments = $questions[$i]["comments"];
			$query .= "
				UPDATE 490examGradesTbl 
				SET 
					pointsEarned = '$pointsEarned', 
					comments = '$comments'
				WHERE 490studentExamTbl_sExamId = '$sExamId' AND 490questionTbl_questionId = '$questionId';
			";
		}
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
		
		$data["message"] = "Success";
		return json_encode($data);
	}
	
	// Testing Needed
	function releaseExam($examId) {
		global $db;
		$data = array();
		$query = "
			UPDATE 490studentExamTbl 
			SET status = '2'
			WHERE 490examTbl_examId = '$examId';
		";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = mysqli_error();
			return json_encode($data);
		}
		$data["message"] = "Success";
		$data["error"] = "None".query;
		return json_encode($data);
	}
?>