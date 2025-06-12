<?php
// Start the session
session_start();

// Include the database connection file
require_once "admin/db_connection.php";

// Function to update user status
function updateStatus($token, $status) {
    global $conn; // Access the database connection within the function
    $sql = "UPDATE user_submissions SET status = ? WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $status, $token);
    $stmt->execute();
    $stmt->close();
}

// Check if the email and token are set in the session
if (!isset($_SESSION["email"]) || !isset($_SESSION["token"])) {
    // If email or token is not set, return an error
    http_response_code(401);
    exit();
}

// Retrieve the token from the session
$token = $_SESSION["token"];

// Check if the status is set in the POST data
if (isset($_POST["status"])) {
    // Retrieve the status from the POST data
    $status = $_POST["status"];

    // Update user status in the database
    updateStatus($token, $status);

    // Return a success response
    http_response_code(200);
    exit();
} else {
    // If status is not set, return an error
    http_response_code(400);
    exit();
}
?>
