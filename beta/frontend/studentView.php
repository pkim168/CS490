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
			header('Location: ./quesCreate.php');
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
		
	</body>
</html>