<?php
/**
 * ARALINKS Configuration File
 * Centralized configuration for database and MikroTik settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'aralinks');

// MikroTik Router Configuration
define('MIKROTIK_IP', '192.168.40.177');
define('MIKROTIK_USER', 'ComonHS');
define('MIKROTIK_PASS', '@comonhs.mikrotik');
define('MIKROTIK_PORT', 8728);
define('MIKROTIK_TIMEOUT', 3);

// Quiz Configuration
define('QUIZ_QUESTIONS_COUNT', 5);
define('QUIZ_PASS_PERCENTAGE', 0.8); // 80% (4 out of 5 questions)
define('QUIZ_ACCESS_TIME_MINUTES', 60);

// Voucher Configuration
define('MAX_VOUCHER_ATTEMPTS', 5); // Max attempts before rate limiting
define('VOUCHER_ATTEMPT_TIMEOUT', 300); // 5 minutes

// Access Control
define('MAX_FREE_ACCESS_PER_DAY', 1); // Max free quizzes per day per IP

// Security
define('ENABLE_LOGGING', true);
define('LOG_FILE', dirname(__FILE__) . '/logs/access.log');
define('SESSION_TIMEOUT', 1800); // 30 minutes

// Error Handling
define('SHOW_ERRORS_TO_USER', false); // Set to false in production
define('DEBUG_MODE', true); // Set to false in production

?>
