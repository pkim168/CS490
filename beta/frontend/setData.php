<?php
	session_start([
        'use_only_cookies' => 1,
        'cookie_lifetime' => 0,
        'cookie_secure' => 1,
        'cookie_httponly' => 1
    ]);
	$ucid = $role = "";
	if (!empty($_POST['ucid'])){
		$ucid = $_POST["ucid"];
	}if (!empty($_POST['role'])){
		$role = $_POST["role"];
	}
	
	$_SESSION['ucid'] = $ucid;
	$_SESSION['role'] = $role;
	header("Location: ".$_SESSION['link']);