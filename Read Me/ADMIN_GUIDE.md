<?php
/**
 * ARALINKS - Admin Quick Reference Guide
 * Common tasks and SQL queries for administrators
 */

// This file is for reference only - do not include in production

?>

# ARALINKS Admin Quick Reference

## 📊 Common SQL Queries

### 1. View Today's Quiz Access Statistics

```sql
SELECT
  COUNT(*) as total_quiz_attempts,
  SUM(CASE WHEN score >= (total_questions * 0.8) THEN 1 ELSE 0 END) as passed,
  SUM(CASE WHEN score < (total_questions * 0.8) THEN 1 ELSE 0 END) as failed,
  AVG(score) as average_score
FROM users
WHERE access_type = 'quiz'
AND DATE(last_access) = CURDATE();
```

### 2. View Today's Voucher Usage

```sql
SELECT
  COUNT(*) as vouchers_redeemed,
  SUM(duration) as total_minutes_issued
FROM vouchers
WHERE DATE(used_at) = CURDATE()
AND used = 1;
```

### 3. Find Unused Vouchers

```sql
SELECT code, duration, created_at
FROM vouchers
WHERE used = 0
ORDER BY created_at DESC;
```

### 4. View Access by IP Address

```sql
SELECT
  device_ip,
  COUNT(*) as access_count,
  MAX(last_access) as last_access_time,
  SUM(CASE WHEN access_type = 'quiz' THEN 1 ELSE 0 END) as quiz_attempts,
  SUM(CASE WHEN access_type = 'voucher' THEN 1 ELSE 0 END) as voucher_uses
FROM users
GROUP BY device_ip
ORDER BY access_count DESC;
```

### 5. Add New Voucher Batch

```sql
INSERT INTO vouchers (code, duration) VALUES
('ARALINK-NEW-001', 60),
('ARALINK-NEW-002', 120),
('ARALINK-NEW-003', 180);
```

### 6. Mark Voucher as Used (Manual)

```sql
UPDATE vouchers
SET used = 1, used_by_ip = '192.168.1.100', used_at = NOW()
WHERE code = 'ARALINK001';
```

### 7. Reset Voucher (Mark Unused)

```sql
UPDATE vouchers
SET used = 0, used_by_ip = NULL, used_at = NULL
WHERE code = 'ARALINK001';
```

### 8. View Daily Statistics (Last 7 Days)

```sql
SELECT
  DATE(last_access) as date,
  COUNT(*) as total_access,
  COUNT(DISTINCT device_ip) as unique_devices,
  SUM(CASE WHEN access_type = 'quiz' THEN 1 ELSE 0 END) as quiz_access,
  SUM(CASE WHEN access_type = 'voucher' THEN 1 ELSE 0 END) as voucher_access
FROM users
WHERE last_access >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(last_access)
ORDER BY date DESC;
```

### 9. Find Devices That Cheated (Multiple Attempts Same Day)

```sql
SELECT
  device_ip,
  DATE(last_access) as access_date,
  COUNT(*) as attempts
FROM users
WHERE access_type = 'quiz'
GROUP BY device_ip, DATE(last_access)
HAVING attempts > 1
ORDER BY access_date DESC;
```

### 10. Delete Old Access Logs (Archive)

```sql
DELETE FROM users
WHERE last_access < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

---

## 🔄 Common Tasks

### Creating Vouchers

1. Generate codes (suggest: 5-10 character alphanumeric)
2. Decide duration (60, 120, 180 min are common)
3. Run INSERT query above
4. Distribute codes to students/staff

### Monitoring Access

1. Check `logs/access.log` daily for suspicious activity
2. Run query #3 above to see access patterns
3. If abnormal activity detected, check logs for IP/details

### Troubleshooting

1. Log file location: `/logs/access.log`
2. Check last entries for errors
3. Verify MikroTik credentials in `config.php`
4. Test database connection

### Backing Up Data

```bash
# Backup entire database
mysqldump -u root aralinks > aralinks_backup_2025-02-23.sql

# Backup just vouchers
mysqldump -u root aralinks vouchers > vouchers_only.sql
```

---

## 🚨 Security Monitoring

### Check for Brute Force Attempts

```sql
-- Look for IPs with many failed voucher attempts
SELECT device_ip, COUNT(*) as attempts
FROM access_log
WHERE action = 'VOUCHER_VALIDATION_ERROR'
AND timestamp > DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY device_ip
HAVING attempts > 5;
```

### Monitor CSRF Attempts

```sql
-- Check for CSRF violations
SELECT COUNT(*) as csrf_violations
FROM access_log
WHERE action = 'CSRF_VIOLATION'
AND timestamp > DATE_SUB(NOW(), INTERVAL 24 HOUR);
```

### Review MikroTik Errors

```sql
-- Check if MikroTik connectivity issues
SELECT COUNT(*) as mikrotik_errors
FROM access_log
WHERE action LIKE 'MIKROTIK_ERROR%'
AND timestamp > DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

---

## 📋 Configuration Changes

### To Change Quiz Pass Rate

Edit `config.php`:

```php
define('QUIZ_PASS_PERCENTAGE', 0.75); // Change to 75% (3.75 ≈ 4 out of 5)
```

### To Change Max Daily Quizzes

Edit `config.php`:

```php
define('MAX_FREE_ACCESS_PER_DAY', 2); // Allow 2 attempts per day
```

### To Change Access Duration

Edit `config.php`:

```php
define('QUIZ_ACCESS_TIME_MINUTES', 120); // Change from 60 to 120 minutes
```

### To Update MikroTik Details

Edit `config.php`:

```php
define('MIKROTIK_IP', 'new-ip-here');
define('MIKROTIK_USER', 'new-user');
define('MIKROTIK_PASS', 'new-password');
```

---

## 📞 Database Maintenance

### Check Database Size

```sql
SELECT
  SUM(round(((data_length + index_length) / 1024 / 1024), 2)) as "Size in MB"
FROM information_schema.TABLES
WHERE table_schema = 'aralinks';
```

### Optimize Tables (Improves Performance)

```sql
OPTIMIZE TABLE users;
OPTIMIZE TABLE vouchers;
```

### Repair Corrupted Tables (If Needed)

```sql
REPAIR TABLE users;
REPAIR TABLE vouchers;
```

---

## 🔐 Password Reset (If Needed)

### Reset User Password (MikroTik)

Update in `config.php`:

```php
define('MIKROTIK_PASS', 'NewPasswordHere');
```

Then restart MikroTik connection test.

---

**Last Updated:** February 23, 2026
**For Support:** Contact your ICT Department
