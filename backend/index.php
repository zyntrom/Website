<?php

    session_start();
    //Handles Registation
    function register(){

        if($_SERVER['REQUEST_METHOD']=='POST'){
            $regusername=filter_input(INPUT_POST,'username',FILTER_SANITIZE_SPECIAL_CHARS);
            $regpassword=filter_input(INPUT_POST,'password',FILTER_SANITIZE_SPECIAL_CHARS);
            $regemail=filter_input(INPUT_POST,'email',FILTER_SANITIZE_SPECIAL_CHARS);
            $hash=password_hash($regpassword,PASSWORD_DEFAULT);
            if(isset($regusername)){   
                //Add the new user
                $sql="insert into users (username, email, password) values ('$regusername','$regemail','$hash')";
                try{	
                    include("database.php");
                    mysqli_query($conn,$sql);
                    $_SESSION["username"]=$regusername;
                    $_SESSON['role']='user';
                    setcookie("session_id", session_id(), time() + 2592000, "/");
                    header('Location: ../frontend/dashboard.html');
                    exit;
                }
                catch(mysqli_sql_exception $e){
                    echo"Could not Register";
                }
            }
            else{
                echo "please input username";
            }
	    }

    }
    //handles Login
    function login(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $logusername=$_POST['logusername'];
            $logpassword=$_POST['logpassword'];
            if(isset($logusername)){
                if (empty($logusername) || empty($logpassword)) {
                    echo "Please fill in both fields.";
                    return;
                }
                //Gets the user from the database
                $sql="select * from users where username = '$logusername'";
                try{
                    include("database.php");
                    $result=mysqli_query($conn,$sql);
                }	
                catch(mysqli_sql_exception $e){
                    echo"Could not check";
                }
                //Confirms Password
                if(mysqli_num_rows($result)>0){
                    $row=mysqli_fetch_assoc($result);
                    if(password_verify($logpassword,$row["password"])){
                        $_SESSION["username"]=$row['username'];
                        $_SESSION['id']=$row['id'];
                        $_SESSION['role']=$row['role'];
                        
                        setcookie("session_id", session_id(), time() + 2592000, "/");
                        header("Location: ../frontend/dashboard.html");
                        exit;
                    }
                    else{
                        echo"Wrong password";	
                    }
                }
                else{
                    echo"User Not found";
                }
            }
        }
    }
   
    //Checks whether login or register button is pressed
    if(isset($_POST['register'])){
        register();
    }
    elseif(isset($_POST['login'])){
        login();
    }
    $role = array(
        'role' => $_SESSION['role']
    );

   
    if(isset($_SESSION['role'])){
        header("Content-Type: application/json");
        echo json_encode($role);
    }

    mysqli_close($conn);


?>