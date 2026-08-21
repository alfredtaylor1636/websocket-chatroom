<?php
$host = "127.0.0.1";
$user = "chatuser";
$password = "chatpassword";
$database = "chatroom";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

session_start();
?>
