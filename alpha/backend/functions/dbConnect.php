<?php
	include("../../../../config/account.php");
 
  //Connects to database using account.php
	$db = mysqli_connect($hostname, $username, $password, $project);
	
	if(mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error ( );
		exit();
	}
	echo "<script>console.log('Successfully connected to MySQL.')</script>";
	mysqli_select_db($db, $project);
?>