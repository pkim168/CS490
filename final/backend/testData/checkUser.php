<?php
	include("./startSession.php");
	include("../routes/post.php");
	ob_start();
 
	//Checks if user_name or password is empty
	if (empty($_POST["user_name"])){
    echo "<script> console.log('User Name not given') </script>";
    header('Location: ../login.php');
		exit();
	}
	if (empty($_POST["password"])){
    echo "<script> console.log('Password not given') </script>";
    header('Location: ../login.php');
		exit();
	}
	
  //Gets data from POST
	$user_name = getData($_POST["user_name"]);
	echo "<script> console.log('User Name set') </script>";
	$password = getData($_POST["password"]);
	echo "<script> console.log('Password set') </script>";
	
	//Checks if user exists. If it does not, goes to newAccount.php, if it does, returns user_id
	$user_id = find_user($user_name);
	if($user_id == "f"){
		header('Location: ../newAccount.php');
		exit();
	}
	
  //Checks if the user_name, password combination exists. If it does, adds user_id and user_name to session and goes to updateInventory.php, else goes to newAccount.php
	if(check_user($user_name, $password)){
		$_SESSION['user_id'] = $user_id;
		$_SESSION['user_name'] = $user_name;
		header('Location: ../updateInventory.php');
		exit();
	}
	else{
		echo "<script> alert('Incorrect Password');</script>";
		header('Location: ../newAccount.php');
		exit();
	}
	
	
?>
