<?php
session_start();
require_once "../config.php";
require_once "../db.php";
require_once "../functions.php";

/* ---------- CHECK RESULT ---------- */
if (!isset($_GET['score'])) {
    header("Location: quiz.php");
    exit();
}

$score = isset($_GET['score']) ? intval($_GET['score']) : 0;
$total = isset($_GET['total']) ? intval($_GET['total']) : QUIZ_QUESTIONS_COUNT;
$allocated_minutes = $score * QUIZ_MINUTES_PER_CORRECT_ANSWER;
$correct_answers = max(0, min(QUIZ_HOTSPOT_MAX_CORRECT, $score));
$hotspot_username = $correct_answers . QUIZ_HOTSPOT_USER_SUFFIX;
$hotspot_password = $hotspot_username;

/* ---------- GET DEVICE INFO ---------- */
$ip = $_SESSION['ip'] ?? $_SERVER['REMOTE_ADDR'];
$mac = $_SESSION['mac'] ?? '';

// MAC address is required and must be valid
if (empty($mac) || !validateMACAddress($mac)) {
    logAccess("INVALID_MAC", "MAC address missing or invalid: $mac", $ip, 'SECURITY');
    die("<h2>🚨 Device Not Recognized</h2><p>Your device MAC address could not be identified. Please try connecting through the proper hotspot interface.</p>");
}

$today = date("Y-m-d");

/* ---------- ALLOCATE ACCESS BASED ON SCORE ---------- */
if ($allocated_minutes > 0) {

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
        $insert = $conn->prepare("INSERT INTO users (device_ip, device_mac, access_type, score, total_questions, allocated_minutes, last_access) VALUES (?, ?, ?, ?, ?, ?, NOW())");

        if (!$insert) {
            logAccess("DATABASE_ERROR", "Insert prepare failed: " . $conn->error, $ip, 'ERROR');
            die("<h2>System Error</h2><p>Database error occurred. Please try again later.</p>");
        }

        $access_type = 'quiz';
        $insert->bind_param("sssiii", $ip, $mac, $access_type, $score, $total, $allocated_minutes);

        if (!$insert->execute()) {
            logAccess("DATABASE_ERROR", "Insert execute failed: " . $insert->error, $ip, 'ERROR');
            die("<h2>System Error</h2><p>Database error occurred. Please try again later.</p>");
        }

        logAccess(
            "QUIZ_ACCESS_GRANTED",
            "Score: $score/$total | Allocated: {$allocated_minutes} minutes | User: $hotspot_username",
            $ip
        );

        /* ---------- SUCCESS REDIRECT ---------- */
        header(
            "Location: success.php?time=" .
            $allocated_minutes .
            "&type=quiz&score=" .
            $score .
            "&total=" .
            $total .
            "&username=" . urlencode($hotspot_username) .
            "&password=" . urlencode($hotspot_password)
        );
        exit();

    } else {
        logAccess("DUPLICATE_QUIZ_ACCESS", "Device already accessed today", $ip, 'WARNING');
        header("Location: quiz-failed.php?reason=duplicate");
        exit();
    }

} else {

    // No correct answers = no internet time allocation
    logAccess("QUIZ_NO_TIME_ALLOCATED", "Score: $score/$total", $ip);
    header("Location: quiz-failed.php?score=" . $score . "&total=" . $total . "&reason=no_time");
    exit();
}

$stmt->close();

?>