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
	$url = "https://web.njit.edu/~jrd62/CS490/rc/teacher_middle_exam.php";
	
	$ch = curl_init($url);
	$payload = json_encode($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	if($json["message"]){
		$err = $json["error"];
		echo "<script> console.log(".$err.")</script>";
	}
	
	
	
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
					var comments = table.rows[i].cells[4].firstChild.value;
					var qtable = document.getElementById("".concat(questionId, "points"));
					console.log(qtable);
					question["function"] = {};
					question["colon"] = {};
					question["constraints"] = {};
					question["function"]["itemId"] = qtable.rows[1].cells[0].id;
					question["colon"]["itemId"] = qtable.rows[1].cells[1].id;
					question["constraints"]["itemId"] = qtable.rows[1].cells[2].id;
					if (qtable.rows[1].cells[0].firstChild.value == "") {
						question["function"]["pointsEarned"] = qtable.rows[1].cells[0].firstChild.placeholder;
					}
					else {
						question["function"]["pointsEarned"] = qtable.rows[1].cells[0].firstChild.value;
					}
					if (qtable.rows[1].cells[1].firstChild.value == "") {
						question["colon"]["pointsEarned"] = qtable.rows[1].cells[1].firstChild.placeholder;
					}
					else {
						question["colon"]["pointsEarned"] = qtable.rows[1].cells[1].firstChild.value;
					}
					if (qtable.rows[1].cells[2].firstChild.value == "") {
						question["constraints"]["pointsEarned"] = qtable.rows[1].cells[2].firstChild.placeholder;
					}
					else {
						question["constraints"]["pointsEarned"] = table.rows[1].cells[2].firstChild.value;
					}
					question["testCases"] = [];
					var tTable = document.getElementById("".concat(questionId, "testCases"));
					for (var j=1; j<tTable.rows.length-1; j++) {
						let temp = {};
						temp["itemId"] = tTable.rows[j].id;
						if (tTable.rows[j].cells[1].firstChild.value == "") {
							temp["pointsEarned"] = tTable.rows[j].cells[1].firstChild.placeholder;
						}
						else {
							temp["pointsEarned"] = tTable.rows[j].cells[1].firstChild.value;
						}
						question["testCases"].push(temp);
						console.log(temp);
					}
					question['questionId'] = questionId;
					question['comments'] = comments;
					formData['questions'].push(question);
					console.log("one loop done");
				}
				console.log(formData);
				return false;
				// cURL to middle end
				fetch("https://web.njit.edu/~jrd62/CS490/rc/teacher_middle_exam.php", {
					method: "POST",
					body: JSON.stringify(formData)
				})
				.then((response) => {
					console.log(response);
					if (response["message"] == "Failure") {
						console.log(response['error']);
						return false;
					}
					location.href = "".concat('https://web.njit.edu/~dn236/CS490/rc/teacherExamStudents.php?examId=',document.getElementById('examId').innerText);
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
							echo "<td><pre style='background-color:rgb(180,180,180);'>".$json[$i]["answer"]."</pre></td>";
							echo '<td><table id="'.$json[$i]["questionId"].'points" style="width:100%">';
							echo '<tr><th>Function Name</th><th>Colon</th><th>Constraint</th><th>Test Cases</th></tr>';
							echo '<tr><td id="'.$json[$i]["function"]["itemId"].'">'."<input style='width: 25%;' placeholder='".$json[$i]["function"]["pointsEarned"]."'>"." /".$json[$i]["function"]["totalSubPoints"]."</td>";
							$totalPointsEarned += $json[$i]["function"]["pointsEarned"];
							echo '<td id="'.$json[$i]["colon"]["itemId"].'">'."<input style='width: 25%;' placeholder='".$json[$i]["colon"]["pointsEarned"]."'>"." /".$json[$i]["colon"]["totalSubPoints"]."</td>";
							$totalPointsEarned += $json[$i]["colon"]["pointsEarned"];
							echo '<td id="'.$json[$i]["constraints"]["itemId"].'">'."<input style='width: 25%;' placeholder='".$json[$i]["constraints"]["pointsEarned"]."'>"." /".$json[$i]["constraints"]["totalSubPoints"]."</td>";
							$totalPointsEarned += $json[$i]["constraints"]["pointsEarned"];
							echo '<td><table id="'.$json[$i]["questionId"].'testCases" style="width:100%">';
							$testCases = $json[$i]["testCases"];
							for ($j=0; $j < count($testCases); $j++) {
								echo '<tr id="'.$testCases[$j]["itemId"].'">';
								$str = "";
								$parameters = "Parameters: ";
								$data = json_decode($testCases[$j]['data'], true);
								for ($h=0; $h < count($data['parameters']); $h++) {
									$parameters .= $data['parameters'][strval($h)]."; ";
								}
								$str .= $parameters."\nOutput: ".$data['result'];
								echo "<td><pre style='background-color:rgb(180,180,180);'>".$str."</pre></td>";
								echo "<td>"."<input style='width: 25%;' placeholder='".$testCases[$j]["pointsEarned"]."'>"." /".$testCases[$j]["totalSubPoints"]."</td></tr>";
								$totalPointsEarned += $testCases[$j]["pointsEarned"];
							}
							echo "</table></td></tr></table></td>";						
							echo "<td>".$json[$i]["totalPoints"]."</td>";
							echo "<td><textarea style='width: 100%; height: 140px; resize:both'>".$json[$i]["comments"]."</textarea></td>";
							echo "</tr>";
							$totalPointsEarned += (float)$json[$i]["pointsEarned"];
							$maxPoints +=  (float)$json[$i]["totalPoints"];
						}
						$percentage = ceil(100*($totalPointsEarned / $maxPoints));
						echo "<td></td><td></td><td></td><td> Percentage: ".$percentage."% </td>";
					?>
				</table>
			</div>
			<div class="flex-container row">
				<button type="button" style="height: 40px; width: 150px" onclick="submit()">Submit Changes</button>
				<button type="button" style=" height: 40px; width: 150px" onclick="location.href = 'https://web.njit.edu/~dn236/CS490/rc/teacherExamList.php';">Back</button>
			</div>
		</div>
		
	</body>
</html>
<?php ob_flush();?>