<?php
	include("../routes/post.php");
	include("./startSession.php");
	
	//Checks if fields are empty and gets data from url
	//If action field is empty, sends back to updateInventory.php

	if(empty($_SESSION["user_id"])){
    echo "<script> console.log('No user_id given') </script>";
    header('Location: ../login.php');
		exit();
	}
	else{
		$user_id = getData($_SESSION["user_id"]);
	}
	if (empty($_GET["action"])){
    echo "<script> console.log('No action given') </script>";
    header('Location: ../updateInventory.php');
		exit();
	}
	if (empty($_GET["item_id"])){
		$item_id = "0";
	}
	else{
		$item_id = getData($_GET["item_id"]);
	}
	if (empty($_GET["quantity"])){
		$quantity = "0";
	}
	else{
		$quantity = getData($_GET["quantity"]);
	}
	
	$action = getData($_GET["action"]);
	
	switch ($action){
		
		case "Show Game States":
			showGameStates($user_id);
			break;
			
		case "Log Out":
			//ends session and redirects to index.php
			session_unset();
			session_destroy();
			header('Location: ../index.php');
			break;
			
		case "Play Game":
			header('Location: ../snake/game.php');
			break;
		
		case "+":
			if($quantity == "0" || $item_id == "0"){
				echo "<script> console.log('No quantity or item_id given') </script>";
				header('Location: ../updateInventory.php');
				exit();
			}
			
			$f = addToInventory($user_id, $item_id, $quantity);
			if($f == "f"){
				echo "<script> alert('Not enough points or item does not exist. You will be sent back to the Inventory Page.'); location.href = '../updateInventory.php';</script>";
				exit();
			}
			
			echo "<script> alert('Successfully bought. You will be sent back to the Inventory Page.'); location.href = '../updateInventory.php';</script>";
				exit();
			break;
			
		case "-":
			if($quantity == "0" || $item_id == "0"){
				echo "<script> console.log('No quantity or item_id given') </script>";
				header('Location: ../updateInventory.php');
				exit();
			}
			
			$f = removeFromInventory($user_id, $item_id, $quantity);
			
			if($f == "f"){
				echo "<script>console.log('inventoryUpdate.php/case - failed')</script>";
				echo "<script> alert('You don\'t have enough of that item to sell. You will be sent back to the Inventory Page.'); location.href = '../updateInventory.php';</script>";
				exit();
			}
			
			echo "<script> alert('Successfully Sold. You will be sent back to the Inventory Page.'); location.href = '../updateInventory.php';</script>";
			exit();
			break;
	}
?>

<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" href="../styles.css">
		<script>
			function back(){
					location.href = '../updateInventory.php';
			}
		</script>
	</head>
	<body>
		<button onclick="back()">Go Back</button>
	</body>
</html>