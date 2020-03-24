<?php 
	session_start([
        'use_only_cookies' => 1,
        'cookie_lifetime' => 0,
        'cookie_secure' => 1,
        'cookie_httponly' => 1
    ]);
	ob_start();
	
	//if no session data
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ./index.php');
	}
	//if teacher redirect to teacher landing
	if ($_SESSION['role'] == '2') {
		header('Location: ./teacherView.php');
	}
	if (isset($_GET['examId'])){
		$_SESSION['examId'] = $_GET['examId'];
	} 
	$data = array();
	$data['requestType'] = 'getExamQuestions';
	$data['examId'] = $_SESSION['examId'];
	$url = "https://web.njit.edu/~jrd62/CS490/beta/student_middle.php";
	
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
								$data = $testCases[$j]['data'];
								var_dump($data);
								for ($h=0; $h < count($data['parameters']); $h++) {
									$parameters .= $data['parameters'][$h]."; ";
								}
								$str .= $parameters."\nOutput: ".$data['result']."\n";
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