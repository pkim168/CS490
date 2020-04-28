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
			if (!empty($json['examId'])) {
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
				(
					SELECT * FROM
					490examGradesTbl
					JOIN
					490questionTbl
					ON 490examGradesTbl.490questionTbl_questionId = 490questionTbl.questionId
				) as C
				ON 490studentExamTbl.sExamId = C.490studentExamTbl_sExamId
				WHERE 490studentExamTbl.490userTbl_ucid = '$ucid' AND 490studentExamTbl.490examTbl_examId = '$examId'
			) as A
			JOIN
			(
				SELECT 490itemTbl.itemId, 490itemTbl.490examGradesTbl_examqId, 490itemTbl.subitem, 490itemTbl.pointsEarned, 490itemTbl.totalSubPoints, 490itemTbl.testCaseId, 490testCaseTbl.490questionTbl_questionId, 490testCaseTbl.testData FROM 
				490itemTbl
				LEFT JOIN
				490testCaseTbl
				ON 490itemTbl.testCaseId = 490testCaseTbl.testCaseId
			) as B
			ON A.examqId = 490examGradesTbl_examqId
			ORDER BY A.490questionTbl_questionId ASC, B.itemId ASC;
		";
		
		$count = -1;
		$examqId = "";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Error".mysqli_error();
			return json_encode($data);
		}
		while ($row = mysqli_fetch_array($result)) {
			$temp = array();
			$test = array();
			if ($examqId == $row["examqId"]) {
				switch ($row["subitem"]) {
					case "function":
						$data[$count]["function"]["itemId"] = $row["itemId"];
						$data[$count]["function"]["functionName"] = $row["functionName"];
						$data[$count]["function"]["pointsEarned"] = $row["pointsEarned"];
						$data[$count]["function"]["totalSubPoints"] = $row["totalSubPoints"];
						break;
					
					case "colon":
						$data[$count]["colon"]["itemId"] = $row["itemId"];
						$data[$count]["colon"]["pointsEarned"] = $row["pointsEarned"];
						$data[$count]["colon"]["totalSubPoints"] = $row["totalSubPoints"];
						break;
					
					case "constraints":
						$data[$count]["constraints"]["itemId"] = $row["itemId"];
						$data[$count]["constraints"]["constraint"] = $row["constraints"];
						$data[$count]["constraints"]["pointsEarned"] = $row["pointsEarned"];
						$data[$count]["constraints"]["totalSubPoints"] = $row["totalSubPoints"];
						break;
					
					default:
						$test["itemId"] = $row["itemId"];
						$test["data"] = $row["testData"];
						$test["pointsEarned"] = $row["pointsEarned"];
						$test["totalSubPoints"] = $row["totalSubPoints"];
						array_push($data[$count]["testCases"], $test);
						break;
				}
			}
			else {
				$temp["questionId"] = $row["questionId"];
				$temp["question"] = $row["question"];
				$temp["function"] = array();
				$temp["colon"] = array();
				$temp["constraints"] = array();
				$temp["testCases"] = array();
				$temp["answer"] = $row["answer"];
				$temp["comments"] = $row["comments"];
				$temp["feedback"] = $row["feedback"];
				$temp["totalPoints"] = $row["totalPoints"];
				switch ($row["subitem"]) {
					case "function":
						$temp["function"]["itemId"] = $row["itemId"];
						$temp["function"]["pointsEarned"] = $row["pointsEarned"];
						$temp["function"]["totalSubPoints"] = $row["totalSubPoints"];
						break;
					
					case "colon":
						$temp["colon"]["itemId"] = $row["itemId"];
						$temp["colon"]["pointsEarned"] = $row["pointsEarned"];
						$temp["colon"]["totalSubPoints"] = $row["totalSubPoints"];
						break;
					
					case "constraints":
						$temp["constraints"]["itemId"] = $row["itemId"];
						$temp["constraints"]["pointsEarned"] = $row["pointsEarned"];
						$temp["constraints"]["totalSubPoints"] = $row["totalSubPoints"];
						break;
					
					default:
						$test["itemId"] = $row["itemId"];
						$test["data"] = $row["testData"];
						$test["pointsEarned"] = $row["pointsEarned"];
						$test["totalSubPoints"] = $row["totalSubPoints"];
						array_push($temp["testCases"], $test);
						break;
				}
				$examqId = $row["examqId"];
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
				$test["data"] = $row["testData"];
				$test["testCaseId"] = $row["testCaseId"];
				array_push($data[$count]["testCases"], $test);
			}
			else {
				$temp["questionId"] = $row["questionId"];
				$temp["question"] = $row["question"];
				$temp["functionName"] = $row["functionName"];
				$temp["constraints"] = $row["constraints"];
				$temp["difficulty"] = $row["difficultyLvl"];
				$temp["tag"] = $row["tag"];
				$test["testCaseId"] = $row["testCaseId"];
				$test["data"] = $row["testData"];
				$temp["testCases"] = array($test);
				$temp["totalPoints"] = $row["points"];
				$questionId = $row["questionId"];
				array_push($data, $temp);
				$count++;
			}
		}
		return json_encode($data);
		
	}
	
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
	
	function submitStudentExam($json) {
		global $db;
		$data = array();
		$ucid = $examId = "";
		if (!empty($json['ucid'])) {
			$ucid = getData($json['ucid']);
		}
		if (!empty($json['examId'])) {
			$examId = getData($json['examId']);
		}
		$query = "
			UPDATE 490studentExamTbl
			SET status = '1'
			WHERE 490userTbl_ucid = '$ucid' AND 490examTbl_examId = '$examId';
		";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = "update status".mysqli_error();
			return json_encode($data);
		}
		
		$query = "
			SELECT sExamId
			FROM 490studentExamTbl
			WHERE 490userTbl_ucid = '$ucid' AND 490examTbl_examId = '$examId';
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Failure";
			$data['error'] = "Error select".$ucid.$examId.mysqli_error();
			return json_encode($data);
		}
		$row = mysqli_fetch_array($result);
		$sExamId = $row["sExamId"];
		
		$query = "INSERT INTO 490examGradesTbl VALUES ";
		$questions = $json["questions"];
		for ($i = 0; $i < count($questions); $i++) {
			$questionId = $questions[$i]["questionId"];
			$answer = getData($questions[$i]["answer"]);
			$totalPoints = $questions[$i]["totalPoints"];
			$comments = getData($questions[$i]["comments"]);
			$query .= "(DEFAULT, '$sExamId', '$questionId', '$totalPoints', '$answer', '$comments', NULL),";
		}
		$query = substr($query, 0, -1).";";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = $query."insert eG".mysqli_error();
			return json_encode($data);
		}
		
		$query = "
			SELECT DISTINCT examqId
			FROM 490examGradesTbl
			WHERE 490studentExamTbl_sExamId = '$sExamId';
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Failure";
			$data['error'] = "Error select".$ucid.$examId.mysqli_error();
			return json_encode($data);
		}
		
		$count = 0;
		$query = "INSERT INTO 490itemTbl VALUES ";
		while ($row = mysqli_fetch_array($result)) {
			$examqId = $row["examqId"];
			$fPoints = $questions[$count]["function"]["pointsEarned"];
			$fTolPoints = $questions[$count]["function"]["totalSubPoints"];
			$colPoints = $questions[$count]["colon"]["pointsEarned"];
			$colTolPoints = $questions[$count]["colon"]["totalSubPoints"];
			$conPoints = $questions[$count]["constraints"]["pointsEarned"];
			$conTolPoints = $questions[$count]["constraints"]["totalSubPoints"];
			$testCases = $questions[$count]["testCases"];
			$query .= "(DEFAULT, '$examqId', 'function', '$fPoints', '$fTolPoints', NULL),
				(DEFAULT, '$examqId', 'colon', '$colPoints', '$colTolPoints', NULL),
				(DEFAULT, '$examqId', 'constraints', '$conPoints', '$conTolPoints', NULL),";
			for ($j=0; $j < count($testCases); $j++) {
				$testCaseId = $testCases[$j]["testCaseId"];
				$pointsEarned = $testCases[$j]["pointsEarned"];
				$totalSubPoints = $testCases[$j]["totalSubPoints"];
				$query .= "(DEFAULT, '$examqId', 'testCase$j', '$pointsEarned', '$totalSubPoints', '$testCaseId'),";
			}
			$count++;
		}
		$query = substr($query, 0, -1).";";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = $query."insert item".mysqli_error().count($testCases).$count;
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
	
	function editStudentExam($json) {
		global $db;
		$data = array();
		$data['error'] = 'h';
		$ucid = $examId = "";
		if (!empty($json['ucid'])) {
			$ucid = getData($json['ucid']);
		}
		if (!empty($json['examId'])) {
			$examId = getData($json['examId']);
		}
		$query = "
			SELECT sExamId FROM 490studentExamTbl
			WHERE 490userTbl_ucid = '$ucid' AND 490examTbl_examId = '$examId';
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Failure";
			$data["error"] = "select sExam".$ucid.mysqli_error().$examId;
			return json_encode($data);
		}
		$row = mysqli_fetch_array($result);
		$sExamId = $row["sExamId"];
		$query = "";
		$query1 = "";
		$questions = $json["questions"];
		$data['error'] .= count($questions).'..';
		for ($i = 0; $i < count($questions); $i++) {
			$questionId = getData($questions[$i]["questionId"]);
			$function = getData($questions[$i]["function"]["itemId"]);
			$fPoints = getData($questions[$i]["function"]["pointsEarned"]);
			$colon = getData($questions[$i]["colon"]["itemId"]);
			$colPoints = getData($questions[$i]["colon"]["pointsEarned"]);
			$constraint = getData($questions[$i]["constraints"]["itemId"]);
			$conPoints = getData($questions[$i]["constraints"]["pointsEarned"]);
			$testCases = $questions[$i]["testCases"];
			$feedback = getData($questions[$i]["feedback"]);
			$data['error'] .= $fPoints.' '.$colPoints.' '.$conPoints.' '.'.';
			$query .= "
				UPDATE 490examGradesTbl 
				SET 
					feedback = '$feedback'
				WHERE 490studentExamTbl_sExamId = '$sExamId' AND 490questionTbl_questionId = '$questionId';
			
				UPDATE 490itemTbl 
				SET 
					pointsEarned = '$fPoints'
				WHERE itemId = '$function';
				
				UPDATE 490itemTbl 
				SET 
					pointsEarned = '$colPoints'
				WHERE itemId = '$colon';
				
				UPDATE 490itemTbl 
				SET 
					pointsEarned = '$conPoints'
				WHERE itemId = '$constraint';
			";
			
			for ($j=0; $j < count($testCases); $j++) {
				$itemId = getData($testCases[$j]["itemId"]);
				$pointsEarned = getData($testCases[$j]["pointsEarned"]);
				$query1 .= "
					UPDATE 490itemTbl 
					SET 
						pointsEarned = '$pointsEarned'
					WHERE itemId = '$itemId';
				";
			}
		}
		if (mysqli_multi_query($db, $query)){
			do {
				$result = mysqli_store_result($db);
			} while (mysqli_next_result($db));
		} else {
			$data["message"] = "Failure";
			$data["error"] = 'hi'.mysqli_error($db);
			return json_encode($data);
		}
		if (mysqli_multi_query($db, $query1)){
			do {
				$result = mysqli_store_result($db);
			} while (mysqli_next_result($db));
		} else {
			$data["message"] = "Failure";
			$data["error"] = 'hi'.mysqli_error($db);
			return json_encode($data);
		}
		
		$data["message"] = "Success";
		$data["error"] = 'hi'.$query;
		return json_encode($data);
	}
	
	function releaseExam($examId) {
		global $db;
		$data = array();
		$query = "
			SELECT 490userTbl_ucid
			FROM
			490studentExamTbl
			WHERE 490examTbl_examId = '$examId' and status = '0';
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows != 0){
			$ucids = array();
			while ($row = mysqli_fetch_array($result)) {
				array_push($ucids, $row["490userTbl_ucid"]);
			}
			
			$query = "
				SELECT 490questionTbl_questionId, points
				FROM
				490examQuestionTbl
				WHERE 490examTbl_examId = '$examId';
			";
			$result = mysqli_query($db, $query);
			if ($result->num_rows == 0){
				$data["message"] = "Failure";
				$data["error"] = "select quest".mysqli_error();
				return json_encode($data);
			}
			$questions = array();
			while ($row = mysqli_fetch_array($result)) {
				$temp = array();
				$temp['questionId'] = $row['490questionTbl_questionId'];
				$temp['answer'] = "";
				$temp['totalPoints'] = $row['points'];
				array_push($questions, $temp);
			}
			$data['requestType'] = 'submitStudentExam';
			$data['examId'] = $examId;
			$data['questions'] = $questions;
			for ($i=0; $i < count($ucids); $i++) {
				$id = $ucids[$i];
				$data['ucid'] = $id;
				$url = "https://web.njit.edu/~jrd62/CS490/rv/grading.php";
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				$response_questions = curl_exec($ch);
				curl_close ($ch);
				$response_decode = json_decode($response_questions, true);
				if ($response_decode['message'] == "Failure") {
					echo $json_encode($response_decode);
					return;
				}
			}
		}
		$query = "
			UPDATE 490studentExamTbl 
			SET status = '2'
			WHERE 490examTbl_examId = '$examId';
		";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = "update".mysqli_error().$examId;
			return json_encode($data);
		}
		
		
		// Get all students not in exam grades table
		// for each student, create exam grade
		$data["message"] = "Success";
		return json_encode($data);
		/*
		$query = "
			SELECT sExamId
			FROM
			490studentExamTbl
			WHERE 490examTbl_examId = '$examId' and status != '1';
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Failure";
			$data["error"] = "select sExam".mysqli_error();
			return json_encode($data);
		}
		$sExamIds = array();
		while ($row = mysqli_fetch_array($result)) {
			array_push($sExamIds, $row["sExamId"]);
		}
		
		$query = "
			SELECT 490questionTbl_questionId, points
			FROM
			490examQuestionTbl
			WHERE 490examTbl_examId = '$examId';
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Failure";
			$data["error"] = "select quest".mysqli_error();
			return json_encode($data);
		}
		$questions = array();
		while ($row = mysqli_fetch_array($result)) {
			$temp = array();
			$temp['questionId'] = $row['490questionTbl_questionId'];
			$temp['totalPoints'] = $row['points'];
			array_push($questions, $temp);
		}
		
		$query = "INSERT INTO 490examGradesTbl VALUES ";
		for ($i=0; $i < count($sExamIds); $i++) {
			$id = $sExamIds[$i];
			for ($j = 0; $j < count($questions); $j++) {
				$questionId = $questions[$j]["questionId"];
				$pointsEarned = '0';
				$totalPoints = $questions[$j]["totalPoints"];
				$query .= "(DEFAULT, '$id', '$questionId', '$totalPoints', '', ''),";
			}
		}
		$query = substr($query, 0, -1).";";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = "insert".mysqli_error();
			return json_encode($data);
		}
		$idList = implode(",", $sExamIds);
		
		$examqIds = array();
		$query = "
			SELECT DISTINCT 490examGradesTbl.examqId, 490testCaseTbl.testCaseId
			FROM
			490examGradesTbl
			JOIN
			490testCaseTbl
			ON 490examGradesTbl.490questionTbl_questionId = 490testCaseTbl.490questionTbl_questionId
			WHERE 490examGradesTbl.490studentExamTbl_sExamId IN ($idList)
			ORDER BY 490examGradesTbl.examqId ASC;
		";
		$result = mysqli_query($db, $query);
		if ($result->num_rows == 0){
			$data["message"] = "Failure";
			$data["error"] = "select examqId".mysqli_error().$idList;
			return json_encode($data);
		}
		$data["error"] = "";
		while ($row = mysqli_fetch_array($result)) {
			$test = array();
			$test["examqId"] = $row["examqId"];
			$test["testCaseId"] = $row["testCaseId"];
			$data["error"] .= $row["examqId"]." ".$row["testCaseId"]."/";
			array_push($examqIds, $test);
		}
		
		$prev = "";
		$count = 0;
		$query = "INSERT INTO 490itemTbl VALUES ";
		for ($i=0; $i < count($examqIds); $i++) {
			$examqId = $examqIds[$i]["examqId"];
			$testCaseId = $examqIds[$i]["testCaseId"];
			$data["error"] .= $prev." ".$examqId." ".$testCaseId." |";
			if ($prev != $examqId) {
				$count = 0;
				$query .= "
					(DEFAULT, '$examqId', 'function', '', '', NULL),
					(DEFAULT, '$examqId', 'colon', '', '', NULL),
					(DEFAULT, '$examqId', 'constraints', '', '', NULL),
				";
				$prev = $examqId;
			}
			$query .= "(DEFAULT, '$examqId', 'testCase$count, '', '', '$testCaseId'),";
			$count++;
		}
		$query = substr($query, 0, -1).";";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] .= $query."insert item".mysqli_error().$idList;
			return json_encode($data);
		}
		
		$query = "
			UPDATE 490studentExamTbl 
			SET status = '2'
			WHERE 490examTbl_examId = '$examId';
		";
		if (!mysqli_query($db, $query)){
			$data["message"] = "Failure";
			$data["error"] = "update".mysqli_error();
			return json_encode($data);
		}
		
		
		// Get all students not in exam grades table
		// for each student, create exam grade
		$data["message"] = "Success";
		return json_encode($data);
		*/
	}
?>