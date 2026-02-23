<?php
/**
 * Database Connection - ARALINKS
 */

require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    logAccess("Database Connection Failed", $conn->connect_error, '', 'CRITICAL');
    
    if (SHOW_ERRORS_TO_USER) {
        die("<h2>Database Connection Error</h2><p>" . htmlspecialchars($conn->connect_error) . "</p>");
    } else {
        die("<h2>System Error</h2><p>Unable to connect to database. Please try again later.</p>");
    }
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

?>