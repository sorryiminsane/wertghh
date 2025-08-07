<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in and is admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Check if token is provided
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Token is required']);
    exit();
}

$token = $_GET['token'];

// Prepare the response
$response = [
    'success' => false,
    'selfies' => [],
    'error' => ''
];

try {
    // Fetch selfie data from the database
    $stmt = $conn->prepare("SELECT document_selfie_path as path FROM user_submissions WHERE token = ? AND document_selfie_path IS NOT NULL");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $selfies = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['path']) && file_exists("../" . $row['path'])) {
            $selfies[] = [
                'path' => $row['path'],
                'filename' => basename($row['path']),
                'uploaded_at' => filemtime("../" . $row['path'])
            ];
        }
    }
    
    $response['success'] = true;
    $response['selfies'] = $selfies;
    
} catch (Exception $e) {
    $response['error'] = 'Error fetching selfies: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
