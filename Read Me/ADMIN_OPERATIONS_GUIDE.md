# 🖥️ ARALINKS Admin Guide

## Overview

This guide helps you monitor, manage, and troubleshoot the ARALINKS system once it's deployed with MikroTik Hotspot.

---

## 📊 Dashboard & Monitoring

### 1. System Status Page

- **URL**: `http://your-server/aralinks/system-status.php`
- **Purpose**: Real-time health check of all components
- **Tests Performed**:
  - ✅ Configuration validation
  - ✅ MikroTik API connectivity
  - ✅ Database connectivity
  - ✅ File permissions

**What to Watch For**:

- 🔴 Red indicators = Immediate action required
- 🟡 Yellow indicators = Configuration issues
- 🟢 Green indicators = OK

---

### 2. Access Logs

Located at: `/logs/access.log`

**Log Format**:

```
[TIMESTAMP] [LEVEL] | IP: X.X.X.X | MAC: XX:XX:XX:XX:XX:XX | ACTION: [event] | DETAILS: [info]
```

**Typical Event Sequence** (Successful Quiz):

```
[2024-01-15 14:23:45] [INFO] | IP: 192.168.1.100 | MAC: AA:BB:CC:DD:EE:FF | ACTION: QUIZ_STARTED | DETAILS: 5 questions selected
[2024-01-15 14:25:12] [INFO] | IP: 192.168.1.100 | MAC: AA:BB:CC:DD:EE:FF | ACTION: QUIZ_COMPLETED | DETAILS: Score 4/5 (80%)
[2024-01-15 14:25:13] [INFO] | IP: 192.168.1.100 | MAC: AA:BB:CC:DD:EE:FF | ACTION: QUIZ_ACCESS_GRANTED | DETAILS: 60 minutes access
[2024-01-15 14:25:14] [INFO] | IP: 192.168.1.100 | MAC: AA:BB:CC:DD:EE:FF | ACTION: MIKROTIK_AUTH_SUCCESS | DETAILS: Device added to bypass IP binding
```

**Key Events to Monitor**:

| Event                     | Meaning                    | Action                        |
| ------------------------- | -------------------------- | ----------------------------- |
| QUIZ_STARTED              | Student began quiz         | Normal                        |
| QUIZ_ACCESS_GRANTED       | Student passed             | Normal                        |
| QUIZ_FAILED               | Student scored <80%        | Check if questions are fair   |
| DUPLICATE_ACCESS_SAME_DAY | MAC tried quiz twice       | Blocked (expected)            |
| RATE_LIMIT_EXCEEDED       | Too many voucher attempts  | Possible brute force attack   |
| INVALID_MAC               | MAC format wrong           | Check MikroTik hotspot config |
| MIKROTIK_AUTH_ERROR       | Could not authorize device | Check API connectivity        |
| CSRF_TOKEN_INVALID        | Form tampering detected    | Possible attack (rare)        |
| SECURITY:                 | Any security event         | ⚠️ Investigate immediately    |

**How to View Logs**:

```bash
# Last 20 entries
tail -20 /var/www/html/aralinks/logs/access.log

# Today's activity
grep "$(date +%Y-%m-%d)" /var/www/html/aralinks/logs/access.log

# Search for errors
grep "ERROR\|FAILED" /var/www/html/aralinks/logs/access.log

# Watch in real-time
tail -f /var/www/html/aralinks/logs/access.log
```

---

## 🗄️ Database Management

### Connect to Database

Using PHPMyAdmin:

```
URL: http://localhost/phpmyadmin
Username: root
Password: [blank or your password]
Database: aralinks
```

### Key Queries

**1. Today's Access Statistics**

```sql
SELECT
    DATE(last_access) AS date,
    access_type,
    COUNT(*) AS count,
    ROUND(AVG(score), 2) AS avg_score
FROM users
WHERE DATE(last_access) = CURDATE()
GROUP BY DATE(last_access), access_type;
```

**Expected Output**:

```
| date       | access_type | count | avg_score |
|------------|-------------|-------|-----------|
| 2024-01-15 | quiz        | 23    | 3.87      |
| 2024-01-15 | voucher     | 5     | NULL      |
```

**2. Identify Suspicious Activity** (Multiple attempts same MAC)

