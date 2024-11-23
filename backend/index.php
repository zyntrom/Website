<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header("Content-Type: application/json");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include("database.php");

function fetchuser ($logusername,$logpassword){
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

            $response = [
                'role' =>$_SESSION['role'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'dateandtime' => $_SESSION['dateandtime'],
                'status' => 'success'
            ];
            setcookie('user_data', json_encode($response), time() + (86400 * 30), "/");
            echo json_encode($response);
            exit;
        } else {
            
            echo json_encode(['error' => 'WrongPass']);
            exit;
        }
    } else {
        
        echo json_encode(['error' => 'NotFound']);
        exit;
    }
    $stmt->close();
}
function adduser($regusername, $regemail, $hash){
    global $conn;
    
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $regusername, $regemail, $hash);
    
    try {
       
        $stmt->execute();
        $_SESSION['role'] = 'user';
        
        
    } catch (mysqli_sql_exception $e) {
        
        if ($e->getCode() == 1062) {
            header("Content-Type: application/json");
            echo json_encode(['error' => 'Duplicate entry']);
        } else {
            header("Content-Type: application/json");
            echo json_encode(['error' => 'Database error']);
        }
        exit;
    } finally {
        
        $stmt->close();
    }
}
function cookie(){
    if (isset($_COOKIE['user_data'])) {
        $userData = json_decode($_COOKIE['user_data'], true);
        echo json_encode($userData);
    } else {
        echo json_encode(['status' => 'none']);
    }
    
}

function register() {
     
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $regusername = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $regpassword = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
        $regemail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        
        if (empty($regusername) || empty($regpassword) || empty($regemail)) {
            echo json_encode(['error' => 'MissingFields']);
            exit;
        }
        $hash = password_hash($regpassword, PASSWORD_DEFAULT);
        adduser($regusername, $regemail,$hash);
        fetchuser($regusername,$regpassword);
        cookie();
        exit;
        
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
        fetchuser($logusername,$logpassword);
        cookie();
        //header('Location: ../frontend/dashboard.html');
        exit;
        }
        
}




if (isset($_POST['username']) && isset($_POST['password'])) {
    register();
    
} elseif (isset($_POST['logusername']) && isset($_POST['logpassword'])) {
    login();
    
}

cookie();


mysqli_close($conn);
?>
