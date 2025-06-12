<?php
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
?>