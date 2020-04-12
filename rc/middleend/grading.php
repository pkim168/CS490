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
	return $response_decode[0];
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
    $grade = grade($answer, $questionId, $functionName, $backend_constraints, $backend_testCases, $totalPoints);
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
    //for each test case lets grab the parameters and results
    $parameters = "";
    $result = "";
    $testCases_num = count($backend_testCases);
    for($j = 0; $j < $testCases_num; $j++) {
        $data = json_decode($backend_testCases[$j]['data'], true);
        $result = $data['result'];
        for ($h=0; $h < count($data['parameters']); $h++) {
            $parameters .= $data['parameters'][strval($h)]."; ";
        }
    }

    //point system testcases 20,functionname 20, constraints 20, colon 20, parameters 20 
    //setting initial grade and comments
    $function_pointsEarned = 0;
    $testCases_pointsEarned = 0;
    $constraints_pointsEarned = 0;
    $colon_pointsEarned = 0;
    $parameters_pointsEarned = 0;
    $comments = "";

    //function name testing
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
        $function_pointsEarned += ($totalPoints*0.2);
    }
    else{
        $comments .= "Better luck next time. Function name is incorrect. Funcations $functionName and $answer_function_name";
    }

    //parameters testing
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
    //backend parameter count
    $parameter_count = substr_count($parameters, ",");
    //student parameter count
    $student_parameter_count = substr_count($student_parameters, ",");
    //checking if student parameter count is equal to backend parameter count
    if($student_parameter_count == $parameter_count){
        $comments .= "Awesome you got the parameters correct\n";
        $parameters_pointsEarned += ($totalPoints*0.2);
    }
    else{
        $comments .= "Better luck next time. The parameters you entered were incorrect. $\n";
    } 
    //tested and working up til this point

    //constraint testing
    //counting the amount of constraints
    $constraint_count = count($backend_constraints);
    //checking if answer contains each constraint
    for($p = 0; $p < $constraint_count; $p++) {
        if(strpos($backend_constraints[$p], $answer) !== false){
            $comments .= "Awesome you got right constraint.\n";
            $constraints_pointsEarned += ($totalPoints*0.2);
        }
        else{
            $comments .= "Sorry you got wrong constraint. The actual constraint was: $backend_constraints[$p]\n";
        }
    }

    //colon testing, if colon is in the student answer then they get points
    if(strpos($answer, ':') !== false){
        $comments .= "Awesome you got the comment.\n";
        $colon_pointsEarned += ($totalPoints*0.2);
    }
    else{
        $comments .= "Sorry you did have the colon.\n";
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
            $testCases_pointsEarned += ($totalPoints*0.2);
        }
        else{
            $comments .= "Result was incorrect. Correct result was $result";
        }
    }

    //packaging the grade 
    $grade = $data = array('questionId' => $questionId, 'function' => [$function_pointsEarned, ($totalPoints*20)], 'colon' => [$colon_pointsEarned, ($totalPoints*20)], 'constrains' => [$constraints_pointsEarned, ($totalPoints*20)], 'testCases' => [], 'answer' => $answer, 'comments' => $comments, 'totalPoints' => $totalPoints);

    //returning grade
    return $grade;
}
?>