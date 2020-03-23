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
					<h2> Graded Exams </h2>
				</div>
			</div>
			<div id="filters" class="flex-container row" style="width:100%; float:left">
		</div>
	</body>
</html>