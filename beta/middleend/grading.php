<?php
//grabbing json from backend
$str_json = file_get_contents('php://input'); 
//decoding json into response array
$response = json_decode($str_json, true); 
//counting the amount of questions within exam
$question_num = count($response);
//grading each question
for($i = 0; $i < $question_num; $i++){
    $parse = $response[$i]; //the json of testcases/param/etc..
    $question = json_decode($parse);
    //questionId
    $questionId = $question['questionId'];
    //questionId
    $question_name = $question['question'];
    //functionName
    $functionName = $question['functionName'];
    //difficulty
    $difficulty = $question['difficulty'];
    //tag
    $tag = $question['tag'];
    //testCases
    $testCases = $question['testCases'];
    //grabbing parameters
    for($j=0; $j < count($testCases); $j++){
        $data = $testCases['data'];
        $parameters = $data['parameters'];
        $results = $data['results'];
    }
    //answer
    $answer = $question['answer'];
    //comments
    $comments = $question['comments'];
    //pointsEarned
    $pointsEarned = $question['pointsEarned'];
    //totalPoints
    $totalPoints = $question['totalPoints'];
    //calculate grade for each questions
    $grade = grade_question($answer, $functionName, $pointsEarned, $parameters, $results);
    //separate the grade
    $grade = explode(",", $grade);
    //comments
    $comments = $grade[0];
    //grades
    $final_grade = $grade[1];   
    
    if($requestType == 'getStudentAnswers'){ 
        //data from json response
        $data = array('questionId' => $questionId, 'question' => $question_name, 'functionName' => $functionName, 'difficulty' => $difficulty, 'tag' => $tag, 'testCases' => $testCases, 'answer' => $answer, 'comments' => $comments, 'answer' => $final_grade, 'totalPoints' => $totalPoints); 
        //url to backend
        $url = "https://web.njit.edu/~pk549/490/beta/examTbl.php";
        //initialize curl session and return a curl handle
        $ch = curl_init($url);
        //options for a curl transfer	
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        //execute curl session
        $response = curl_exec($ch);
        //close curl session
        curl_close ($ch);
        //decoding response
        $response_decode = json_decode($response);
        //return response
        return $response_decode[0];
    }
}

function grade_question($answer, $functionName, $pointsEarned, $parameters, $results){
    //initial grade
    $question_grade = $pointsEarned;
    //total grade
    $total_points = 0;
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
        $total_points += 5;
    }
    else{
        $comments .= "Better luck next time. Function name is not correct, it's suppose to be $functionName";
        $question_grade += 0;
        $total_points += 5;
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
        $total_points++;
    }
    else{
        $comments .= "Better luck next time. The parameters you entered were incorrect. The correct parameters are $parameters";
        $question_grade += 0;
        $total_points++;
    } 
    //counting parameters to test through
    $parameter_count = count($parameters);
    //setting file
    $file = "test.py";
    //testing for each parameter
    for($i = 0; $i < $parameter_count; $i++){
        //individual parameter
        $parameter = $temp[$i];
        $result = $results[$i];
        //inserting code into file
        file_put_contents($file, $answer . "\n" . "print($answer_function_name($parameter))");
        //running the python code
        $runpython = exec("python test.py");
        //checking if code matches the result
        if ($runpython == $result){
            $comments .= "Awesome, code results were correct";
        }
        else{
            //error checking
            if($runpython == ""){
                $question_grade += 0;
                $comments .= "Testcase incorrect. For $answer_function_name($parameter)";
            }
            else{
                $question_grade += 0;
                $comments .= "Testcase incorrect. For $answer_function_name($parameter)";
            }
        }
    }
    //adding comments to the grade
    $comma = ",";
    //$grade = round($grade, 0);
    $grade = $comments .= $comma .= $question_grade;
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