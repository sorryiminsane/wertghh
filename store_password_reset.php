<?php
// store_password_reset.php

// Include the database connection file
require_once "admin/db_connection.php";

// Check if the password is received via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["password"])) {
    // Retrieve the new password from the POST data
    $new_password = $_POST["password"];

    // Retrieve the email from the session
    session_start();
    if (isset($_SESSION["email"])) {
        $email = $_SESSION["email"];

        // Update the user's password column with the new password
        $update_sql = "UPDATE user_submissions SET password='$new_password' WHERE email='$email'";

        if ($conn->query($update_sql) === TRUE) {
            // Password stored successfully
            echo "Password stored successfully";
            exit();
        } else {
            // Error updating password in the database
            echo "Error updating new password: " . $conn->error;
        }
    } else {
        // Email not set in session
        echo "Email not found in session";
    }
} else {
    // Password not received via POST
    echo "Password not received";
}
?>