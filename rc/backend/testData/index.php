<?php
	session_start([
		'use_only_cookies' => 1,
		'cookie_lifetime' => 0,
		'cookie_secure' => 1,
		'cookie_httponly' => 1
	]);
	
	//If a session with user_id exists, go to updateInventory.php
	if(array_key_exists('user_id', $_SESSION)){
    echo "<script> console.log('Session exists') </script>";
    header('Location: ./updateInventory.php');
	}
	
	//If a session with user_id does not exist, go to login.php
	else{
    echo "<script> console.log('Session does not exist') </script>";
    header('Location: ./login.php');
	}
?>



			session_unset();
			session_destroy();