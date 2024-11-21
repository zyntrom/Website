<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection
include("database.php");

function register() {
    global $conn; // Use the global connection
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $regusername = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $regpassword = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
        $regemail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        // Validate input
        if (empty($regusername) || empty($regpassword) || empty($regemail)) {
            echo "All fields are required.";
            return;
        }

        // Hash password
        $hash = password_hash($regpassword, PASSWORD_DEFAULT);

        // Prepared statement to avoid SQL injection
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $regusername, $regemail, $hash);

        if ($stmt->execute()) {
            $_SESSION["username"] = $regusername;
            $_SESSION['role'] = 'user';
            setcookie("session_id", session_id(), time() + 2592000, "/");
            header('Location: ../frontend/dashboard.html');
            exit;
        } else {
            echo "Could not register: " . $conn->error;
        }
        $stmt->close();
    }
}

function login() {
    global $conn; // Use the global connection
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $logusername = filter_input(INPUT_POST, 'logusername', FILTER_SANITIZE_SPECIAL_CHARS);
        $logpassword = filter_input(INPUT_POST, 'logpassword', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($logusername) || empty($logpassword)) {
            echo "Please fill in both fields.";
            return;
        }

        // Prepared statement to avoid SQL injection
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

                setcookie("session_id", session_id(), time() + 2592000, "/");
                header("Location: ../frontend/dashboard.html");
                exit;
            } else {
                echo "Wrong password.";
            }
        } else {
            echo "User not found.";
        }
        $stmt->close();
    }
}

// Check which button was pressed
if (isset($_POST['register'])) {
    register();
} elseif (isset($_POST['login'])) {
    login();
}

// Return session role as JSON if set
if (isset($_SESSION['role'])) {
    header("Content-Type: application/json");
    echo json_encode(['role' => $_SESSION['role']]);
}

mysqli_close($conn);
?>
