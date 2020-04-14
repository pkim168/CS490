<?php
	include("account.php");
 
  //Connects to database using account.php
	$db = mysqli_connect($hostname, $username, $password, $project);
	
	if(mysqli_connect_errno())
	{
		exit();
	}
	mysqli_select_db($db, $project);
?>