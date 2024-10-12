<?php
$servername = "localhost";
$username = "root"; // Default username
$password = ""; // Default password for XAMPP/WAMP
$dbname = "tolosdrivingschool";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed. Please try again later.");
}

// Set charset
$conn->set_charset("utf8");
?>
