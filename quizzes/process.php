<?php
session_start();
require_once "../config.php";
require_once "../db.php";
require_once "../functions.php";

/* ---------- CHECK RESULT ---------- */
if (!isset($_GET['passed'])) {
    header("Location: quiz.php");
    exit();
}

$passed = intval($_GET['passed']);
$score = isset($_GET['score']) ? intval($_GET['score']) : 0;
$total = isset($_GET['total']) ? intval($_GET['total']) : QUIZ_QUESTIONS_COUNT;

/* ---------- GET DEVICE INFO ---------- */
$ip  = $_SESSION['ip']  ?? $_SERVER['REMOTE_ADDR'];
$mac = $_SESSION['mac'] ?? '';

// MAC address is required and must be valid
if (empty($mac) || !validateMACAddress($mac)) {
    logAccess("INVALID_MAC", "MAC address missing or invalid: $mac", $ip, 'SECURITY');
    die("<h2>🚨 Device Not Recognized</h2><p>Your device MAC address could not be identified. Please try connecting through the proper hotspot interface.</p>");
}

$today = date("Y-m-d");

/* ---------- IF QUIZ PASSED ---------- */
if ($passed == 1) {

    // Check if device (by MAC) already accessed today
    // MAC address is unique per device, more secure than IP
    $stmt = $conn->prepare("SELECT id FROM users WHERE device_mac=? AND DATE(last_access)=?");
    
    if (!$stmt) {
        logAccess("DATABASE_ERROR", "Prepare failed: " . $conn->error, $ip, 'ERROR');
        die("<h2>System Error</h2><p>Database error occurred. Please try again later.</p>");
    }
    
    $stmt->bind_param("ss", $mac, $today);
    
    if (!$stmt->execute()) {
        logAccess("DATABASE_ERROR", "Execute failed: " . $stmt->error, $ip, 'ERROR');
        die("<h2>System Error</h2><p>Database error occurred. Please try again later.</p>");
    }
    
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {

        // Save access log
        $insert = $conn->prepare("INSERT INTO users (device_ip, device_mac, access_type, score, total_questions, last_access) VALUES (?, ?, ?, ?, ?, NOW())");
        
        if (!$insert) {
            logAccess("DATABASE_ERROR", "Insert prepare failed: " . $conn->error, $ip, 'ERROR');
            die("<h2>System Error</h2><p>Database error occurred. Please try again later.</p>");
        }
        
        $access_type = 'quiz';
        $insert->bind_param("sssii", $ip, $mac, $access_type, $score, $total);
        
        if (!$insert->execute()) {
            logAccess("DATABASE_ERROR", "Insert execute failed: " . $insert->error, $ip, 'ERROR');
            die("<h2>System Error</h2><p>Database error occurred. Please try again later.</p>");
        }

        logAccess("QUIZ_ACCESS_GRANTED", "Score: $score/$total", $ip);

        /* ---------- AUTHORIZE DEVICE IN MIKROTIK ---------- */
        if (!empty($mac)) {
            authorizeInMikroTik($mac, $ip);
        }

        /* ---------- SUCCESS REDIRECT ---------- */
        header("Location: success.php?time=" . QUIZ_ACCESS_TIME_MINUTES . "&type=quiz");
        exit();

    } else {
        logAccess("DUPLICATE_QUIZ_ACCESS", "Device already accessed today", $ip, 'WARNING');
        header("Location: quiz-failed.php?reason=duplicate");
        exit();
    }

} else {

    // Quiz failed
    $required = ceil($total * QUIZ_PASS_PERCENTAGE);
    logAccess("QUIZ_FAILED", "Score: $score/$total (Required: $required)", $ip);
    header("Location: quiz-failed.php?score=" . $score . "&total=" . $total . "&required=" . $required);
    exit();
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

        // add MAC bypass
        fwrite($socket, "/ip/hotspot/ip-binding/add\n");
        fwrite($socket, "=mac-address=$mac\n");
        fwrite($socket, "=address=$ip\n");
        fwrite($socket, "=type=bypassed\n");
        fwrite($socket, "=comment=quiz-pass\n\n");

        logAccess("MIKROTIK_AUTH_SUCCESS", "MAC: $mac", $ip);

    } catch (Exception $e) {
        logAccess("MIKROTIK_ERROR", $e->getMessage(), $ip, 'WARNING');
    } finally {
        @fclose($socket);
    }
}

?>