```sql
SELECT
    device_mac,
    COUNT(*) AS attempts,
    GROUP_CONCAT(DATE_FORMAT(last_access, '%H:%i') SEPARATOR ', ') AS times
FROM users
WHERE DATE(last_access) = CURDATE()
GROUP BY device_mac
HAVING COUNT(*) > 1
ORDER BY attempts DESC;
```

**What This Shows**: Students trying to access multiple times (should be blocked, indicates possible issue)

**3. Voucher Usage Report**

```sql
SELECT
    code,
    duration,
    used,
    used_by_mac,
    used_by_ip,
    DATE_FORMAT(used_at, '%Y-%m-%d %H:%i') AS redeemed_at
FROM vouchers
WHERE used = 1
ORDER BY used_at DESC
LIMIT 20;
```

**4. All Unused Vouchers**

```sql
SELECT code, duration
FROM vouchers
WHERE used = 0
ORDER BY code;
```

**5. Device That Accessed Most**

```sql
SELECT
    device_mac,
    COUNT(*) AS total_access,
    access_type,
    MAX(last_access) AS last_access_time,
    SUM(CASE WHEN access_type='quiz' THEN 1 ELSE 0 END) AS quiz_count,
    SUM(CASE WHEN access_type='voucher' THEN 1 ELSE 0 END) AS voucher_count
FROM users
WHERE DATE(last_access) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY device_mac
ORDER BY total_access DESC
LIMIT 10;
```

### Database Maintenance

**Weekly Tasks**:

```sql
-- Backup before major changes
-- Check for orphaned records
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM vouchers;
SELECT COUNT(*) FROM access_log;

-- Verify no NULL MACs (data integrity)
SELECT COUNT(*) FROM users WHERE device_mac IS NULL;
```

**Monthly Tasks**:

```sql
-- Archive old logs (optional)
DELETE FROM access_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Generate performance report
SELECT
    DATE(last_access) AS day,
    COUNT(*) AS total_users,
    AVG(score) AS avg_quiz_score,
    SUM(CASE WHEN access_type='quiz' THEN 1 ELSE 0 END) AS quiz_access,
    SUM(CASE WHEN access_type='voucher' THEN 1 ELSE 0 END) AS voucher_access
FROM users
WHERE last_access >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(last_access)
ORDER BY day;
```

---

## 🎯 Quiz Management

### Current Settings (in config.php)

```php
define('QUIZ_QUESTIONS_COUNT', 5);        // 5 questions per session
define('QUIZ_PASS_PERCENTAGE', 0.8);      // 80% = 4/5 questions correct
define('QUIZ_ACCESS_TIME_MINUTES', 60);   // 60 minutes access if pass
```

### Question Bank

All questions stored in: `/quizzes/quiz.php` lines 40-95

**Current Questions** (50 total on topics):

- Internet Safety & Passwords
- Cyberbullying & Netiquette
- Phishing & Social Engineering
- Copyright & Intellectual Property
- Privacy & Data Protection

### To Modify Pass Rate

Edit `config.php`:

```php
// Change from 0.8 (80%) to:
define('QUIZ_PASS_PERCENTAGE', 0.6);  // 60% (3/5 questions)
define('QUIZ_PASS_PERCENTAGE', 1.0);  // 100% (5/5 questions - harder)
define('QUIZ_PASS_PERCENTAGE', 0.4);  // 40% (2/5 questions - easier)
```

### To Add Questions

Edit `/quizzes/quiz.php` starting at line 40:

```php
$questions = [
    // ... existing questions ...

    // Add new question like this:
    [
        'question' => 'What is the best way to protect your password?',
        'options' => [
            'A' => 'Use your birthday',
            'B' => 'Use a strong combination of letters, numbers, and symbols',
            'C' => 'Write it on a sticky note',
            'D' => 'Share with friends'
        ],
        'correct' => 'B'
    ],

    // ... more questions ...
];
```

Then update this line with total question count:

```php
$totalQuestions = count($questions);  // Auto-calculates
```

---

## 🔐 Voucher Management

### Generate Test Vouchers

Use PHPMyAdmin or SQL:

