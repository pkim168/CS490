<?php 
	// If session doesn't exists, redirect to login page
	if(session_id() == '' || !isset($_SESSION)) {
		header('Location: ./index.php');
	} 
	session_start();
	if (empty($_SESSION['ucid']) || empty($_SESSION['role'])){
		header('Location: ./index.php');
	} 
	if ($_SESSION['role'] = '1') {
		header('Location: ./studentView.php');
	}*/
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
		?>
		<div class="flex-container column" style="width: 100%; margin: 0%; float:left; border-right: 1px black solid;">
			<div class="flex-container row">
				<h1> <?php echo "Welcome ".$_SESSION['ucid']?> </h1>
			</div>
			<div class="flex-container row">
				<button type="button" style="height: 40px; width: 150px" onclick="location.href = '***********************';">Create Exam</button>
			</div>
			<div class="flex-container row">
				<button type="button" style="height: 40px; width: 150px" onclick="location.href = '***********************';">See All Exams</button>
			</div>
			<div class="flex-container row">
				<button type="button" style="height: 40px; width: 150px" onclick="location.href = '***********************';">Create Question</button>
			</div>
			<div class="flex-container row">
				<button type="button" style="height: 40px; width: 150px" onclick="location.href = '***********************';">Log Out</button>
			</div>
		</div>
		
	</body>
</html>
<?php ob_flush();?>