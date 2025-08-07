<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(403);
    echo "Access denied. Please log in.";
    exit();
}

// Include database connection
require_once 'db_connection.php';

// Check if document path is provided
if (!isset($_GET['path']) || empty($_GET['path'])) {
    http_response_code(400);
    echo "Invalid request.";
    exit();
}

// Sanitize the document path
$documentPath = basename($_GET['path']);

// Validate that the file exists in the uploads directory
$fullPath = '../uploads/' . $documentPath;
if (!file_exists($fullPath)) {
    http_response_code(404);
    echo "File not found.";
    exit();
}

// Get file info
$fileExtension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
$fileSize = filesize($fullPath);

// Generate better filename using token and document type if available
$filename = 'document';
if (isset($_GET['token']) && isset($_GET['type'])) {
    $filename = $_GET['token'] . '_' . $_GET['type'];
}

// Set appropriate headers for download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '.' . $fileExtension . '"');
header('Content-Length: ' . $fileSize);
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Clear output buffer and serve file
ob_clean();
flush();
readfile($fullPath);
exit();
?>
