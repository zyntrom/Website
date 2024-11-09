<?php
	session_start();
	
	if(isset($_SESSION['id'])){
		echo"Welocome ".$_SESSION['username']."<br>";
	}
	elseif(isset($_COOKIE['session_id'])){

		session_id($_COOKIE['session_id']);
		session_start();
	
		include("database.php");
		$username = mysqli_real_escape_string($conn, $_SESSION['username']);
		$sql="select * from users where username = '$username'";
		try{
			$result=mysqli_query($conn,$sql);
			if($result && mysqli_num_rows($result) > 0){
				$row=mysqli_fetch_assoc($result);
				echo"Welcome".$row['username'];	
		}
		else{
			echo"User not found";
		}
		}catch(mysqli_sql_exception $e){
			echo"failed to retrive session";
		}
	}
	else{
		echo"Please relogin";
	}
	

?>
