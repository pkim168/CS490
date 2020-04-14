<?php
//grabbing from front end
$str_json = file_get_contents('php://input'); 
//decoding json into response array
$response = json_decode($str_json, true);
//grabbing fields from front end
$requestType = $response['requestType'];
$ucid = $response['ucid'];
$examId = $response['examId'];
$questions = $response['questions'];

//getting questions from backend 
$backend_questions=get_exam_questions('getExamQuestions',$examId);
function get_exam_questions($requestType,$examId){
	//data from json response
	$data = array('requestType' => $requestType, 'examId' => $examId);
	//url to backend
	$url = "https://web.njit.edu/~pk549/490/rc/examTbl.php";
	//initialize curl session and return a curl handle
	$ch = curl_init($url);
	//options for a curl transfer	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	//execute curl session
	$response_questions = curl_exec($ch);
	//close curl session
	curl_close ($ch);
	$response_decode = json_decode($response_questions, true);
	//return response
	return $response_decode;
}

//counting the amount of questions within exam
$question_num = count($questions);
$student_questions = array();
//grading each question
for($i = 0; $i < $question_num; $i++){
    $question = $questions[$i];
    //student answer and total points
    $answer = $question['answer'];
    $totalPoints = $question['totalPoints'];
    $questionId = $question['questionId'];
    
    //grabbing testcases from backend
    $backend_question = $backend_questions[$i];
    $backend_functionName = $backend_question['functionName'];
    $backend_constraints = $backend_question['constraints'];
    $backend_testCases = $backend_question['testCases'];
    //counting the number of test cases
    $testCases_num = count($backend_testCases);
    //calculate grade for each question, get back array
    $grade = grade($answer, $questionId, $backend_functionName, $backend_constraints, $backend_testCases, $totalPoints);
    //pushing to student questions
    array_push($student_questions, $grade);
}
//send student answers to backend
$student_answers = array('requestType' => 'submitStudentExam', 'ucid' => $ucid, 'examId' => $examId, 'questions' => $student_questions);
//url to backend
$url = "https://web.njit.edu/~pk549/490/rc/examTbl.php";
//initialize curl session and return a curl handle
$ch = curl_init($url);
//options for a curl transfer	
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($student_answers));
//execute curl session
$send = curl_exec($ch);
//close curl session
curl_close ($ch);
//return response
echo $send;


function grade($answer, $questionId, $functionName, $backend_constraints, $backend_testCases, $totalPoints){
    //point system testcases 20,functionname 20, constraints 20, colon 20
    //setting initial grade and comments
    $function_pointsEarned = 0;
    $constraints_pointsEarned = 0;
    $colon_pointsEarned = 0;
    $parameters_pointsEarned = 0;
    $comments = "";
	$pointsPerItem = floor($totalPoints*.2);

    //function name testing
    //cleaning students answer of white space in the beginning
    $student_answer = ltrim($answer);
    $split = preg_split("/\s+|\(|:/", $student_answer);
    //grabbing the first word, which should be def
    $def = $split [0]; 
    //grabbing the function name
    $answer_function_name = $split[1];
    //checking if function name is correct
    if($answer_function_name == $functionName){
        $comments .= "Congrats. Function name is correct!\n";
        $function_pointsEarned += floor($totalPoints*0.2);
    }
    else{
        $comments .= "Better luck next time. Function name is incorrect. The correct answer should be: $functionName.\n";
    }

    //constraint testing
    //counting the amount of constraints
    if(strpos($student_answer, $backend_constraints) !== false){
        $comments .= "Awesome you got right constraint.\n";
        $constraints_pointsEarned += floor($totalPoints*0.2);
    }
    else{
        $comments .= "Sorry you got wrong constraint. The actual constraint was: $backend_constraints.\n";
    }

    //colon testing, if colon is in the student answer then they get points
    if(strpos($student_answer, ':') !== false){
        $comments .= "Awesome you got the colon.\n";
        $colon_pointsEarned += floor($totalPoints*0.2);
    }
    else{
        $comments .= "Sorry you didn't have the colon.\n";
    }

    //test case testing
	$testCase_totalPoints = $totalPoints-$pointsPerItem*3;
    $testCases_num = count($backend_testCases);
	$pointsPerCase = floor($testCase_totalPoints/$testCases_num);
	$lastCasePoints = $testCase_totalPoints-$pointsPerCase*($testCases_num-1);
    $testCase_array = array();
    //setting file
    $file = "test.py";
    //testing for each parameter
    for($i = 0; $i < $testCases_num; $i++){
        //grab test case id
        $testCaseId = $backend_testCases[$i]['testCaseId'];
        $parameters = "";
        $result = "";
        $testCases_pointsEarned = 0;
        $data = json_decode($backend_testCases[$i]['data'], true);
        $result = $data['result'];
        //grabbing parameters
        for ($h=0; $h < count($data['parameters']); $h++) {
            if(is_numeric($data['parameters'][strval($h)])){
                $parameters .= $data['parameters'][strval($h)].",";
            }
            else{
                $parameters .= "'".$data['parameters'][strval($h)]."'".",";
            }
        }
        $parameters = substr($parameters, 0, -1);
        //inserting code into file
		if (strpos($student_answer, 'print') || $answer_function_name == "") {
			file_put_contents($file, "#!/usr/bin/env python\n" . $student_answer . "\n" . "$answer_function_name($parameters)");
		}
		else {
			file_put_contents($file, "#!/usr/bin/env python\n" . $student_answer . "\n" . "print($answer_function_name($parameters))");
		}
        /* if($backend_constraints == 'print'){
            file_put_contents($file, "#!/usr/bin/env python\n" . $student_answer . "\n" . "$answer_function_name($parameters)");
        }
        else{
            file_put_contents($file, "#!/usr/bin/env python\n" . $student_answer . "\n" . "print($answer_function_name($parameters))");
        } */
        //running the python code
        $runpython = exec("python $file");
        //checking if code matches the result
        if ($runpython == $result){
            $comments .= "Awesome code results were correct.\n";
			if ($i == $testCases_num-1) {
				$testCases_pointsEarned += $lastCasePoints;
			}
			else {
				$testCases_pointsEarned += $pointsPerCase;
			}
        }
        else{
            $comments .= "Result was incorrect. Your result was: $runpython. Correct result was $result.\n";
        }
		if ($i == $testCases_num-1) {
			$temp = array('testCaseId' => $testCaseId, 'pointsEarned' => $testCases_pointsEarned, 'totalSubPoints' => $lastCasePoints);
		}
		else {
			$temp = array('testCaseId' => $testCaseId, 'pointsEarned' => $testCases_pointsEarned, 'totalSubPoints' => $pointsPerCase);
		}
        
        array_push($testCase_array, $temp);
    }

    //packaging the grade 
    $grade = array('questionId' => $questionId, 'function' => array('pointsEarned' => $function_pointsEarned, 'totalSubPoints' => $pointsPerItem), 'colon' => array('pointsEarned' => $colon_pointsEarned, 'totalSubPoints' => $pointsPerItem), 'constraints' => array('pointsEarned' => $constraints_pointsEarned, 'totalSubPoints' => $pointsPerItem), 'testCases' => $testCase_array, 'answer' => $student_answer, 'comments' => $comments, 'totalPoints' => $totalPoints);

    //returning grade
    return $grade;
}
?>