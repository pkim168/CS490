<?php 
	
	session_start([
        'use_only_cookies' => 1,
        'cookie_lifetime' => 0,
        'cookie_secure' => 1,
        'cookie_httponly' => 1
    ]);
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ./index.php');
	} 
	if ($_SESSION['role'] == '1') {
		header('Location: ./studentView.php');
	}
	
	ob_start();
	$data = array();
	$data['requestType'] = 'getExams';
	$data['ucid'] = $_SESSION['ucid'];
	$url = "https://web.njit.edu/~dn236/CS490/beta/getExams.php";
	
	$ch = curl_init($url);
	$payload = json_encode($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($result, true);
	echo var_dump($json);
?>
<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="styles.css">
		<script>
			function exam(id) {
				$_SESSION['examId'] = id;
				location.href = '';
			}
			
			function releaseExam(name) {
				var id = name.substr(1);
				let formData = new FormData();
				formData.append('requestType', 'releaseExam');
				formData.append('examId', id);
				// cURL to middle end
				fetch("https://web.njit.edu/~dn236/CS490/beta/releaseExams.php", {
					method: "POST",
					body: formData
				})
				.then((response) => {
					console.log(response);
					response.json().then((data) => {
						if (data["message"] == "Success") {
							// Redirect back after successful submission
							location.href = 'https://web.njit.edu/~dn236/CS490/beta/teacherView.php'
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
				<h1> Exams </h1>
			</div>
			<?php
				for ($i = 0; $i < count($json); $i++) {
					echo "<div class='flex-container row'>";
					echo "<button type='button' id='".$json[$i]."' style='height: 40px; width: 150px' onclick='exam(this.id)'>Exam ".$json[$i]."</button>";
					echo "<button type='button' id=r'".$json[$i]."' style='height: 40px; width: 150px' onclick='releaseExam(this.id)'>Release Exam ".$json[$i]."</button>";
					echo "</div>";
				}
			?>
		</div>
		
	</body>
</html>
<?php ob_flush();?>