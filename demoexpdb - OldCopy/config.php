<?php
// Database connection configuration
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "demoexpdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");

// Function to sanitize input data
function sanitize($conn, $data) {
    return $conn->real_escape_string(trim($data));
}

// Function to redirect with message
function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header("Location: $url");
    exit();
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>