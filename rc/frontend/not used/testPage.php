<html>
	<head>
    <!-- When submitted, the form opens checkUser.php. user_name and password are required elements and the form cannot be submitted without them-->
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="styles.css">
	</head> 
	<body>
		<form style="align-content: space-around; width:240px; float:left; padding: 5px 10px;" class="flex-container column" method="post" action="./login.php">
			<div class="flex-container column" style="margin: 0%; float:right;">
				<div class="flex-container row">
					<label style="width: 120px;">UCID:</label>
					<input style="width: 120px;" type="text" name="ucid" placeholder="Required field" autofocus required>
				</div>
				<div class="flex-container row">
					<label style="width: 120px;">Password:</label>
					<input style="width: 120px;" type="password" name="pass" placeholder="Required field" required>
				</div>
				<div class="flex-container row">
					  <input style="height: 40px; width: 150px" type="submit" value="Login">
				</div>
			</div>
		</form>
	</body>
</html>