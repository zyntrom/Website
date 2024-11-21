<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include("database.php");

function gettinguser($logusername,$logpassword){
    global $conn;
    
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $logusername);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($logpassword, $row['password'])) {
            $_SESSION["username"] = $row['username'];
            $_SESSION['id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['email']=$row['email'];
            $_SESSION['dateandtime']=$row['dateandtime'];

            setcookie("session_id", session_id(), time() + 2592000, "/");
            header("Location: ../frontend/dashboard.html");
            exit;
        } else {
            echo "Wrong password.";
        }
    } else {
        echo 'usernot found';  
    }
    $stmt->close();
}
function addinguser($regusername, $regemail, $hash){
    global $conn;
    
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $regusername, $regemail, $hash);

    if ($stmt->execute()) {
        
        $_SESSION['role'] = 'user';
        setcookie("session_id", session_id(), time() + 2592000, "/");
        header('Location: ../frontend/dashboard.html');
        exit;
    } else {
        echo "Could not register: " . $conn->error;
    }
    $stmt->close();
}

function register() {
     
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $regusername = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $regpassword = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
        $regemail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        
        if (empty($regusername) || empty($regpassword) || empty($regemail)) {
            echo "All fields are required.";
            return;
        }

        
        $hash = password_hash($regpassword, PASSWORD_DEFAULT);
        addinguser($regusername, $regemail, $hash);
        gettinguser($regusername,$hash);
        //header("Location: ../frontend/dashboard.html");
    }
}

function login() {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $logusername = filter_input(INPUT_POST, 'logusername', FILTER_SANITIZE_SPECIAL_CHARS);
        $logpassword = filter_input(INPUT_POST, 'logpassword', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($logusername) || empty($logpassword)) {
            echo "Please fill in both fields.";
            return;
        }
        gettinguser($logusername,$logpassword);
        //header("Location: ../frontend/dashboard.html");
        }
        
    }



if (isset($_POST['register'])) {
    register();
} elseif (isset($_POST['login'])) {
    login();
}


if (isset($_SESSION['role'])) {
    header("Content-Type: application/json");
    $result = [
        'role' => $_SESSION['role'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'dateandtime' => $_SESSION['dateandtime'],
    ];
    echo json_encode($result);
}

mysqli_close($conn);
?>
