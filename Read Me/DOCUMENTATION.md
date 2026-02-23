# ARALINKS - Complete Setup & Documentation

## Project Overview

ARALINKS is an internet advocacy platform designed for Comon High School's MikroTik-powered WiFi network. It provides a dual-access system where students can:

- **Answer internet advocacy quizzes** (5 randomly selected questions, need 4/5 correct) for **1 hour free access**
- **Use paid vouchers** with customizable durations

## 🔒 Security Improvements Made

### 1. **SQL Injection Prevention**

- ✅ All database queries now use **prepared statements with parameterized queries**
- ✅ Fixed critical vulnerability in `validate.php`
- ✅ Prevents malicious code injection

### 2. **CSRF Protection**

- ✅ Added CSRF tokens to all forms (`voucher.php`)
- ✅ Tokens validated before processing
- ✅ Protects against cross-site request forgery attacks

### 3. **Input Validation & Sanitization**

- ✅ Voucher codes now validated with regex pattern: `^[A-Za-z0-9]{5,20}$`
- ✅ HTML special characters escaped with `htmlspecialchars()`
- ✅ All user inputs trimmed and validated

### 4. **Rate Limiting**

- ✅ Voucher attempts limited to 5 per 5-minute window
- ✅ Prevents brute force attacks
- ✅ Allows only 1 free quiz access per IP per day

### 5. **Secure Credential Management**

- ✅ MikroTik credentials moved to `config.php` instead of hardcoded
- ✅ All sensitive data centralized in configuration file
- ✅ Easy to change credentials without editing multiple files

### 6. **Error Handling**

- ✅ Production mode hides database errors from users
- ✅ All errors logged to `logs/access.log`
- ✅ Debug mode available for development

### 7. **Logging & Audit Trail**

- ✅ All access attempts logged with timestamp, IP, and action
- ✅ Security events flagged (SECURITY, ERROR, WARNING levels)
- ✅ Helps track suspicious activity

---

## 📱 Mobile Responsiveness Improvements

### What's Fixed:

- ✅ **Added viewport meta tag** to all pages for proper mobile rendering
- ✅ **Responsive modals** that adjust padding/width on phones
- ✅ **Mobile-optimized forms** with proper input sizing
- ✅ **Touch-friendly buttons** with larger padding
- ✅ **Progress indicators** in quiz
- ✅ **Prevents iOS zoom** on input focus (font-size: 16px)

### Breakpoints:

- 📱 **480px and below**: Mobile phones (extra compact)
- 📱 **481px - 768px**: Tablets (medium)
- 🖥️ **769px+**: Desktop/large screens

---

## 🔧 How to Set Up the Database

### 1. **Create Database**

```sql
CREATE DATABASE aralinks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aralinks;
```

### 2. **Create Users Table** (Access logs)

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  device_ip VARCHAR(45) NOT NULL,
  device_mac VARCHAR(17),
  access_type ENUM('quiz', 'voucher') DEFAULT 'quiz',
  score INT,
  total_questions INT,
  last_access TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_device_ip (device_ip),
  INDEX idx_last_access (last_access)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. **Create Vouchers Table**

```sql
CREATE TABLE vouchers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) UNIQUE NOT NULL,
  duration INT NOT NULL DEFAULT 60,
  used TINYINT DEFAULT 0,
  used_by_ip VARCHAR(45),
  used_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_code (code),
  INDEX idx_used (used),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 4. **Sample Insert Vouchers**

```sql
INSERT INTO vouchers (code, duration) VALUES
('ARALINK001', 120),
('ARALINK002', 180),
('ARALINK003', 60),
('ARALINK004', 240),
('ARALINK005', 90);
```

---

## ⚙️ Configuration File (`config.php`)

Located at the root. All sensitive settings are here:

```php
// Database
DB_HOST, DB_USER, DB_PASS, DB_NAME

// MikroTik Router
MIKROTIK_IP, MIKROTIK_USER, MIKROTIK_PASS, MIKROTIK_PORT

// Quiz Settings
QUIZ_QUESTIONS_COUNT = 5
QUIZ_PASS_PERCENTAGE = 0.8 (80% = 4/5 questions)
QUIZ_ACCESS_TIME_MINUTES = 60

// Voucher Settings
MAX_VOUCHER_ATTEMPTS = 5
VOUCHER_ATTEMPT_TIMEOUT = 300 seconds

// Logging
ENABLE_LOGGING = true
LOG_FILE = /logs/access.log
```

---

## 📋 File Structure

```
aralinks/
├── config.php                 # Configuration (MikroTik, DB, settings)
├── functions.php              # Utility functions (logging, CSRF, rate limiting)
├── db.php                      # Database connection
├── index.php                   # Home page with modal
├── voucher.php                 # Voucher code form
├── validate.php               # Voucher validation (FIXED: SQL injection)
├── style.css                   # Global styles (responsive)
├── readme.txt                  # Original documentation
│
├── quizzes/
│   ├── quiz.php               # Quiz display (80% pass rate, progress bar)
│   ├── process.php            # Quiz processing (improved error handling)
│   ├── success.php            # Success page (enhanced)
│   └── quiz-failed.php        # Failure page (NEW - better UX)
│
├── assets/
│   └── images/
│       └── background.png
│
└── logs/
    └── access.log             # Auto-generated access logs
