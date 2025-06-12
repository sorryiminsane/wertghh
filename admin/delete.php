<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

// Check if the ID parameter is provided in the POST request
if(isset($_POST['id'])) {
    // Include the database connection file
    require_once 'db_connection.php';

    // Prepare a delete statement
    $sql = "DELETE FROM user_submissions WHERE id = ?";

    if($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $param_id);

        // Set parameters
        $param_id = $_POST['id'];

        // Attempt to execute the prepared statement
        if($stmt->execute()) {
            // Records deleted successfully. Redirect to the previous page
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        } else {
            // Error handling
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
} else {
    // ID parameter is not provided. Redirect to the previous page
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
}
?>
