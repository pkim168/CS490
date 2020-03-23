<?php 
	// If session doesn't exists, redirect to login page
	session_start();
	ob_start();
	/*
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ********LINK HERE********');
	}
	if ($_SESSION['role'] != '2') {
		header('Location: ********LINK HERE********');
	}
	*/
	$data = array();
	$data['requestType'] = 'getExamStatuses';
	$data['examId'] = $_SESSION['examId'];
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
			function exam(id) {
				$_SESSION['examId'] = id;
				location.href = '********LINK HERE********';
			}
			
			function releaseExam(name) {
				var id = name.substr(1);
				let formData = new FormData();
				formData.append('requestType', 'releaseExam');
				formData.append('examId', id);
				// cURL to middle end
				fetch("********LINK HERE********", {
					method: "POST",
					body: formData
				})
				.then((response) => {
					console.log(response);
					response.json().then((data) => {
						if (data["message"] == "Success") {
							// Redirect back after successful submission
							location.href = '********LINK HERE********'
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
		?>
		<div class="flex-container column" style="width: 100%; margin: 0%; float:left; border-right: 1px black solid;">
			<div class="flex-container row">
				<h1> Students </h1>
			</div>
			<div class="flex-container row" style="width:98%; float:left">
				<table id="exams" style="width:100%">
					<tr>
						<th> Student Id </th>
						<th> Status </th>
					</tr>
					<?php
						for ($i = 0; $i < count($json); $i++) {
							echo "<tr id=".$json[$i]['ucid'].">";
							echo "<td>".$json[$i]['ucid']."</td>";
							if ($json[$i]['status'] == 0) {
								echo "<td>Not Taken</td>";
							} else if ($json[$i]['status'] == 1) {
								echo "<td>Graded</td>";
							} else {
								echo "<td>Released</td>";
							}
							echo "<td><button type='button' style='height: 40px; width: 100%' onclick='exam(this.id)'>View Results</button></td>";
							echo "</tr>";
						}
					?>
				</table>
			</div>
		</div>
		
	</body>
</html>
<?php ob_flush();?>