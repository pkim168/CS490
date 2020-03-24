<?php 
	// If session doesn't exists, redirect to login page
	session_start([
        'use_only_cookies' => 1,
        'cookie_lifetime' => 0,
        'cookie_secure' => 1,
        'cookie_httponly' => 1
    ]);
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ./index.php');
	} 
	if ($_SESSION['role'] != '2') {
		header('Location: ./index.php');
	}
	if (isset($_GET['studentId'])){
		$_SESSION['studentId'] = $_GET['studentId'];
	} 
	
	$data = array();
	$data['requestType'] = 'getStudentAnswers';
	$data['examId'] = $_SESSION['examId'];
	$data['ucid'] = $_SESSION['studentId'];
	$url = "https://web.njit.edu/~jrd62/CS490/beta/teacher_middle_exam.php";
	
	$ch = curl_init($url);
	$payload = json_encode($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	
	
	
	ob_start();
?>
<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="styles.css">
		<script>
		function submit(){
			var table = document.getElementById("equestions");
				let formData = {};
				formData['requestType'] = 'editStudentExam';
				formData['ucid'] = document.getElementById("studentId").innerText;
				formData['examId'] = document.getElementById("examId").innerText;
				formData['questions'] = [];
				for (var i=1; i<table.rows.length-1; i++) {
					let question = {};
					var questionId = table.rows[i].id;
					var comments = table.rows[i].cells[5].firstChild.value;
					if (table.rows[i].cells[3].firstChild.value == "") {
						var points = table.rows[i].cells[3].firstChild.placeholder;
					}
					else {
						var points = table.rows[i].cells[3].firstChild.value;
					}
					question['questionId'] = questionId;
					question['points'] = points;
					question['comments'] = comments;
					formData['questions'].push(question);
				}
				// cURL to middle end
				fetch("https://web.njit.edu/~jrd62/CS490/beta/teacher_middle_exam.php", {
					method: "POST",
					body: JSON.stringify(formData)
				})
				.then((response) => {
					console.log(response);
					location.href = "".concat('https://web.njit.edu/~dn236/CS490/beta/teacherExamStudents.php?examId=',document.getElementById('examId').innerText);
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
			echo "<p id='studentId' hidden>{$_SESSION['studentId']}</p>";
		?>
		<div class="flex-container column" style="width: 100%; margin: 0%; float:left; border-right: 1px black solid;">
			<div class="flex-container column" style="margin: 0%; float:left;">
				<div class="flex-container row">
					<h1> <?php echo $_SESSION['studentId']."'s Exam"?> </h1>
				</div>
			</div>
			<div class="flex-container row" style="width:98%; float:left">
				<table id="equestions" style="width:100%">
					<tr>
						<th> Question </th>
						<th> Answer </th>
						<th> Test Cases </th>
						<th> Points Earned </th>
						<th> Points Total </th>
						<th> Comments </th>
					</tr>
					<?php
						$totalPointsEarned=0;
						$maxPoints=0;
						for ($i = 0; $i < count($json); $i++) {
							echo "<tr id=".$json[$i]["questionId"].">";
							echo "<td>".$json[$i]["question"]."</td>";
							echo "<td>".$json[$i]["answer"]."</td>";
							$testCases = $json[$i]["testCases"];
							$str = $testCases[0]['case'];
							for ($j=0; $j < count($testCases); $j++) {
								$parameters = "\nParameters: ";
								$data = json_decode($testCases[$j]['data'], true);
								for ($h=0; $h < count($data['parameters']); $h++) {
									$parameters .= $data['parameters'][strval($h)]."; ";
								}
								$str .= $parameters."\nOutput: ".$data['result']."\n";
							}
							echo "<td><pre>".$str."</pre></td>";
							echo "<td><input style='width: 100%;' placeholder='".$json[$i]["pointsEarned"]."'></td>";							
							echo "<td>".$json[$i]["totalPoints"]."</td>";
							echo "<td><textarea style='width: 100%; resize:vertical'>".$json[$i]["comments"]."</textarea></td>";
							
							echo "</tr>";
							$totalPointsEarned += (float)$json[$i]["pointsEarned"];
							$maxPoints +=  (float)$json[$i]["totalPoints"];
						}
						$percentage = ceil(100*($totalPointsEarned / $maxPoints));
						echo "<td></td><td></td><td></td><td></td><td> Percentage: ".$percentage."% </td><td></td>";
					?>
				</table>
			</div>
			<div class="flex-container row">
				<button type="button" style="height: 40px; width: 150px" onclick="submit()">Submit Changes</button>
			</div>
		</div>
		
	</body>
</html>
<?php ob_flush();?>