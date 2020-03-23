<?php 
	// If session doesn't exists, redirect to login page
	if(session_id() == '' || !isset($_SESSION)) {
		header('Location: ./index.php');
	}
	session_start();
	
	ob_start();
	
	//if no session data
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ./index.php');
	}
	//if teacher redirect to teacher landing
	if ($_SESSION['role'] = '2') {
		header('Location: ./teacherView.php');
	}
	
	$data = array();
	$data['requestType'] = 'getExamQuestions';
	$data['examId'] = $_SESSION['examId'];
	$url = "https://web.njit.edu/~jrd62/CS490/student_middle.php";
	
	$ch = curl_init($url);
	$payload = json_encode($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	
?>
<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="styles.css">
		<script>
			function submit(){
				var table = document.getElementById("equestions");
				let formData = new FormData();
				formData.append('requestType', 'submitStudentExam');
				formData.append('ucid', document.getElementById("ucid").innerText);
				formData.append('examId', document.getElementById("examId").innerText);
				for (var i=1; i<table.rows.length; i++) {
					let question = new FormData();
					var questionId = table.rows[i].id;
					var answer = table.rows[i].children[1].innerText;
					var points = table.rows[i].children[2].innerText;
					question.append('questionId', questionId);
					question.append('totalPoints', points);
					question.append('answer', answer);
					formData.append('questions', question);
				}
				// cURL to middle end
				fetch("*********link*******", {
					method: "POST",
					body: formData
				})
				.then((response) => {
					console.log(response);
					response.json().then((data) => {
						if (data["message"] == "Success") {
							// Redirect back after successful submission
							location.href = 'Location: ./studentView.php'
						}
						else {
							alert(''.concat("There was a problem submitting the exam. Please try again. Error message: ", data['error']));
						}
					})
				})
				.catch(function(error) {
					console.log(error);
				});
				return;
			}
			
			function filter() {
				var diff = document.getElementById("difficulty").value;
				var tag = document.getElementById("tag").value;
				let formData = new FormData();
				formData.append('requestType', 'getQuestions');
				if (diff != "") {
					formData.append('difficulty', diff);
				}
				if (tag != "") {
					formData.append('tag', tag);
				}
				for (var p of formData) {
				  console.log(p);
				}
				// cURL to middle end
				fetch("********LINK HERE********", {
					method: "POST",
					body: formData
				})
				.then((response) => {
					console.log(response);
					response.json().then((data) => {
						var questions = document.getElementById('questions')
						while (questions.firstChild()) {
							questions.removeChild(questions.firstChild());
						}
						var count = Object.keys(data).length;
						for (var i=0; i<count; i++) {
							var tr = document.createElement('tr');
							tr.setAttribute("id", data[i][questionId]);
							var td = document.createElement('tr');
							td.textContent = data[i][question];
							tr.appendChild(td);
							var tb = document.createElement('tr');
							var button = document.createElement('button');
							button.setAttribute("type", "button");
							button.setAttribute("onclick", "add(this.closest('tr'))");
							button.textContent = "Add";
							tb.appendChild(button);
							tr.appendChild(tb);
							document.getElementById('questions').appendChild(tr);
						}
					})
				})
				.catch(function(error) {
					console.log(error);
				});
				return;
			}
			
			function add(question) {
				if (document.getElementById('q'.concat(question.id))) {
					alert("You already added this question");
					return;
				}
				var tr = document.createElement('tr');
				tr.setAttribute("id", 'q'.concat(question.id));
				var td = document.createElement("td");
				td.textContent = question.children[0].innerText;
				tr.appendChild(td);
				td = document.createElement("td");
				var points = document.createElement("input");
				td.appendChild(points);
				tr.appendChild(td);
				td = document.createElement("td");
				var button = document.createElement("button");
				button.setAttribute("type", "button");
				button.setAttribute("style", "height: 40px; width: 150px");
				button.setAttribute("onclick", "this.closest('tr').remove()");
				button.textContent = "Delete";
				td.appendChild(button);
				tr.appendChild(td);
				document.getElementById('equestions').appendChild(tr);
				return;
			}
			
			function testCases(testCase) {
				var count = Object.keys(testCase).length;
				var str = testCase[0]['case'];
				for (var i=0; i<count; i++) {
					var parameters = "\nParameters: "
					for (var j=0; j < Object.keys(testCase[i]['data']['parameters']).length; j++) {
						var parameters = "".concat(parameters, testCase[i]['data']['parameters'][j], "; ")
					}
					str = "".concat(str, parameters, "\n Output: ", testCase[i]['data']['result'], "\n");
				}
				alert(str);
			}
			
			function back(){
				// Go back to previous page
				location.href = '********LINK HERE********';
				return;
			}
		</script>
	</head>
	<body>
		<?php
			echo "<p id='ucid' hidden>{$_SESSION['ucid']}</p>";
			echo "<p id='examId' hidden>{$_SESSION['examId']}</p>";
		?>
		<div class="flex-container column" style="width: 100%; margin: 0%; float:left; border-right: 1px black solid;">
			<div class="flex-container column" style="margin: 0%; float:left;">
				<div class="flex-container row">
					<h1> <?php echo "Exam ".$_SESSION['examId']?> </h1>
				</div>
			</div>
			<div class="flex-container row" style="width:98%; float:left">
				<table id="equestions" style="width:100%">
					<tr>
						<th> Question </th>
						<th> Answer </th>
						<th> Points </th>
						<th> Test Cases </th>
					</tr>
					<?php
						for ($i = 0; $i < count($json); $i++) {
							echo "<tr id=".$json[$i]["questionId"].">";
							echo "<td>".$json[$i]["question"]."</td>";
							echo "<td><textarea style='width: 100%; resize:vertical' id='testCase' required></textarea></td>";
							echo "<td>".$json[$i]["totalPoints"]."</td>";
							$testCases = $json[$i]["testCases"];
							$str = $testCases[0]['case'];
							for ($j=0; $j < count($testCases); $j++) {
								$parameters = "\nParameters: ";
								for ($h=0; $h < count($testCases[$j]['data']['parameters']); $h++) {
									$parameters .= $testCases[$j]['data']['parameters'][$h]."; ";
								}
								$str .= $parameters."\nOutput: ".$testCases[$j]['data']['result']."\n";
							}
							echo "<td><pre>".$str."</pre></td>";
							echo "</tr>";
						}
					?>
				</table>
			</div>
			<div class="flex-container row">
				<button type="button" style="height: 40px; width: 150px" onclick="submit()">Submit Exam</button>
			</div>
		</div>
		
	</body>
</html>
<?php ob_flush();?>