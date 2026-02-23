<?php
/**
 * ARALINKS - MikroTik Connection Tester
 * Verifies ARALINKS is properly configured and can communicate with MikroTik
 */

require_once 'config.php';
require_once 'functions.php';

$tests = [];
$status_ok = true;

// ============================================================
// Test 1: Configuration Check
// ============================================================
$test_config = [
    'status' => 'success',
    'title' => '⚙️ Configuration',
    'items' => []
];

$test_config['items'][] = [
    'label' => 'MikroTik IP',
    'value' => MIKROTIK_IP,
    'status' => 'ok'
];

$test_config['items'][] = [
    'label' => 'MikroTik Port',
    'value' => MIKROTIK_PORT,
    'status' => 'ok'
];

$test_config['items'][] = [
    'label' => 'API User',
    'value' => MIKROTIK_USER,
    'status' => 'ok'
];

$test_config['items'][] = [
    'label' => 'Quiz Pass %',
    'value' => (QUIZ_PASS_PERCENTAGE * 100) . '%',
    'status' => 'ok'
];

$tests[] = $test_config;

// ============================================================
// Test 2: MikroTik Connectivity
// ============================================================
$test_mikrotik = [
    'status' => 'pending',
    'title' => '🔌 MikroTik Connection',
    'items' => []
];

$socket = @fsockopen(MIKROTIK_IP, MIKROTIK_PORT, $errno, $errstr, 3);

if ($socket) {
    $test_mikrotik['status'] = 'success';
    $test_mikrotik['items'][] = [
        'label' => 'Socket Connection',
        'value' => '✅ Connected',
        'status' => 'ok'
    ];
    
    // Try login
    fwrite($socket, "/login\n");
    fwrite($socket, "=name=" . MIKROTIK_USER . "\n");
    fwrite($socket, "=password=" . MIKROTIK_PASS . "\n\n");
    
    $response = fgets($socket, 128);
    
    if (strpos($response, '!done') !== false) {
        $test_mikrotik['items'][] = [
            'label' => 'API Authentication',
            'value' => '✅ Success',
            'status' => 'ok'
        ];
        $test_mikrotik['items'][] = [
            'label' => 'Status',
            'value' => 'Ready to grant access',
            'status' => 'ok'
        ];
    } else {
        $test_mikrotik['status'] = 'warning';
        $test_mikrotik['items'][] = [
            'label' => 'API Authentication',
            'value' => '⚠️ Failed (check user/pass)',
            'status' => 'warning'
        ];
        $status_ok = false;
    }
    
    @fclose($socket);
} else {
    $test_mikrotik['status'] = 'error';
    $test_mikrotik['items'][] = [
        'label' => 'Socket Connection',
        'value' => "❌ Failed: $errstr ($errno)",
        'status' => 'error'
    ];
    $test_mikrotik['items'][] = [
        'label' => 'Troubleshoot',
        'value' => 'Check: IP address, Port 8728, Firewall rules',
        'status' => 'error'
    ];
    $status_ok = false;
}

$tests[] = $test_mikrotik;

// ============================================================
// Test 3: Database Check
// ============================================================
require_once 'db.php';

$test_db = [
    'status' => 'success',
    'title' => '🗄️ Database',
    'items' => []
];

// Check tables exist
$tables = ['users', 'vouchers'];
foreach ($tables as $table) {
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check && $check->num_rows > 0) {
        $test_db['items'][] = [
            'label' => ucfirst($table) . ' Table',
            'value' => '✅ Exists',
            'status' => 'ok'
        ];
    } else {
        $test_db['items'][] = [
            'label' => ucfirst($table) . ' Table',
            'value' => '❌ Missing',
            'status' => 'error'
        ];
        $test_db['status'] = 'error';
        $status_ok = false;
    }
}

// Count test data
$voucher_count = $conn->query("SELECT COUNT(*) as count FROM vouchers")->fetch_assoc();
$test_db['items'][] = [
    'label' => 'Sample Vouchers',
    'value' => $voucher_count['count'] . ' found',
    'status' => 'ok'
];

$tests[] = $test_db;

// ============================================================
// Test 4: Network Interface Check
// ============================================================
$test_network = [
    'status' => 'info',
    'title' => '📡 Network Info',
    'items' => []
];

$test_network['items'][] = [
    'label' => 'Server IP (for hotspot redirect)',
    'value' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
    'status' => 'info'
];

$test_network['items'][] = [
    'label' => 'Hotspot Should Redirect To',
    'value' => $_SERVER['SERVER_ADDR'] . '/aralinks/quizzes/quiz.php',
    'status' => 'info'
];

$tests[] = $test_network;

// ============================================================
// Test 5: File Permissions
// ============================================================
$test_files = [
    'status' => 'success',
    'title' => '📁 File System',
    'items' => []
];

$log_dir = dirname(__FILE__) . '/logs';
if (is_dir($log_dir) && is_writable($log_dir)) {
    $test_files['items'][] = [
        'label' => 'Logs Directory',
        'value' => '✅ Writable',
        'status' => 'ok'
    ];
} else {
    $test_files['items'][] = [
        'label' => 'Logs Directory',
        'value' => '⚠️ Not writable or missing',
        'status' => 'warning'
    ];
}

