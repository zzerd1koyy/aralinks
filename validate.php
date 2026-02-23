<?php
/**
 * Voucher Code Validator
 * ARALINKS - Validates and processes voucher codes
 */

session_start();
require_once 'db.php';
require_once 'functions.php';

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    logAccess("CSRF_VIOLATION", "Invalid CSRF token", '', 'SECURITY');
    die("<h2>Security Error</h2><p>Invalid request. Please go back and try again.</p>");
}

$ip = $_SERVER['REMOTE_ADDR'];

// Check rate limiting
if (!checkRateLimit($ip)) {
    $remaining = getRemainingAttempts($ip);
    logAccess("RATE_LIMIT_EXCEEDED", "Voucher validation attempts exceeded", $ip, 'SECURITY');
    die("<h2>Too Many Attempts</h2><p>Please wait " . VOUCHER_ATTEMPT_TIMEOUT . " seconds before trying again. You have {$remaining} attempts remaining.</p>");
}

$remaining_attempts = getRemainingAttempts($ip);

// Get and validate voucher code
$code = isset($_POST['code']) ? sanitizeInput($_POST['code']) : '';
$mac = $_SESSION['mac'] ?? '';

// MAC address is required for device identification
if (empty($mac) || !validateMACAddress($mac)) {
    logAccess("INVALID_MAC", "MAC address missing or invalid for voucher: $mac", $ip, 'SECURITY');
    die("<h2>🚨 Device Not Recognized</h2><p>Your device MAC address could not be identified. Please try connecting through the proper hotspot interface.</p>");
}

if (empty($code)) {
    logAccess("VOUCHER_VALIDATION_ERROR", "Empty voucher code submitted", $ip);
    $response = [
        'success' => false,
        'message' => '❌ Please enter a voucher code.',
        'remaining' => $remaining_attempts
    ];
    $_SESSION['voucher_response'] = $response;
    header("Location: voucher.php?error=1");
    exit;
}

// Validate format
if (!isValidVoucherFormat($code)) {
    logAccess("VOUCHER_VALIDATION_ERROR", "Invalid format: $code", $ip);
    $response = [
        'success' => false,
        'message' => '❌ Invalid voucher code format.',
        'remaining' => $remaining_attempts
    ];
    $_SESSION['voucher_response'] = $response;
    header("Location: voucher.php?error=1");
    exit;
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT id, duration FROM vouchers WHERE code = ? AND used = 0 LIMIT 1");

if (!$stmt) {
    logAccess("DATABASE_ERROR", "Prepare failed: " . $conn->error, $ip, 'ERROR');
    die("<h2>System Error</h2><p>Database error occurred. Please try again later.</p>");
}

$stmt->bind_param("s", $code);

if (!$stmt->execute()) {
    logAccess("DATABASE_ERROR", "Execute failed: " . $stmt->error, $ip, 'ERROR');
    die("<h2>System Error</h2><p>Database error occurred. Please try again later.</p>");
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $duration = $row['duration'];
    
    // Mark voucher as used (with MAC and IP for tracking)
    $update_stmt = $conn->prepare("UPDATE vouchers SET used = 1, used_by_mac = ?, used_by_ip = ?, used_at = NOW() WHERE code = ?");
    
    if (!$update_stmt) {
        logAccess("DATABASE_ERROR", "Update prepare failed: " . $conn->error, $ip, 'ERROR');
        die("<h2>System Error</h2><p>Database error occurred. Please try again later.</p>");
    }
    
    $update_stmt->bind_param("sss", $mac, $ip, $code);
    
    if (!$update_stmt->execute()) {
        logAccess("DATABASE_ERROR", "Update execute failed: " . $update_stmt->error, $ip, 'ERROR');
        die("<h2>System Error</h2><p>Database error occurred. Please try again later.</p>");
    }
    
    logAccess("VOUCHER_REDEEMED", "Duration: {$duration} min | MAC: $mac", $ip);
    
    // Authorize device in MikroTik if MAC provided
    $mac = $_SESSION['mac'] ?? '';
    if (!empty($mac)) {
        authorizeInMikroTik($mac, $ip);
    }
    
    // Redirect to success page
    header("Location: success.php?time=" . urlencode($duration) . "&type=voucher");
    exit;
    
} else {
    logAccess("VOUCHER_VALIDATION_ERROR", "Invalid or used voucher: $code", $ip);
    $response = [
        'success' => false,
        'message' => '❌ Invalid or already used voucher code.',
        'remaining' => $remaining_attempts
    ];
    $_SESSION['voucher_response'] = $response;
    header("Location: voucher.php?error=1");
    exit;
}

$stmt->close();

/**
 * Authorize device in MikroTik RouterOS
 */
function authorizeInMikroTik($mac, $ip) {
    $socket = @fsockopen(MIKROTIK_IP, MIKROTIK_PORT, $errno, $errstr, MIKROTIK_TIMEOUT);
    
    if (!$socket) {
        logAccess("MIKROTIK_ERROR", "Connection failed: $errstr ($errno)", $ip, 'WARNING');
        return false;
    }
    
    try {
        // Send login command
        fwrite($socket, "/login\n");
        fwrite($socket, "=name=" . MIKROTIK_USER . "\n");
        fwrite($socket, "=password=" . MIKROTIK_PASS . "\n\n");
        
        // Add MAC bypass
        fwrite($socket, "/ip/hotspot/ip-binding/add\n");
        fwrite($socket, "=mac-address=$mac\n");
        fwrite($socket, "=address=$ip\n");
        fwrite($socket, "=type=bypassed\n");
        fwrite($socket, "=comment=voucher-redeemed\n\n");
        
        logAccess("MIKROTIK_AUTH_SUCCESS", "MAC: $mac", $ip);
        
    } catch (Exception $e) {
        logAccess("MIKROTIK_ERROR", $e->getMessage(), $ip, 'WARNING');
    } finally {
        fclose($socket);
    }
}

?>
