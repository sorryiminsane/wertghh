<?php
session_start();

// Define default username and password
$default_username = "admin";
$default_password = "123";

// Initialize error message variable
$error_message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from the form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validate username and password
    if ($username === $default_username && $password === $default_password) {
        // Authentication successful, set session variable and redirect to dashboard.php
        $_SESSION["loggedin"] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        // Authentication failed, set error message
        $error_message = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/favicon-32x32.png" type="image/x-icon">
    <title>Gray Panel - Login</title>
    <style>
    * {
        box-sizing: border-box;
        font-family: 'Courier New', monospace;
        margin: 0;
        padding: 0;
        color: #fff;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background: #0a0a0a;
        overflow: hidden;
        position: relative;
    }

    /* Animated Star Field Background */
    .stars {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }

    .star {
        position: absolute;
        width: 2px;
        height: 2px;
        background: #666666;
        animation: moveStars linear infinite;
    }

    @keyframes moveStars {
        from {
            transform: translateY(-100vh) translateX(0);
        }
        to {
            transform: translateY(100vh) translateX(-50px);
        }
    }

    .login-container {
        position: relative;
        z-index: 10;
        text-align: center;
        background: #111111;
        border: 2px solid #444444;
        padding: 3rem;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.8);
    }

    .logo-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #444444;
    }

    .logo-icon {
        width: 80px;
        height: 80px;
        background: #666666;
        border: 2px solid #888888;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Courier New', monospace;
        font-weight: 700;
        font-size: 2rem;
        color: #ffffff;
        letter-spacing: -2px;
        margin: 0 auto 1rem;
    }

    .logo-text {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        font-size: 1.5rem;
        color: #cccccc;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .logo-subtitle {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        color: #888888;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .login-container input {
        display: block;
        width: 100%;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #1a1a1a;
        border: 1px solid #444444;
        color: #fff;
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
        transition: border-color 0.2s ease;
    }

    .login-container input:focus {
        outline: none;
        border-color: #666666;
        background: #222222;
    }

    .login-container input::placeholder {
        color: #666666;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.75rem;
    }

    .login-container button {
        display: block;
        width: 100%;
        padding: 1rem;
        border: 1px solid #666666;
        background: #333333;
        color: #fff;
        font-size: 0.875rem;
        font-family: 'Courier New', monospace;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 1rem;
    }

    .login-container button:hover {
        background: #444444;
        border-color: #888888;
    }

    .contact-link {
        font-size: 0.75rem;
        text-decoration: none;
        color: #666666;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: color 0.2s ease;
    }

    .contact-link:hover {
        color: #888888;
    }

    .error-message {
        color: #aa4444;
        margin-bottom: 1rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: #221111;
        border: 1px solid #441111;
        padding: 0.75rem;
    }
</style>

</head>
<body>
<!-- Animated Star Field Background -->
<div class="stars" id="starField"></div>

<div class="login-container">
    <div class="logo-section">
        <div class="logo-icon">GP</div>
        <div class="logo-text">GRAY PANEL</div>
        <div class="logo-subtitle">ADMINISTRATIVE ACCESS</div>
    </div>
    
    <?php if (!empty($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
        <?php $error_message = ""; ?>
    <?php endif; ?>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    
    <a href="#" class="contact-link">Contact Support</a>
</div>
<script>
    // Create animated star field
    function createStarField() {
        const starField = document.getElementById('starField');
        const numStars = 50;

        for (let i = 0; i < numStars; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            
            // Random position
            star.style.left = Math.random() * 100 + '%';
            star.style.animationDuration = (Math.random() * 20 + 15) + 's';
            star.style.animationDelay = Math.random() * 20 + 's';
            
            // Random opacity
            star.style.opacity = Math.random() * 0.8 + 0.2;
            
            starField.appendChild(star);
        }
    }

    // Function to hide error message after 10 seconds
    setTimeout(function() {
        var errorMessage = document.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
    }, 10000);

    // Initialize star field when page loads
    document.addEventListener('DOMContentLoaded', function() {
        createStarField();
    });
</script>
</body>
</html>
