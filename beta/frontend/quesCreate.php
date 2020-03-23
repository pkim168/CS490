<?php 
		session_start([
		'use_only_cookies' => 1,
		'cookie_lifetime' => 0,
		'cookie_secure' => 1,
		'cookie_httponly' => 1
	]);

		
	if(array_key_exists('role', $_SESSION)){
    echo "<script> console.log('Session exists') </script>";
		if($_SESSION["role"] == 1)
			header('Location: ./student.php');
	
	else{
    echo "<script> console.log('Session does not exist') </script>";
    header('Location: ./index.php');
	}
	
	ob_start();
?>
<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="styles.css">
		<script>
			function submit(){
				formData.append('requestType', 'newQuestion');
				formData.append('ucid', document.getElementById("ucid").innerText);
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
							alert(''.concat("There was a problem submitting the question. Please try again. Error message: ", data['error']));
						}
					})
				})
				.catch(function(error) {
					console.log(error);
				});
				return;
			}
			
			function filter() {
				var diff = document.getElementById("difficulty").value;
				var tag = document.getElementById("tag").value;
				let formData = new FormData();
				formData.append('requestType', 'getQuestions');
				if (diff != "") {
					formData.append('difficulty', diff);
				}
				if (tag != "") {
					formData.append('tag', tag);
				}
				for (var p of formData) {
				  console.log(p);
				}
				// cURL to middle end
				fetch("********LINK HERE********", {
					method: "POST",
					body: formData
				})
				.then((response) => {
					console.log(response);
					response.json().then((data) => {
						var questions = document.getElementById('questions')
						while (questions.firstChild()) {
							questions.removeChild(questions.firstChild());
						}
						var count = Object.keys(data).length;
						for (var i=0; i<count; i++) {
							var tr = document.createElement('tr');
							tr.setAttribute("id", data[i][questionId]);
							var td = document.createElement('tr');
							td.textContent = data[i][question];
							tr.appendChild(td);
							var tb = document.createElement('tr');
							var button = document.createElement('button');
							button.setAttribute("type", "button");
							button.setAttribute("onclick", "add(this.closest('tr'))");
							button.textContent = "Add";
							tb.appendChild(button);
							tr.appendChild(tb);
							document.getElementById('questions').appendChild(tr);
						}
					})
				})
				.catch(function(error) {
					console.log(error);
				});
				return;
			}
			
			
			function back(){
				// Go back to previous page
				location.href = '********LINK HERE********';
				return;
			}
		</script>
	</head>
	<body>
		<?php
			echo "<p id='ucid' hidden></p>";
		?>
		<div class="flex-container column" style="width: 50%; margin: 0%; float:left; border-right: 1px black solid;">
			<div class="flex-container column" style="margin: 0%; float:left;">
				<div class="flex-container row">
					<h1> New Question </h1>
				</div>
			</div>
			<div class="flex-container row" style="width:98%; float:left">
				<table id="equestions" style="width:100%">
					<tr>
						<th> Question </th>
						<th> Points </th>
					</tr>
				</table>
			</div>
			<div class="flex-container row">
				<button type="button" style="height: 40px; width: 150px" onclick="submit()">Create Question</button>
				<button type="button" style="height: 40px; width: 150px" onclick="back()">Back</button>
			</div>
		</div>
		<div class="flex-container column" style="width: 50%; margin: 0%; float:right; border-left: 1px black solid;">
			<div class="flex-container column" style="margin: 0%; float:left;">
				<div class="flex-container row">
					<h1> Question Bank </h1>
				</div>
			</div>
			<div id="filters" class="flex-container row" style="width:100%; float:left">
				<label> &nbsp Difficulty: </label>
				<select id="difficulty">
					<option value="" selected></option>
					<option value="Easy">Easy</option>
					<option value="Medium">Medium</option>
					<option value="Hard">Hard</option>
				</select>
				<!--- Values will be changed --->
				<label> &nbsp Tag: </label>
				<select id="tag">
					<option value="" selected></option>
					<option value="Operations">Operations</option>
					<option value="test">test</option>
				</select>
				<button type="button" style="height: 27px; width: 80px" onclick="filter()">Filter</button>
			</div>
			<div class="flex-container row" style="width:98%; float:left">
				<table id="questions" style="width:100%">
					<tr>
						<th> Question </th>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
<?php ob_flush();?>