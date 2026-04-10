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
define('MIKROTIK_IP', '192.168.40.219');
define('MIKROTIK_USER', 'aralinks');
define('MIKROTIK_PASS', 'aralinksbydikoy');
define('MIKROTIK_PORT', 8728);
define('MIKROTIK_TIMEOUT', 3);

// Quiz Configuration
define('QUIZ_QUESTIONS_COUNT', 10);
define('QUIZ_MINUTES_PER_CORRECT_ANSWER', 12);

// Offline Library
define('OFFLINE_LIBRARY_URL', 'http://192.168.40.1/library');

// Quiz-to-MikroTik Username Mapping
define('QUIZ_HOTSPOT_USER_SUFFIX', 'aralinks_zeke');
define('QUIZ_HOTSPOT_MAX_CORRECT', 10);
define('QUIZ_HOTSPOT_SHARED_USERS', 100);

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