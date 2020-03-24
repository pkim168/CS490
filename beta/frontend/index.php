<?php
	session_start([
        'use_only_cookies' => 1,
        'cookie_lifetime' => 0,
        'cookie_secure' => 1,
        'cookie_httponly' => 1
    ]);
	
	
	if(array_key_exists('role', $_SESSION)){
		if($_SESSION["role"] == 1)
			header('Location: ./studentView.php');
		if($_SESSION["role"] == 2)
			header('Location: ./teacherView.php');
	}
	ob_start();
?>
<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="styles.css">
		<script>
			function login(){
				var ucid = document.getElementById("ucid").value;
				var pass = document.getElementById("pass").value;
				if(ucid == "" || pass == ""){
					return;
				}
				let formData = new FormData();
				formData.append('requestType', 'login');
				formData.append('ucid', ucid);
				formData.append('pass', pass);
				
				fetch("https://web.njit.edu/~dn236/CS490/beta/login.php", {
					method: "POST",
					body: formData
				})
				.then((response) => {
					console.log(response);
					response.json().then((data) => {
						if(data["message"] == "Verified") {
							
							if(data['role']==  1 ){
								<?php  
								$_SESSION['role'] = 1;
								header('Location: ./studentView.php');
								?>
								
							}else{
								<?php  
								$_SESSION['role'] = 2;
								header('Location: ./teacherView.php');
								?>
							}
						}
						else {
							alert("Invalid Login");
						}
					})
				})
				.catch(function(error) {
					console.log(error);
				});
			}
		</script>
	</head> 
	<body>
		<div id="div" class="flex-container column" style="margin: 0%;">
			<form style="align-content: space-around; width:240px; float:left; padding: 5px 10px;" class="flex-container column" >
				<div class="flex-container column" style="margin: 0%; float:right;">
					<div class="flex-container row">
						<label style="width: 120px;">UCID:</label>
						<input style="width: 120px;" type="text" id="ucid" name="ucid" placeholder="Required field" autofocus required>
					</div>
					<div class="flex-container row">
						<label style="width: 120px;">Password:</label>
						<input style="width: 120px;" type="password" id="pass" name="pass" placeholder="Required field" required>
					</div>
					<div class="flex-container row">
						  <button type="button" style="height: 40px; width: 150px" onclick="login()">Login</button>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
<?php ob_flush()?>