```

---

## 🎯 Quiz System Changes

### OLD SYSTEM:

- Required 100% score (5/5 questions) to pass
- Only showed "passed" or "failed"
- No score feedback

### NEW SYSTEM:

- ✅ Requires only **80% score (4/5 questions)** to pass
- ✅ Shows **score breakdown** on failure page
- ✅ **Progress bar** during quiz
- ✅ **Question counter** (e.g., "Question 2 of 5")
- ✅ Better feedback with tips to improve
- ✅ Handles duplicate daily access gracefully

---

## 🎫 Voucher System Improvements

### Security:

- ✅ **SQL Injection fixed** with prepared statements
- ✅ **Rate limiting** prevents brute force (5 attempts per 5 min)
- ✅ **Format validation** ensures code is alphanumeric
- ✅ **Audit logging** tracks who used which voucher

### User Experience:

- ✅ Better error messages (distinguish empty code, invalid format, already used)
- ✅ Input validation before database query
- ✅ Clear instructions on form
- ✅ Mobile-friendly interface

---

## 📊 MikroTik Integration

### How It Works:

1. **Student passes quiz** or redeems voucher
2. **Device MAC address captured** from URL parameter
3. **API call to MikroTik** via socket connection
4. **Device added to IP-Binding bypass list** for specified duration
5. **Access log saved** to database

### Configuration:

- Edit `config.php` with your MikroTik details:
  ```php
  MIKROTIK_IP = "192.168.40.177"
  MIKROTIK_USER = "ComonHS"
  MIKROTIK_PASS = "@comonhs.mikrotik"
  ```

### Error Handling:

- If MikroTik connection fails, access is still logged
- Admin can manually add devices if needed
- Non-blocking: doesn't prevent success page

---

## 📝 Logging System

### Log Location: `/logs/access.log`

### Log Entries Include:

- **Timestamp**: YYYY-MM-DD HH:MM:SS
- **Level**: INFO, WARNING, ERROR, SECURITY
- **IP Address**: Student device IP
- **Action**: What happened
- **Details**: Additional info

### Example Logs:

```
[2025-02-23 14:35:22] [INFO] [192.168.1.100] PAGE_VISIT - User Agent: Mozilla/5.0...
[2025-02-23 14:35:45] [INFO] [192.168.1.100] QUIZ_STARTED - IP: 192.168.1.100
[2025-02-23 14:38:22] [INFO] [192.168.1.100] QUIZ_COMPLETED - Score: 4/5 (Required: 4)
[2025-02-23 14:38:23] [INFO] [192.168.1.100] QUIZ_ACCESS_GRANTED - Score: 4/5
[2025-02-23 14:38:24] [INFO] [192.168.1.100] MIKROTIK_AUTH_SUCCESS - MAC: AA:BB:CC:DD:EE:FF
[2025-02-23 14:40:15] [SECURITY] [192.168.1.101] RATE_LIMIT_EXCEEDED - Voucher validation attempts exceeded
```

---

## 🛠️ Future Enhancements

1. **Admin Dashboard**
   - View analytics (quiz pass rates, voucher usage)
   - Create/manage vouchers
   - View access logs with filtering

2. **Question Management**
   - Store questions in database
   - Add/edit/delete questions from admin panel
   - Track question statistics

3. **User Feedback**
   - Show which answers were correct/incorrect
   - Provide explanations for each question
   - Print certificate on pass

4. **Advanced Security**
   - 2FA for special access
   - Device fingerprinting
   - IP geolocation check

5. **Analytics**
   - Peak usage times
   - Success rate trends
   - Popular/unpopular questions

---

## 🚨 Deployment Checklist

Before going live:

- [ ] Update `config.php` with real MikroTik IP and credentials
- [ ] Update database name and credentials in `config.php`
- [ ] Run database setup SQL queries
- [ ] Ensure `/logs` directory is writable
- [ ] Set `SHOW_ERRORS_TO_USER = false` in config
- [ ] Set `DEBUG_MODE = false` in config
- [ ] Test quiz flow end-to-end
- [ ] Test voucher flow with test codes
- [ ] Verify MikroTik integration works
- [ ] Check logs are being written
- [ ] Test on mobile devices
- [ ] Set up regular log rotation

---

## 📞 Troubleshooting

### Quiz not submitting?

- Check browser console for JavaScript errors
- Verify session is enabled in PHP
- Check `input[name="answer"]` is present

### Voucher not working?

- Verify code exists: `SELECT * FROM vouchers WHERE code='xxx'\G`
- Check if already used: `SELECT used FROM vouchers WHERE code='xxx'`
- Verify CSRF token in form

### MikroTik not authenticating?

- Test connection: `telenet 192.168.40.177 8728`
- Verify credentials in `config.php`
- Check socket firewall rules
- Review logs for errors

### Access logs not being written?

- Verify `/logs` directory exists and is writable
- Check `ENABLE_LOGGING = true` in `config.php`
- Verify PHP has write permissions

---

**Project by:** Dexter B. Cargullo  
**School:** Comon High School  
**Date Updated:** February 23, 2026
