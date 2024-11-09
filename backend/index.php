<?php 
	include("database.php");
	session_start();	
	$username=$_POST['username'];
	$password=$_POST['password'];
	$email=$_POST['email'];
	$hash=password_hash($password,PASSWORD_DEFAULT);
	$logusername=$_POST['logusername'];
	$logpassword=$_POST['logpassword'];
	echo"test";
	if(isset($username)){
		$sql="insert into users (username, email, password) values ('$username','$email','$hash')";
		
		try{	
			mysqli_query($conn,$sql);
			echo"User is registerd";
			header('Location: ../frontend/dashboard.html');
			exit;
		}
		catch(mysqli_sql_exception $e){
			echo"Could not Register";
		}
	}
	elseif(isset($logusername)){
		$sql="select * from users where username = '$logusername'";
		try{
			
			$result=mysqli_query($conn,$sql);
		}	
		catch(mysqli_sql_exception $e){
			echo"Could not check";
		}
		if(mysqli_num_rows($result)>0){
			$row=mysqli_fetch_assoc($result);
			if(password_verify($logpassword,$row["password"])){
				$_SESSION["username"]=$row['username'];
				$_SESSION['id']=$row['id'];
				setcookie("session_id", session_id(), time() + 2592000, "/");
				header("Location: ../frontend/dashboard.html");
				exit;
			}
			else{
				echo"Wrong password";	
			}
		}
		else{
			echo"Not found";
		}
	}
	else{
		echo"Invalid";
	}
	mysqli_close($conn);
?>
