<?php
  //Trims and cleans data before it is used in queries
	function getData($input){
		global $db;
		$input = trim($input);
		$input = mysqli_real_escape_string($db, $input);
		return $input;
	}
?>