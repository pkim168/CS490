<?php
	session_start([
		'use_only_cookies' => 1,
		'cookie_lifetime' => 0,
		'cookie_secure' => 1,
		'cookie_httponly' => 1
	]);
	
	//If a session with user_id exists, go to updateInventory.php
	if(array_key_exists('ucid', $_SESSION)){
    echo "<script> console.log('Session exists') </script>";
		if($_SESSION["role"] == 1)
			header('Location: ./student.php');
		if($_SESSION["role"] == 2)
			header('Location: ./teacher.php');
	}
	
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
				
				fetch("https://web.njit.edu/~dn236/CS490/Alpha/login.php", {
					method: "POST",
					body: formData
				})
				.then((response) => {
					console.log(response);
					response.json().then((data) => {
						if(document.getElementById('result') == null) {
							var p = document.createElement('p');
							p.textContent = "Backend: ".concat(data['backend'], " NJIT: ", data['njit']);
							p.setAttribute('id', 'result');
							document.getElementById('div').appendChild(p);
						}
						else {
							var result = document.getElementById('result');
							document.getElementById('div').removeChild(result);
							var p = document.createElement('p');
							p.textContent = "Backend: ".concat(data['backend'], " NJIT: ", data['njit']);
							p.setAttribute('id', 'result');
							
							document.getElementById('div').appendChild(p);
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