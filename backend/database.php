<?php

$db_server = "localhost";
$db_user = "root";
$db_pass = "1234";
$db_name = "userdata";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

// if (!$conn) {
//     // Connection failed
//     die("Could not connect: " . mysqli_connect_error());
// } else {
//     // Connection successful
//     echo "You are connected <br>";
// }

?>

