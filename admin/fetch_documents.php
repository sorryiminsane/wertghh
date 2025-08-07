<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Please log in.']);
    exit();
}

// Include database connection
require_once 'db_connection.php';

// Check if token is provided
if (!isset($_GET['token']) || empty($_GET['token'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request.']);
    exit();
}

// Sanitize the token
$token = $conn->real_escape_string($_GET['token']);

// Fetch document information from the database
$sql = "SELECT id_verified, id_verification_date, document_front_path, document_back_path, document_selfie_path, document_type, document_number, verification_notes FROM user_submissions WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Prepare document data
    $documents = [];
    
    // Add front document if exists
    if (!empty($row['document_front_path'])) {
        $documents[] = [
            'type' => 'Front of ' . ($row['document_type'] ?? 'Document'),
            'path' => basename($row['document_front_path']),
            'original_name' => basename($row['document_front_path'])
        ];
    }
    
    // Add back document if exists
    if (!empty($row['document_back_path'])) {
        $documents[] = [
            'type' => 'Back of ' . ($row['document_type'] ?? 'Document'),
            'path' => basename($row['document_back_path']),
            'original_name' => basename($row['document_back_path'])
        ];
    }
    
    // Add selfie if exists
    if (!empty($row['document_selfie_path'])) {
        $documents[] = [
            'type' => 'Selfie',
            'path' => basename($row['document_selfie_path']),
            'original_name' => basename($row['document_selfie_path'])
        ];
    }
    
    // Return document data
    echo json_encode([
        'success' => true,
        'documents' => $documents,
        'verification_status' => $row['id_verified'],
        'verification_date' => $row['id_verification_date'],
        'document_type' => $row['document_type'],
        'document_number' => $row['document_number'],
        'notes' => $row['verification_notes']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No document data found for this victim.'
    ]);
}

$stmt->close();
$conn->close();
?>