```sql
-- Insert single voucher (60 min free access)
INSERT INTO vouchers (code, duration, used, created_at)
VALUES ('ADMIN001', 60, 0, NOW());

-- Insert batch (Teacher codes for testing)
INSERT INTO vouchers (code, duration, used, created_at) VALUES
('TEACHER10', 60, 0, NOW()),
('TEACHER11', 60, 0, NOW()),
('TEACHER12', 60, 0, NOW());

-- Insert promotional vouchers (120 min)
INSERT INTO vouchers (code, duration, used, created_at) VALUES
('PROMO01', 120, 0, NOW()),
('PROMO02', 120, 0, NOW());
```

### Voucher Code Format

- Alphanumeric only (A-Z, 0-9)
- 5-20 characters recommended
- Format validated with regex: `^[A-Z0-9]{5,20}$`

### Track Voucher Usage

View in PHPMyAdmin:

- **columns**: code, used, used_by_mac, used_by_ip, used_at
- **used** = 0 (not yet used), 1 (redeemed)
- **used_by_mac** = Device that redeemed it
- **used_at** = When it was redeemed

---

## 🖧 MikroTik Integration

### Check MikroTik API Connection

Visit: `http://your-server/aralinks/system-status.php`

Should show:

- ✅ MikroTik IP
- ✅ MikroTik Port (8728)
- ✅ API User
- ✅ Connection Status

### Manual API Test

If PHP can't connect to MikroTik:

1. **Test Connectivity** (from server):

```bash
telnet MIKROTIK_IP 8728
# If you get "Connected to...", connection works
# Press Ctrl+] then quit
```

2. **Verify API User** (on MikroTik - WinBox):

- Go to System → Users
- Look for user "aralinks"
- Verify "API" checkbox is enabled
- Check password is correct

3. **Check Firewall** (on MikroTik - WinBox):

- Go to IP → Firewall → Filter Rules
- Make sure port 8728 is not blocked
- Or add rule: `Chain=input In.Interface=bridge Protocol=tcp Dst.Port=8728 Action=accept`

### View Devices on MikroTik

In WinBox, Hotspot → Bindings:

- Shows all devices with access authorization
- MAC and IP binding
- Access duration remaining

---

## 🚨 Troubleshooting

### Problem: "MAC not captured"

**Symptoms**:

- Users see "Invalid MAC" error
- device-info.php shows "Not Captured"

**Solutions**:

1. Check hotspot login URL includes `$(client-mac)` parameter
2. Reload hotspot profile
3. In MikroTik WinBox → IP → Hotspot → Host Profiles → [profile]
   - Redirect URL must be: `http://server-ip/aralinks/quizzes/quiz.php?ip=$(client-ip)&mac=$(client-mac)`

### Problem: "MikroTik connection error"

**Symptoms**:

- Access Granted page shows error
- Log shows "MIKROTIK_AUTH_ERROR"

**Solutions**:

1. Verify MikroTik is running: Ping the IP
2. Verify API user exists and password is correct
3. Test connection: Visit system-status.php
4. Check firewall isn't blocking port 8728

### Problem: "Quiz access not working but pass succeeded"

**Symptoms**:

- Student passes quiz but can't access internet
- Log shows "QUIZ_ACCESS_GRANTED" but no internet

**Solutions**:

1. Check MikroTik API didn't encounter error (look for "MIKROTIK_AUTH_ERROR" in logs)
2. Verify device IP binding created successfully in MikroTik
3. Check hotspot service is running on MikroTik

### Problem: "Student accessing multiple times per day"

**Symptoms**:

- Log shows multiple "QUIZ_ACCESS_GRANTED" for same MAC
- Student shouldn't be able to access twice

**Solutions**:

1. This should be blocked! Run query:
   ```sql
   SELECT device_mac FROM users
   WHERE DATE(last_access)=CURDATE()
   GROUP BY device_mac HAVING COUNT(*)>1;
   ```
2. If you see results, it means:
   - MikroTik time not synced with server (run WinBox → System → Clock → Sync from Network)
   - Or there's a bug - contact support

### Problem: "Can't reach system-status.php"

**Symptoms**:

- "404 Not Found" error
- File doesn't exist

**Solutions**:

