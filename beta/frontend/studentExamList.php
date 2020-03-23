<?php 
	// If session doesn't exists, redirect to login page
	session_start();
	ob_start();
	/*
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ********LINK HERE********');
	}
	if ($_SESSION['role'] != '1') {
		header('Location: ********LINK HERE********');
	}
	*/
	$data = array();
	$data['requestType'] = 'getExams';
	$data['ucid'] = $_SESSION['ucid'];
	$url = "****************URL HERE *********************";
	
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
			function takeExam(id) {
				$_SESSION['examId'] = id;
				location.href = '********LINK HERE********';
			}
			
			function viewResults(id) {
				$_SESSION['examId'] = id;
				location.href = '********LINK HERE********';
			}
		</script>
	</head>
	<body>
		<?php
			echo "<p id='ucid' hidden>{$_SESSION['ucid']}</p>";
		?>
		<div class="flex-container column" style="width: 100%; margin: 0%; float:left; border-right: 1px black solid;">
			<div class="flex-container row">
				<h1> Exams </h1>
			</div>
			<div class="flex-container row" style="width:98%; float:left">
				<table id="exams" style="width:100%">
					<tr>
						<th> Exam Id </th>
						<th> Status </th>
					</tr>
					<?php
						for ($i = 0; $i < count($json); $i++) {
							echo "<tr>";
							echo "<td>".$json[$i]['examId']."</td>";
							if ($json[$i]['status'] == 0) {
								echo "<td>Not Taken</td>";
								echo "<td><button type='button' id='".$json[$i]['examId']."' style='height: 40px; width: 100%' onclick='takeExam(this.id)'>Take Exam</button></td>";
							} else if ($json[$i]['status'] == 1) {
								echo "<td>Graded</td>";
							} else {
								echo "<td>Released</td>";
								echo "<td><button type='button' id='".$json[$i]['examId']."' style='height: 40px; width: 100%' onclick='viewResults(this.id)'>View Results</button></td>";
							}
							echo "</tr>";
						}
					?>
				</table>
			</div>
		</div>
		
	</body>
</html>
<?php ob_flush();?>