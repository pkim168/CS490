<?php
	session_start([
		'use_only_cookies' => 1,
		'cookie_lifetime' => 0,
		'cookie_secure' => 1,
		'cookie_httponly' => 1
	]);
	

	if(array_key_exists('ucid', $_SESSION)){
    echo "<script> console.log('Session exists') </script>";
		if($_SESSION["role"] == 2)
			header('Location: ./teacherView.php');
	}else{
    echo "<script> console.log('Session does not exist') </script>";
    header('Location: ./index.php');
	}
	
	
?>
<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="styles.css">
		<script>
			function takeExam(){
				
			}
			
			
		</script>
	</head> 
	<body>
		<?php
			echo "<p id='ucid' hidden></p>";
		?>
		<div class="flex-container column" style="margin: 0%;">
			<div class="flex-container column" style="margin: 0%; float:left;">
				<div class="flex-container row">
					<h2> Exams </h2>
				</div>
			</div>
			<div  class="flex-container row" style="width:100%; float:left">
				<table id="graded" style = "width:100%; float:left">
					<tr> 
						<th> Graded Exams </th>
						<th> Grade </th>
						<?php
						$data = array();
						$data['requestType'] = "getStudentExams";
						$data['ucid'] = $_SESSION['ucid'];
						$url = "https://web.njit.edu/~jrd62/CS490/student_middle.php"
						
						$ch = curl_init($url);
						$payload = json_encode($data);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$result = curl_exec($ch);
						curl_close($ch);
						$json = json_decode($result, true);
						for($i=0;i<count($result);i++){
							
					</tr>
				</table>
			</div>
			<div  class="flex-container row" style="width:100%; float:left">
				<table id="graded" style = "width:100%; float:left">
					<tr> 
						<th> Avaiable Exams </th>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>