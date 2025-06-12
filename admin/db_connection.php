<?php
// db_connection.php

// Database configuration for local XAMPP environment
$servername = "localhost";
$username = "root";
$password = "";  // Default XAMPP MySQL has no password
$dbname = "coinbase_panel";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 to handle all characters properly
$conn->set_charset("utf8mb4");
?>
