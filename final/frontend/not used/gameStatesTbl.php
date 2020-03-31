<?php
//GET

	//Displays a table of all Game States recorded for the user. Return t if successful
	function showGameStates($user_id){
    global $db;
    
    $query = 
    "SELECT gameStatesTbl.record_number, gameStatesTbl.record_date, gameStatesTbl.user_id, tbl2.user_name, gameStatesTbl.level_completed, gameStatesTbl.score_earned, gameStatesTbl.comments, gameStatesTbl.complexity FROM gameStatesTbl
    INNER JOIN
    (SELECT user_id, user_name FROM userTbl WHERE user_id = '$user_id') as tbl2
    ON gameStatesTbl.user_id = tbl2.user_id;";
    $result = mysqli_query($db, $query);
    if($result->num_rows == 0){
      echo "<script> console.log('No game states for this user') </script>";
      exit();
    }
    
    echo "<table>";
    echo "<tr><th>Record Number</th><th>Date</th><th>User ID</th><th>User Name</th><th>Level Completed</th><th>Score Earned</th><th>Comments</th><th>Complexity</th></tr>";
  
    while($row = mysqli_fetch_array($result)) {
        $record_number = $row["record_number"];
        $record_date = $row["record_date"];
        $user_id = $row["user_id"];
        $user_name = $row["user_name"];
        $level_completed = $row["level_completed"];
        $score_earned = $row["score_earned"];
        $comments = $row["comments"];
        $complexity = $row["complexity"];
        echo "<tr><td>".$record_number."</td><td>".date('l, j, M Y, g:i A', strtotime($record_date))."</td><td>".$user_id."</td><td>".$user_name."</td><td>".$level_completed."</td><td>".$score_earned."</td><td>".$comments."</td><td>".$complexity."</td></tr>";
    } 
    echo "</table>";
    return;
  }
	
  //Displays a table containing the user's most recent game state (the most recent record date, level completed, and inventory). Returns t if successful
	function display($user_id, &$number/*, &$output*/){
		if(!isset($user_id) || !isset($number)){
      echo "<script>console.log('Error. user_id or number is empty')</script>";
			exit();
		}
			
		global $db;
		$query = "
			SELECT * FROM 
				(SELECT userTbl.user_id, userTbl.user_name, userTbl.score, gameStates.level_completed, gameStates.record_date
				FROM userTbl
				INNER JOIN 
					(SELECT level_completed, record_date, user_id
					FROM gameStatesTbl
					WHERE user_id = $user_id AND level_completed > 0
					ORDER BY record_date DESC
					LIMIT 1
					) AS gameStates
				ON userTbl.user_id = gameStates.user_id
				) AS userState
			CROSS JOIN
				(SELECT userInventory.quantity, itemTbl.* 
				FROM
					(SELECT item_id, quantity
					FROM userInventoryTbl
					WHERE user_id = $user_id
					) AS userInventory
				INNER JOIN itemTbl
				ON userInventory.item_id = itemTbl.item_id
				) AS userInventory;
		";
		
    $result = mysqli_query($db, $query);
    if($result->num_rows == 0){
			
			$query = "
				SELECT * FROM userTbl
				WHERE user_id = '$user_id';
			";
			
			$result = mysqli_query($db, $query);
			if($result->num_rows == 0){
				echo "<label style='width:550px;'> User does not exist. No data</label>";
				exit();
			}
			
			echo "<script>console.log('User exists')</script>";
			
			$row = mysqli_fetch_array($result);
			$user_name = $row["user_name"];
			$score = $row["score"];
			$record_date = "N/A";
			$level_completed = "0";
			
			echo "<table>";
			echo "<tr><th colspan='2'>User ID: ".$user_id."</th><th colspan='3'>Username: ".$user_name."</th></tr>";
			echo "<tr><th colspan='4'>Record Date: ".$record_date."</th><th colspan='1'>Score: ".$score."</th></tr>";
			echo "<tr><th colspan='3'>Level Completed: ".$level_completed."</th><th colspan='2'>Number of rows: ".$number."</th></tr>";
			echo "<tr><th>Item ID</th><th>Item Name</th><th>Description</th><th>Quantity</th><th>Value of Single Item</th></tr>";
			echo "<tr><td colspan='5'>No Inventory</td></tr>";
			echo "</table>";
      return "t";
    }
		
		$number = $number + $result->num_rows;
		
		$row = mysqli_fetch_array($result);
		$user_name = $row["user_name"];
		$score = $row["score"];
		$record_date = $row["record_date"];
		$level_completed = $row["level_completed"];
		
		echo "<table>";
		echo "<tr><th colspan='2'>User ID: ".$user_id."</th><th colspan='3'>Username: ".$user_name."</th></tr>";
		echo "<tr><th colspan='4'>Record Date: ".$record_date."</th><th colspan='1'>Score: ".$score."</th></tr>";
		echo "<tr><th colspan='3'>Level Completed: ".$level_completed."</th><th colspan='2'>Number of rows: ".$number."</th></tr>";
		echo "<tr><th>Item ID</th><th>Item Name</th><th>Description</th><th>Quantity</th><th>Value of Single Item</th></tr>";
		
		do {
				$item_id = $row["item_id"];
				$item_name = $row["item_name"];
				$item_description = $row["item_description"];
				$quantity = $row["quantity"];
				$value = $row["value"];
				echo "<tr><td>".$item_id."</td><td>".$item_name."</td><td>".$item_description."</td><td>".$quantity."</td><td>".$value."</td></tr>";
		} while($row = mysqli_fetch_array($result));
		echo "</table>";
		return "t";
	}
	
	//Gets the sum of all scores in gameStatesTbl and returns the value if successful, f if failed
	function getTotalScore($user_id){
		if(!isset($user_id)){
      echo "<script>console.log('Error. user_id is empty')</script>";
		}
		global $db;
	
		$query = "
			SELECT SUM(score_earned) AS total FROM gameStatesTbl
			WHERE user_id = $user_id;
		";
		
		$result = mysqli_query($db, $query);
		if($result->num_rows == 0){
			echo "<script>console.log('No output. User does not exist')</script>";
			return "f";
		}
		
		$row = mysqli_fetch_array($result);
		$total = $row["total"];
		return $total;
	}

//POST
	//Inserts a new tuple into gameStatesTbl. Returns t if successful, f if failed
	function insertGameState($record_date, $user_id, $level_completed, $score_earned, $comments, $complexity, $bonus_points){
		if(!isset($record_date) || !isset($user_id) || !isset($level_completed) || !isset($score_earned)){
      echo "<script>console.log('Error. A value is empty')</script>";
			return "f";
		}
		if(empty($comments)){
			$comments = "NULL";
		}
		if(empty($complexity)){
			$complexity = 0;
		}
		if(empty($bonus_points)){
			$bonus_points = 0;
		}
    global $db;
    
    $query = "
			INSERT INTO gameStatesTbl VALUES
			(NULL, '$record_date', $user_id, $level_completed, $score_earned, '$comments', $complexity, $bonus_points);
		";
		
		$result = mysqli_query($db, $query);
		if($result){
			echo "<script> console.log('Successfully inserted game state')</script>";
		}
		else {
			echo "Failed to insert game state: " . mysqli_error($db);
			return "f";
		}
		return "t";
	}
	
?>