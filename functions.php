<?php
/**
 * ARALINKS Utility Functions
 * Security, logging, and helper functions
 */

require_once 'config.php';

/**
 * Log access attempts and actions
 */
function logAccess($action, $details = '', $ip = '', $status = 'INFO') {
    if (!ENABLE_LOGGING) return;
    
    if (empty($ip)) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$status] [$ip] $action - $details\n";
    
    // Create log directory if it doesn't exist
    $log_dir = dirname(LOG_FILE);
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    file_put_contents(LOG_FILE, $log_message, FILE_APPEND);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate voucher code format
 */
function isValidVoucherFormat($code) {
    // Alphanumeric only, 5-20 characters
    return preg_match('/^[A-Z0-9]{5,20}$/i', $code);
}

/**
 * Validate MAC address format
 * Accepts: XX:XX:XX:XX:XX:XX or XX-XX-XX-XX-XX-XX
 */
function validateMACAddress($mac) {
    // MAC address validation pattern
    return preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac);
}

/**
 * Check rate limiting for voucher attempts
 */
function checkRateLimit($ip = '') {
    if (empty($ip)) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    $attempt_key = 'voucher_attempt_' . $ip;
    
    if (!isset($_SESSION[$attempt_key])) {
        $_SESSION[$attempt_key] = array('count' => 0, 'timestamp' => time());
        return true;
    }
    
    $last_attempt = $_SESSION[$attempt_key]['timestamp'];
    $current_time = time();
    
    // Reset counter if timeout expired
    if ($current_time - $last_attempt > VOUCHER_ATTEMPT_TIMEOUT) {
        $_SESSION[$attempt_key] = array('count' => 0, 'timestamp' => $current_time);
        return true;
    }
    
    // Check if max attempts exceeded
    if ($_SESSION[$attempt_key]['count'] >= MAX_VOUCHER_ATTEMPTS) {
        return false;
    }
    
    // Increment counter
    $_SESSION[$attempt_key]['count']++;
    $_SESSION[$attempt_key]['timestamp'] = $current_time;
    
    return true;
}

/**
 * Get remaining rate limit attempts
 */
function getRemainingAttempts($ip = '') {
    if (empty($ip)) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    $attempt_key = 'voucher_attempt_' . $ip;
    
    if (!isset($_SESSION[$attempt_key])) {
        return MAX_VOUCHER_ATTEMPTS;
    }
    
    $current_time = time();
    $last_attempt = $_SESSION[$attempt_key]['timestamp'];
    
    // Reset if timeout expired
    if ($current_time - $last_attempt > VOUCHER_ATTEMPT_TIMEOUT) {
        return MAX_VOUCHER_ATTEMPTS;
    }
    
    return max(0, MAX_VOUCHER_ATTEMPTS - $_SESSION[$attempt_key]['count']);
}

/**
 * Handle errors safely
 */
function handleError($error_message, $error_code = 'ERROR', $details = '') {
    logAccess("ERROR: $error_code", $details, '', 'ERROR');
    
    $response = [
        'success' => false,
        'message' => SHOW_ERRORS_TO_USER ? $error_message : 'An error occurred. Please try again.'
    ];
    
    if (DEBUG_MODE) {
        $response['debug'] = $details;
    }
    
    return $response;
}

?>
