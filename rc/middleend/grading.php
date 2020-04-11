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
$backend_questions=get_exam_questions('getQuestions',$examId);
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
	return $response_decode[0];
}

//counting the amount of questions within exam
$question_num = count($questions);
$student_questions = array();
//grading each question
for($i = 0; $i < $question_num; $i++){
    $question = $questions[$i];

    $questionId = $question['questionId'];
    $answer = $question['answer'];
    $pointsEarned = $question['pointsEarned'];
    $totalPoints = $question['totalPoints'];
    $comments = $question['comments'];
    
    //grabbing testcases from backend
    $backend_question = $backend_questions[$i];
    $backend_question_str = $backend_question['question'];
    $backend_questionId = $backend_question['questionId'];
    $backend_functionName = $backend_question['functionName'];
    $backend_difficulty = $backend_question['difficulty'];
    $backend_tag = $backend_question['tag'];
    $backend_constraints = $backend_question['constraints'];
    $backend_testCases = $backend_question['testCases'];
    //counting the number of test cases
    $testCases_num = count($backend_testCases);
    //for each test case lets grab the parameters and results
    $parameters = "";
    $result = "";
    for($j = 0; $j < $testCases_num; $j++) {
        $data = json_decode($backend_testCases[$j]['data'], true);
        $result = $data['result'];
        for ($h=0; $h < count($data['parameters']); $h++) {
            $parameters .= $data['parameters'][strval($h)]."; ";
        }
    }
    //calculate grade for each test case
    $grade = grade($answer, $backend_functionName, $parameters, $backend_constraints, $result, $totalPoints);
    //separate the grade
    $testCase_grade = explode(",", $grade);
    //comments
    $comments = $testCase_grade[0];
    //grades
    $final_grade = $testCase_grade[1]; 
    //data from for response
    $data = array('questionId' => $questionId, 'answer' => $answer, 'comments' => $comments, 'pointsEarned' => $final_grade, 'totalPoints' => $totalPoints);
    array_push($student_questions, $data);
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


function grade($answer, $functionName, $parameters, $backend_constraints, $result, $totalPoints){
    //setting initial grade and comments
    $pointsEarned = 0;
    $comments = "";

    //cleaning students answer of white space in the beginning
    $answer = ltrim($answer);
    $split = preg_split("/\s+|\(|:/", $answer);
    //grabbing the first word, which should be def
    $def = $split [0]; 
    //grabbing the function name
    $answer_function_name = $split[1];

    //checking if function name is correct
    if($answer_function_name == $functionName){
        $comments .= "Congrats. Function name is correct!";
        $pointsEarned += ($totalPoints*0.2);
    }
    else{
        $comments .= "Better luck next time. Function name is incorrect. Funcations $functionName and $answer_function_name";
    }

    //splitting original student answer
    $split_left = explode(")", $answer);
    //should give you everything up to ')'
    $temp = $split_left[0];
    //splitting it again giving you everything up to '('
    $split_right = explode("(", $temp);
    //the student parameters
    $answer_parameter = $split_right[1];
    //removing any additional space
    $student_parameters = preg_replace("/\s/","", $answer_parameter);
    $parameters = preg_replace("/\s/","", $parameters);
    //checking student parameters with the test case parameters
    if($student_parameters == $parameters){
        $comments .= "Awesome you got the parameters correct\n";
        $pointsEarned += ($totalPoints*0.2);
    }
    else{
        $comments .= "Better luck next time. The parameters you entered were incorrect. $\n";
    } 
    //tested and working up til this point

    //counting the amount of constraints
    $constraint_count = count($backend_constraints);
    //checking if answer contains each constraint
    for($p = 0; $p < $constraint_count; $p++) {
        if(strpos($backend_constraints[$p], $answer) !== false){
            $comments .= "Awesome you got right constraint.\n";
        }
        else{
            $comments .= "Sorry you got wrong constraint. The actual constraint was: $backend_constraints[$p]\n";
        }
    }
    //counting the amount of parameters
    $parameter_count = substr_count($parameters, ",");
    //setting file
    $file = "test.py";
    //testing for each parameter
    for($i = 0; $i < $parameter_count; $i++){
        //individual parameter
        $split_leftt = explode(",", $parameters);
        $parameter = $split_leftt[$i];
        //inserting code into file
        file_put_contents($file, $answer . "\n" . "print($answer_function_name($parameters))");
        //running the python code
        $runpython = exec("python $file");
        //checking if code matches the result
        if ($runpython == $result){
            $comments .= "Awesome code results were correct";
            $pointsEarned += ($totalPoints*0.6);
        }
        else{
            $comments .= "Result was incorrect. Correct result was $result";
        }
    }
    //adding comments to the grade
    $comma = ",";
    $grade .= $comments .= $comma .= ($pointsEarned);
    //returning grade
    return $grade;
}
?>