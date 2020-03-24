<?php
//grabbing from front end
$str_json = file_get_contents('php://input'); 
//decoding json into response array
$response = json_decode($str_json, true);
//grabbing fields
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
	$url = "https://web.njit.edu/~pk549/490/beta/examTbl.php";
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
	//return response
	return $response_questions;
}

//counting the amount of questions within exam
$question_num = count($questions);
//grading each question
for($i = 0; $i < $question_num; $i++){
    $question = $questions[$i];
    //string question
    $question_str = $question['question'];
    //difficulty
    $difficulty = $question['difficulty'];
    //tag
    $tag = $question['tag'];
    //questionId
    $questionId = $question['questionId'];
    //student answer
    $answer = $question['answer'];
    //totalPoints
    $totalPoints = $question['totalPoints'];

    //grabbing testcases from backend
    $backend_question = $backend_questions[$i];
    $backend_questionId = $backend_question['questionId'];
    $backend_functionName = $backend_question['functionName'];
    $backend_testCases = $backend_question['testCases'];
    $functionName = $backend_testCases[0]['case'];
    for ($j=0; $j < count($backend_testCases); $j++) {
        $parameters = "\nParameters: ";
        $data = json_decode($testCases[$j]['data'], true);
        $result = $data['result'];
        for ($h=0; $h < count($data['parameters']); $h++) {
            $parameters .= $data['parameters'][strval($h)]."; ";
        }
    }

    //calculate grade for each questions
    $grade = grade_question($answer, $functionName, $parameters, $result, $totalPoints);
    //separate the grade
    $grade = explode(",", $grade);
    //comments
    $comments = $grade[0];
    //grades
    $final_grade = $grade[1]; 
    //data from for response
    $data = array('questionId' => $questionId, 'question' => $question_str, 'functionName' => $functionName, 'difficulty' => $difficulty, 'tag' => $tag, 'testCases' => $testCases, 'answer' => $answer, 'comments' => $comments, 'pointsEarned' => $final_grade, 'totalPoints' => $totalPoints);
}

//send to backend
//student answers
$student_answers = array('requestType' => 'submitStudentExam', 'ucid' => $ucid, 'examId' => $examId, 'questions' => $data);
//url to backend
$url = "https://web.njit.edu/~pk549/490/beta/examTbl.php";
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
return $send;


function grade_question($answer, $functionName, $parameters, $result, $totalPoints){
    //initial grade
    $pointsEarned = 0;
    //setting comments
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
        $comments .= "Better luck next time. Function name is not correct, it's suppose to be $functionName";
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
    if(strcmp($student_parameters, $parameters) == 0){
        $comments .= "Awesome, you got the parameters correct";
        $pointsEarned += ($totalPoints*0.2);
    }
    else{
        $comments .= "Better luck next time. The parameters you entered were incorrect. The correct parameters are $parameters";
    } 
    //counting parameters to test through
    $parameter_count = count($parameters);
    //setting file
    $file = "test.py";
    //testing for each parameter
    for($i = 0; $i < $parameter_count; $i++){
        //individual parameter
        $parameter = $temp[$i];
        $r = $result[$i];
        //inserting code into file
        file_put_contents($file, $answer . "\n" . "print($answer_function_name($parameter))");
        //running the python code
        $runpython = exec("python test.py");
        //checking if code matches the result
        if ($runpython == $r){
            $comments .= "Awesome, code results were correct";
            $pointsEarned += ($totalPoints*0.6);
        }
        else{
            //error checking
            if($runpython == ""){
                $comments .= "Testcase incorrect. For $answer_function_name($parameter)";
            }
            else{
                $comments .= "Testcase incorrect. For $answer_function_name($parameter)";
            }
        }
    }
    //adding comments to the grade
    $comma = ",";
    //$grade = round($grade, 0);
    $grade = $comments .= $comma .= ($pointsEarned);
    //returning grade
    //grade only tests for whether the function name and the parameters are correct
    return $grade;
?>


<!--what will be received-->
<!--
0: {
	questionId: (String),
	question: (String),
	functionName: (String),
	difficulty: 'Easy, 'Medium', or 'Hard'
	tag: (string)
	testCases: {
		0: {
			case: Testcase,
			data: {
				parameters: {
					0: Parameter,
					1: Parameter,
					etc.
				},
				argc: Num of Arguments,
				result: Expected output
			}
		},
		1: {
			case: Testcase,
			data: {
				parameters: {
					0: Parameter,
					1: Parameter,
					etc.
				},
				argc: Num of Arguments,
				result: Expected output
			}
		},
		etc.
	}
	answer: (String),
	comments: (String),
	pointsEarned: (String),
	totalPoints: (String)
}
-->