1. Verify file exists: Check file browser at `c:\xampp\htdocs\aralinks\system-status.php`
2. If missing, download from project repository
3. Permissions: File should be readable (644 or 755)

---

## 📱 Device Verification

### Student Tool: device-info.php

**URL**: `http://your-server/aralinks/device-info.php`

**What It Shows**:

- Device IPv4 address
- MAC address (if captured)
- Status indicator:
  - 🟢 Green = MAC captured successfully
  - 🟡 Yellow = MAC not available

**Use Case**: Have student visit this page to verify their MAC is being captured by hotspot

---

## 👥 User Management

### View All Users

```sql
SELECT
    device_mac,
    COUNT(*) AS total_access,
    GROUP_CONCAT(DISTINCT access_type) AS access_methods,
    MAX(last_access) AS last_access_time
FROM users
GROUP BY device_mac
ORDER BY last_access_time DESC;
```

### Clear User (Reset Daily Limit)

⚠️ **Use Carefully** - Allows student to access again same day

```sql
DELETE FROM users
WHERE device_mac = 'AA:BB:CC:DD:EE:FF'
AND DATE(last_access) = CURDATE();
```

### Block User (No Access)

Manually remove from MikroTik (until next day when limit resets automatically):

1. WinBox → IP → Hotspot → Bindings
2. Right-click device → Remove

Or delete from database:

```sql
DELETE FROM users WHERE device_mac = 'AA:BB:CC:DD:EE:FF';
```

---

## 📈 Performance Monitoring

### Daily Checks

```
□ Check system-status.php shows all green
□ Review access logs for any ERRORs
□ Verify MikroTik is online (ping test)
□ Sample test: Pass a quiz, verify internet access works
```

### Weekly Checks

```
□ Run access statistics query (see section above)
□ Check database size (shouldn't grow excessively)
□ Review suspicious activity (multiple attempts)
□ Verify log file size (archive if >10MB)
```

### Monthly Tasks

```
□ Update question bank if needed
□ Review voucher inventory (create new batch if needed)
□ Database maintenance (backup, cleanup old logs)
□ Update documentation if system changes
```

---

## 🔧 Advanced Configuration

### Increase Quiz Access Time

Edit `config.php`:

```php
define('QUIZ_ACCESS_TIME_MINUTES', 120);  // Changed from 60 to 120 min
```

### Modify Rate Limiting

Edit `config.php`:

```php
define('MAX_VOUCHER_ATTEMPTS', 10);       // Allow 10 attempts instead of 5
define('RATE_LIMIT_WINDOW', 600);         // Per 10 minutes instead of 5
```

### Change Quiz Questions Count

Edit `config.php`:

```php
define('QUIZ_QUESTIONS_COUNT', 10);       // 10 questions instead of 5
```

⚠️ If you change this, may need to adjust QUIZ_PASS_PERCENTAGE:

```php
define('QUIZ_PASS_PERCENTAGE', 0.7);      // 70% of 10 = 7/10
```

---

## 📞 Support Contacts

### Common Issues

1. **MikroTik Networking Issues**: Contact MikroTik support or check www.mikrotik.com/docs
2. **PHP/Database Issues**: Check server logs in XAMPP Control Panel
3. **Hotspot Redirect Issues**: Verify redirect URL has `$(client-mac)` parameter

### Log Locations

- **PHP Errors**: `c:\xampp\apache\logs\error.log`
- **Database Errors**: `c:\xampp\mysql\data\*.err`
- **ARALINKS Logs**: `c:\xampp\htdocs\aralinks\logs\access.log`

---

## 📋 Quick Reference

| Component      | Status URL           | Purpose                  |
| -------------- | -------------------- | ------------------------ |
| System Health  | `/system-status.php` | Check all components     |
| Device Info    | `/device-info.php`   | Verify MAC capture       |
| Database Setup | `/setup_tables.php`  | Initialize/repair tables |
| Quiz           | `/quizzes/quiz.php`  | Test quiz flow           |
| Voucher        | `/voucher.php`       | Test voucher redemption  |

---

## Version Info

- **System**: ARALINKS v1.0
- **Last Updated**: 2024
- **Compatibility**: MikroTik Hex GR3 with RouterOS 7.x+
- **Requirements**: PHP 7.4+, MySQL 5.7+