$tests[] = $test_files;

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>System Status - ARALINKS</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 900px;
    margin: 0 auto;
}

.header {
    background: white;
    padding: 30px;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.header h1 {
    color: #333;
    margin-bottom: 5px;
}

.header p {
    color: #666;
    font-size: 14px;
}

.status-indicator {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    margin-top: 15px;
    font-size: 14px;
    font-weight: bold;
}

.status-indicator.ok {
    background: #d4edda;
    color: #155724;
}

.status-indicator.error {
    background: #f8d7da;
    color: #721c24;
}

.status-indicator.warning {
    background: #fff3cd;
    color: #856404;
}

.tests {
    background: white;
    border-radius: 0 0 12px 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    overflow: hidden;
}

.test-section {
    border-bottom: 1px solid #eee;
    padding: 25px 30px;
}

.test-section:last-child {
    border-bottom: none;
}

.test-title {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.test-status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.test-status.success {
    background: #d4edda;
    color: #155724;
}

.test-status.warning {
    background: #fff3cd;
    color: #856404;
}

.test-status.error {
    background: #f8d7da;
    color: #721c24;
}

.test-status.pending {
    background: #d1ecf1;
    color: #0c5460;
}

.test-status.info {
    background: #d1ecf1;
    color: #0c5460;
}

.test-items {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.test-item {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 6px;
    border-left: 3px solid #ccc;
}

.test-item.ok {
    border-left-color: #28a745;
    background: #f1f9f6;
}

.test-item.error {
    border-left-color: #dc3545;
    background: #ffe6e6;
}

.test-item.warning {
    border-left-color: #ffc107;
    background: #feffeb;
}

.test-item.info {
    border-left-color: #17a2b8;
    background: #ebf8fb;
}

.test-item-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.test-item-value {
    font-size: 14px;
    font-weight: bold;
    color: #333;
    font-family: 'Courier New', monospace;
    word-break: break-all;
}

.actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s;
    display: inline-block;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5568d3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.recommendations {
    background: #e7f3ff;
    border-left: 4px solid #0066cc;
    padding: 20px;
    margin-top: 20px;
    border-radius: 6px;
    color: #004085;
}

.recommendations h3 {
    margin-bottom: 10px;
    color: #0c5460;
}

.recommendations ul {
    margin-left: 20px;
}

.recommendations li {
    margin: 8px 0;
}

@media (max-width: 768px) {
    .test-items {
        grid-template-columns: 1fr;
    }
    
    .header {
        padding: 20px;
    }
    
    .test-section {
        padding: 15px 20px;
    }
}
</style>
</head>

<body>

<div class="container">
    <div class="header">
        <h1>🔧 ARALINKS System Status</h1>
        <p>MikroTik Hotspot Integration Diagnostics</p>
        
        <?php if ($status_ok): ?>
            <div class="status-indicator ok">✅ All Systems Operational</div>
        <?php else: ?>
            <div class="status-indicator error">⚠️ Issues Detected - See Below</div>
        <?php endif; ?>
    </div>
    
    <div class="tests">
        <?php foreach ($tests as $test): ?>
            <div class="test-section">
                <div class="test-title">
                    <?php echo $test['title']; ?>
                    <span class="test-status <?php echo $test['status']; ?>">
                        <?php 
                        echo match($test['status']) {
                            'success' => '✅ PASS',
                            'error' => '❌ FAIL',
                            'warning' => '⚠️ WARNING',
                            'pending' => '⏳ PENDING',
                            'info' => 'ℹ️ INFO',
                            default => $test['status']
                        };
                        ?>
                    </span>
                </div>
                
                <div class="test-items">
                    <?php foreach ($test['items'] as $item): ?>
                        <div class="test-item <?php echo $item['status']; ?>">
                            <div class="test-item-label"><?php echo $item['label']; ?></div>
                            <div class="test-item-value"><?php echo htmlspecialchars($item['value']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="recommendations">
        <h3>📋 Next Steps</h3>
        <ul>
            <li><strong>Configure MikroTik:</strong> Follow the Read Me/MIKROTIK_SETUP.md guide</li>
            <li><strong>Test WiFi Connection:</strong> Connect to hotspot and auto-redirect should appear</li>
            <li><strong>Verify MAC Capture:</strong> Visit device-info.php to see if MAC is being captured</li>
            <li><strong>Test Quiz:</strong> Answer 4/5 questions correctly to test full workflow</li>
            <li><strong>Monitor Logs:</strong> Check /logs/access.log for all activity</li>
        </ul>
    </div>
    
    <div style="margin-top: 30px; display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="device-info.php" class="btn btn-primary">📱 Check Device Info</a>
        <a href="setup_tables.php" class="btn btn-primary">🗄️ Verify Database</a>
        <a href="/" class="btn btn-secondary">🏠 Home</a>
    </div>
</div>

</body>
</html>
