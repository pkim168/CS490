<?php 
	// If session doesn't exists, redirect to login page
	
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
?>
<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="styles.css">
		<script>
			function submit(){
				var table = document.getElementById("equestions");
				if (table.rows.length <= 1) {
					alert("You have no questions in this exam");
					return;
				}
				var score = 0;
				let formData = {};
				formData['questions'] = []
				for (var i=1; i<table.rows.length; i++) {
					let question = {};
					var questionId = table.rows[i].substr(1);
					var points = table.rows[i].children[1].firstChild.value;
					if (points == "" || isNaN(points)) {
						alert("All questions must have a numerical points value");
						return;
					}
					score += Number(table.rows[i].children[1].firstChild.value);
					question['questionId'] = questionId;
					question['points'] = points;
					formData['questions'].push(question);
				}
				formData['requestType'] = 'createNewExam';
				formData['ucid'] = document.getElementById("ucid").innerText;
				formData['totalPoints'] = score;
				// cURL to middle end
				fetch("https://web.njit.edu/~dn236/CS490/beta/CreateExam2makeExam.php", {
					method: "POST",
					body: JSON.stringify(formData)
				})
				.then((response) => {
					console.log(response);
					response.json().then((data) => {
						if (data["message"] == "Success") {
							// Redirect back after successful submission
							location.href = 'https://web.njit.edu/~dn236/CS490/beta/teacherView.php';
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
			
			function filter() {
				var diff = document.getElementById("difficulty").value;
				var tag = document.getElementById("tag").value;
				let formData = new FormData();
				formData.append('requestType', 'getQuestions');
				formData.append('difficulty', diff);
				formData.append('tag', tag);
				for (var p of formData) {
				  console.log(p);
				}
				// cURL to middle end
				fetch("https://web.njit.edu/~dn236/CS490/beta/CreateExam2getQuestions.php", {
					method: "POST",
					body: formData
				})
				.then((response) => {
					console.log(response);
					response.json().then((data) => {
						var questions = document.getElementById('questions');
						console.log(questions.childNodes.length);
						while (questions.childNodes.length > 2) {
							questions.removeChild(questions.lastChild);
						}
						if (data.hasOwnProperty('message')) {
							console.log(data['error']);
							return;
						}
						var count = Object.keys(data).length;
						for (var i=0; i<count; i++) {
							var tr = document.createElement('tr');
							tr.setAttribute("id", data[i]['questionId']);
							var td = document.createElement('td');
							td.textContent = data[i]['question'];
							tr.appendChild(td);
							var tb = document.createElement('td');
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
			
			function add(question) {
				if (document.getElementById('q'.concat(question.id))) {
					alert("You already added this question");
					return;
				}
				var tr = document.createElement('tr');
				tr.setAttribute("id", 'q'.concat(question.id));
				var td = document.createElement("td");
				td.textContent = question.children[0].innerText;
				tr.appendChild(td);
				td = document.createElement("td");
				var points = document.createElement("input");
				td.appendChild(points);
				tr.appendChild(td);
				td = document.createElement("td");
				var button = document.createElement("button");
				button.setAttribute("type", "button");
				button.setAttribute("style", "height: 40px; width: 150px");
				button.setAttribute("onclick", "this.closest('tr').remove()");
				button.textContent = "Delete";
				td.appendChild(button);
				tr.appendChild(td);
				document.getElementById('equestions').appendChild(tr);
				return;
			}
			
		</script>
	</head>
	<body>
		<?php
			echo "<p id='ucid' hidden>{$_SESSION['ucid']}</p>";
		?>
		<div class="flex-container column" style="width: 50%; margin: 0%; float:left; border-right: 1px black solid;">
			<div class="flex-container column" style="margin: 0%; float:left;">
				<div class="flex-container row">
					<h1> New Exam </h1>
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
				<button type="button" style="height: 40px; width: 150px" onclick="submit()">Create Exam</button>
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