<?php
/**
 * ARALINKS Database Setup Script
 * This script creates the required tables for the improved system
 * Run in browser: http://localhost/aralinks/setup_tables.php
 */

require_once 'config.php';
require_once 'db.php';

$errors = [];
$success = [];

// ============================================================
// CREATE USERS TABLE
// ============================================================
$sql_users = "CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  device_ip VARCHAR(45) NOT NULL COMMENT 'Student device IP address',
  device_mac VARCHAR(17) COMMENT 'Student device MAC address',
  access_type ENUM('quiz', 'voucher') DEFAULT 'quiz' COMMENT 'How access was gained',
  score INT COMMENT 'Quiz score if from quiz',
  total_questions INT COMMENT 'Total questions in quiz',
  last_access TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'When access was granted',
  
  INDEX idx_device_ip (device_ip),
  INDEX idx_last_access (last_access),
  INDEX idx_access_type (access_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Logs every WiFi access request and result'";

if ($conn->query($sql_users)) {
    $success[] = "✅ Users table created/updated successfully";
} else {
    $errors[] = "❌ Error creating users table: " . $conn->error;
}

// ============================================================
// CREATE VOUCHERS TABLE
// ============================================================
$sql_vouchers = "CREATE TABLE IF NOT EXISTS vouchers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Unique voucher code',
  duration INT NOT NULL DEFAULT 60 COMMENT 'WiFi access duration in minutes',
  used TINYINT DEFAULT 0 COMMENT 'Has voucher been used? (0=No, 1=Yes)',
  used_by_mac VARCHAR(17) COMMENT 'MAC address that used this voucher',
  used_by_ip VARCHAR(45) COMMENT 'IP address that used this voucher',
  used_at TIMESTAMP NULL COMMENT 'When was this voucher used',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When was voucher created',
  
  INDEX idx_code (code),
  INDEX idx_used (used),
  INDEX idx_created_at (created_at),
  INDEX idx_used_at (used_at),
  INDEX idx_used_by_mac (used_by_mac)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stores WiFi voucher codes and their usage status'";

if ($conn->query($sql_vouchers)) {
    $success[] = "✅ Vouchers table created/updated successfully";
} else {
    $errors[] = "❌ Error creating vouchers table: " . $conn->error;
}

// ============================================================
// CREATE ACCESS LOG TABLE (Optional)
// ============================================================
$sql_access_log = "CREATE TABLE IF NOT EXISTS access_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  level VARCHAR(20) COMMENT 'Log level: INFO, WARNING, ERROR, SECURITY',
  ip_address VARCHAR(45),
  action VARCHAR(100),
  details TEXT,
  
  INDEX idx_timestamp (timestamp),
  INDEX idx_level (level),
  INDEX idx_ip_address (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Detailed audit trail of all system actions'";

if ($conn->query($sql_access_log)) {
    $success[] = "✅ Access log table created/updated successfully";
} else {
    $errors[] = "⚠️ Note: Access log table - " . $conn->error;
}

// ============================================================
// INSERT SAMPLE DATA
// ============================================================
$sample_vouchers = [
    ['TEST001', 60],
    ['TEST002', 120],
    ['TEST003', 180],
    ['TEACHER001', 240],
    ['TEACHER002', 240],
    ['ADMIN001', 480]
];

$existing_codes = $conn->query("SELECT COUNT(*) as count FROM vouchers WHERE code IN ('TEST001', 'TEST002', 'TEST003')");
$row = $existing_codes->fetch_assoc();

if ($row['count'] == 0) {
    foreach ($sample_vouchers as $voucher) {
        $code = $voucher[0];
        $duration = $voucher[1];
        $insert_sql = "INSERT INTO vouchers (code, duration) VALUES ('$code', $duration)";
        
        if ($conn->query($insert_sql)) {
            $success[] = "✅ Sample voucher '$code' inserted";
        } else {
            if (strpos($conn->error, 'Duplicate entry') === false) {
                $errors[] = "⚠️ Voucher '$code': " . $conn->error;
            }
        }
    }
} else {
    $success[] = "✅ Sample vouchers already exist (skipped insertion)";
}

// ============================================================
// VERIFY TABLES
// ============================================================
$tables_check = $conn->query("SHOW TABLES FROM " . DB_NAME);
$tables = [];
while ($table = $tables_check->fetch_row()) {
    $tables[] = $table[0];
}

$success[] = "📊 Database Tables: " . implode(", ", $tables);

// ============================================================
// DISPLAY RESULTS
// ============================================================
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARALINKS Database Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 28px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .message {
            margin: 12px 0;
            padding: 12px 15px;
            border-radius: 6px;
            font-size: 14px;
            line-height: 1.5;
            border-left: 4px solid #ccc;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        
        .status-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        
        .status-title {
            font-weight: bold;
            color: #333;
            margin: 15px 0 10px 0;
            font-size: 16px;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            flex: 1;
            min-width: 150px;
            padding: 12px 20px;
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
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .check-box {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .check-item {
            padding: 8px 0;
            font-size: 13px;
            color: #555;
        }
        
        .check-item.ok {
            color: #28a745;
            font-weight: bold;
        }
        
        .icon {
            margin-right: 8px;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }
            
            h1 {
                font-size: 22px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🗄️ Database Setup</h1>
    <p class="subtitle">ARALINKS - FFi Advocacy System</p>
    
    <!-- Results Section -->
    <div class="status-section">
        <?php if (!empty($success)): ?>
            <div class="status-title">✅ Success</div>
            <?php foreach ($success as $msg): ?>
                <div class="message success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="status-title">⚠️ Issues</div>
            <?php foreach ($errors as $msg): ?>
                <div class="message error"><?php echo htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Database Status -->
    <div class="status-section">
        <div class="status-title">📊 Database Status</div>
        <div class="check-box">
            <div class="check-item ok">
                <span class="icon">✓</span>
                Database: <strong><?php echo DB_NAME; ?></strong>
            </div>
            <div class="check-item ok">
                <span class="icon">✓</span>
                Tables: <strong><?php echo count($tables); ?></strong>
            </div>
            <div class="check-item ok">
                <span class="icon">✓</span>
                Charset: <strong>utf8mb4</strong>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="button-group">
        <a href="http://localhost/aralinks/" class="btn btn-primary">🏠 Go to Home</a>
        <a href="http://localhost/phpmyadmin" class="btn btn-secondary">📊 View in phpMyAdmin</a>
    </div>
    
    <div style="margin-top: 20px; padding: 15px; background: #f0f8ff; border-radius: 6px; font-size: 12px; color: #333; line-height: 1.6;">
        <strong>✅ Setup Complete!</strong><br>
        Your database is now ready for ARALINKS. You can:
        <ul style="margin: 10px 0 0 20px;">
            <li>Start the system at http://localhost/aralinks/</li>
            <li>Test quiz and voucher flows</li>
            <li>Monitor access in phpMyAdmin</li>
        </ul>
    </div>
</body>
</html>

<?php
$conn->close();
?>
