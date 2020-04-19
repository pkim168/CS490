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
	if ($_SESSION['role'] != '1') {
		header('Location: ./index.php');
	}
	if (isset($_GET['examId'])){
		$_SESSION['examId'] = $_GET['examId'];
	} 
	$data = array();
	$data['requestType'] = 'getStudentAnswers';
	$data['examId'] = $_SESSION['examId'];
	$data['ucid'] = $_SESSION['ucid'];
	$url = "https://web.njit.edu/~jrd62/CS490/rc/student_middle.php";
	
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
						<th> Points Earned </th>
						<th> Points Total </th>
						<th> Comments </th>
					</tr>
					<?php
						$totalPointsEarned=0;
						$maxPoints=0;
						for ($i = 0; $i < count($json); $i++) {
							$questionPoints = 0;
							echo "<tr id=".$json[$i]["questionId"].">";
							echo "<td>".$json[$i]["question"]."</td>";
							echo "<td><pre style='background-color:rgb(180,180,180);'>".$json[$i]["answer"]."</pre></td>";
							echo '<td><table id="'.$json[$i]["questionId"].'points" style="width:100%">';
							
							//function row
							echo '<tr><th>Function Name</th>';
							echo '<td id="'.$json[$i]["function"]["itemId"].'">'.$json[$i]["function"]["pointsEarned"]."/".$json[$i]["function"]["totalSubPoints"]."</td>";
							echo '<td>'.$comments[0].'</td></tr>';
							$totalPointsEarned += (float)$json[$i]["function"]["pointsEarned"];
							$questionPoints += $json[$i]["function"]["pointsEarned"];

							//colon row
							echo '<th>Colon</th>';
							echo '<td id="'.$json[$i]["colon"]["itemId"].'">'.$json[$i]["colon"]["pointsEarned"]."/".$json[$i]["colon"]["totalSubPoints"]."</td>";
							echo '<td>'.$comments[1].'</td></tr>';
							$totalPointsEarned += (float)$json[$i]["colon"]["pointsEarned"];
							$questionPoints += $json[$i]["colon"]["pointsEarned"];
							
							//constraints row
							echo '<th>Constraint</th>';													
							echo '<td id="'.$json[$i]["constraints"]["itemId"].'">'.$json[$i]["constraints"]["pointsEarned"]."/".$json[$i]["constraints"]["totalSubPoints"]."</td>";
							echo '<td>'.$comments[2].'</td></tr>';
							$totalPointsEarned += (float)$json[$i]["constraints"]["pointsEarned"];
							$questionPoints += $json[$i]["constraints"]["pointsEarned"];
							
							echo '<tr><th>Test Cases</th>';
							echo '<td><table id="'.$json[$i]["questionId"].'testCases" style="width:100%">';
							$testCases = $json[$i]["testCases"];
							for ($j=0; $j < count($testCases); $j++) {
								echo '<tr id="'.$testCases[$j]["itemId"].'">';
								$parameters = "Parameters: ";
								$data = json_decode($testCases[$j]['data'], true);
								for ($h=0; $h < count($data['parameters']); $h++) {
									$parameters .= $data['parameters'][strval($h)]."; ";
								}
								$parameters .= "\nOutput: ".$data['result'];
								echo "<td><pre style='background-color:rgb(180,180,180);'>".$parameters."</pre></td>";
								echo "<td>".$testCases[$j]["pointsEarned"]."/".$testCases[$j]["totalSubPoints"]."</td></tr>";
								echo '<td>'.$comments[2+$j].'</td></tr>';
								$totalPointsEarned += (float)$testCases[$j]["pointsEarned"];
								$questionPoints += $testCases[$j]["pointsEarned"];
							}
							echo "</table></td></tr></table></td>";
							echo "<td>".$questionPoints."/".$json[$i]["totalPoints"]."</td>";
							echo "<td><pre style='background-color:rgb(180,180,180);'>".$json[$i]["comments"]."</pre></td>";
							echo "</tr>";
							//$totalPointsEarned += (float)$json[$i]["pointsEarned"];
							$maxPoints +=  (float)$json[$i]["totalPoints"];
						}
						$percentage = ceil(100*($totalPointsEarned / $maxPoints));
						echo "<td></td><td></td><td></td><td> Percentage: ".$percentage."% </td>";
					?>
				</table>
			</div>
		</div>
		<div  style = "display: flex; justify-content: center; width: 100%;">
				<button type="button" style=" height: 40px; width: 150px" onclick="location.href = 'https://web.njit.edu/~dn236/CS490/rc/studentExamList.php';">Back</button>
		</div>
	</body>
</html>
<?php ob_flush();?>