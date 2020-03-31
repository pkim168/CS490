<?php 
	include("./functions/startSession.php");
	include("./functions/dbConnect.php");
	include("./functions/supportFunctions.php");
	include("./routes/gameStatesTbl.php");
	//include("./routes/post.php");
	if (empty($_SESSION['user_id']) || empty($_SESSION['user_name'])){
    header('Location: ./index.php');
	}
	ob_start();
?>
<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="styles.css">
		<!--If + is clicked, adds to inventory, if - is clicked, removes from inventory, if Show Game States is clicked, shows all user's game states, if play is clicked, starts game, if log out is clicked, logs out-->
		<!--check() makes sure that quantity is a positive value-->
		<script>
			function check(){
				if(document.getElementById("quantity").value != "" && document.getElementById("quantity").value <= 0 ){
					alert("Quantity must be greater than 0");
					return false;
				}
			}
			
			function back(){
					location.href = './login.php';
			}
		</script>
	</head>
	<body>
		<div class="flex-container column" style="margin: 0%; border: 1px black solid;">
			<form class="borderless" style="text-align:center; align-content: space-around; width:550px; float:left; padding: 0px 10px;" method="get" action="./functions/inventoryUpdate.php" onsubmit="return check();">
				<div class="flex-container column" style="margin: 0%; float:right;">
					<div class="flex-container row">
						<?php 
							//Checks if user_name is stored in session
							if (!empty($_SESSION['user_name'])){
								$user_name = getData($_SESSION['user_name']);
								echo "<label style='width:550px;'>".$user_name."</label>";
							}
						?>
					</div>
					<div class="flex-container row">
						<?php 
							//Checks if user_id is stored in session
							if (empty($_SESSION['user_id'])){
								echo "<label style='width:550px;'>user_id not given. No data</label>";
							}
							
							//Prints table data
							else{
								$user_id = getData($_SESSION['user_id']);
								echo "<script> console.log('User ID set') </script>";
								$number = 0;
								display($user_id, $number);
							}
						?>
					</div>
					<div class="flex-container row">
						<label style="width: 210px;">Item ID:</label>
						<input style="width: 210px;" type="text" id="item_id" name="item_id" required autofocus/>
					</div>
					<div class="flex-container row">
						<label style="width: 210px;">Quantity:</label>
						<input style="width: 210px;" type="number" id="quantity" name="quantity" required/>
					</div>
					<div class="flex-container row">
						 <input style="height: 40px; width: 210px" type="submit" name="action" value="+"/>
						 <input style="height: 40px; width: 210px" type="submit" name="action" value="-"/>
					</div>
				</div>
			</form>
			<form class="borderless" style="text-align:center; align-content: space-around; width:420px; float:left; padding: 0px 10px;" method="get" action="./functions/inventoryUpdate.php" onsubmit="return check();">
				<div class="flex-container column" style="margin: 0%; float:right;">
					<div class="flex-container row">
						 <input style="height: 40px; width: 140px" type="submit" name="action" value="Show Game States"/>
						 <input style="height: 40px; width: 140px" type="submit" name="action" value="Log Out"/>
						 <input style="height: 40px; width: 140px" type="submit" name="action" value="Play Game"/>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>

<?php ob_flush();?>