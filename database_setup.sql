-- ARALINKS Database Setup Script
-- Run this in phpMyAdmin or MySQL command line to set up the database

-- Create Database
CREATE DATABASE IF NOT EXISTS aralinks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aralinks;

-- Create Users Table (Access logging)
CREATE TABLE IF NOT EXISTS users (
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
COMMENT='Logs every WiFi access request and result';

-- Create Vouchers Table
CREATE TABLE IF NOT EXISTS vouchers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Unique voucher code',
  duration INT NOT NULL DEFAULT 60 COMMENT 'WiFi access duration in minutes',
  used TINYINT DEFAULT 0 COMMENT 'Has voucher been used? (0=No, 1=Yes)',
  used_by_ip VARCHAR(45) COMMENT 'IP that used this voucher',
  used_at TIMESTAMP NULL COMMENT 'When was this voucher used',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When was voucher created',
  
  INDEX idx_code (code),
  INDEX idx_used (used),
  INDEX idx_created_at (created_at),
  INDEX idx_used_at (used_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stores WiFi voucher codes and their usage status';

-- Create Access Log Table (Optional - for detailed audit trail)
CREATE TABLE IF NOT EXISTS access_log (
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
COMMENT='Detailed audit trail of all system actions';

-- ============================================================
-- SAMPLE DATA (optional - delete after testing)
-- ============================================================

-- Sample Vouchers for Testing
INSERT INTO vouchers (code, duration) VALUES
('TEST001', 60),
('TEST002', 120),
('TEST003', 180),
('TEACHER001', 240),
('TEACHER002', 240),
('ADMIN001', 480);

-- ============================================================
-- VERIFICATION QUERIES
-- ============================================================

-- Verify tables were created
SELECT 'Users Table:' as Table_Name;
DESCRIBE users;

SELECT 'Vouchers Table:' as Table_Name;
DESCRIBE vouchers;

SELECT 'Sample Vouchers:' as Table_Name;
SELECT code, duration, used, created_at FROM vouchers;

-- Show table status
SHOW TABLE STATUS FROM aralinks;
