# 🎓 ARALINKS WiFi Advocacy System

**Free WiFi Access Through Internet Advocacy Education**

An innovative platform that combines educational accountability with WiFi access. Students must demonstrate responsible internet use knowledge to earn free WiFi access through a 5-question multiple-choice quiz or by using paid voucher codes.

---

## 🚀 Quick Start

### For First-Time Users:

1. **Read the Improvements** (5 minutes)
   - Open: [IMPROVEMENTS_SUMMARY.md](IMPROVEMENTS_SUMMARY.md)

2. **Set Up Database** (5 minutes)
   - Use: [database_setup.sql](database_setup.sql)
   - Run in phpMyAdmin or: `mysql aralinks < database_setup.sql`

3. **Configure Settings** (2 minutes)
   - Edit: [config.php](config.php)
   - Update MikroTik IP, username, password

4. **Test the System** (5 minutes)
   - Visit: `http://localhost/aralinks/`
   - Try quiz (answer 4/5 correctly to pass)
   - Try voucher (use code: TEST001)

---

## 📖 Documentation Files

| File                                                   | Purpose                          |
| ------------------------------------------------------ | -------------------------------- |
| **[IMPROVEMENTS_SUMMARY.md](IMPROVEMENTS_SUMMARY.md)** | ⭐ Overview of all improvements  |
| **[DOCUMENTATION.md](DOCUMENTATION.md)**               | Complete technical documentation |
| **[ADMIN_GUIDE.md](ADMIN_GUIDE.md)**                   | Quick reference for admins       |
| **[database_setup.sql](database_setup.sql)**           | Database creation script         |

---

## 🔐 Security Features

✅ **SQL Injection Prevention** - All queries use prepared statements  
✅ **CSRF Protection** - Token-based form validation  
✅ **Rate Limiting** - Prevents brute force attacks (5 attempts per 5 min)  
✅ **Input Validation** - Format checking and sanitization  
✅ **Secure Credentials** - Centralized in config.php  
✅ **Error Logging** - Comprehensive audit trail  
✅ **Secure Database** - Proper character encoding and indexes

---

## 📱 Responsive Design

✅ **Full Mobile Support** - Works perfectly on phones  
✅ **Tablet Optimized** - Great on iPads and tablets  
✅ **Desktop Ready** - Professional on large screens  
✅ **Touch Friendly** - Large buttons and inputs  
✅ **Progress Indicators** - Shows quiz progress

---

## 🎯 Quiz System

**5 Random Questions** covering internet safety, netiquette, cybersecurity

**Pass Requirement**: 4/5 questions correct (80%)  
**Access Duration**: 1 hour (configurable)  
**Limit**: 1 free quiz per IP per day

**Topics Covered**:

- Strong passwords and 2FA
- Netiquette and respectful online behavior
- Cyberbullying recognition
- Phishing and malware protection
- Copyright and intellectual property
- Online privacy and security

---

## 🎫 Voucher System

**Flexible Access** - Custom duration vouchers  
**Format**: 5-20 alphanumeric character codes  
**Rate Limited**: 5 attempts per 5 minutes  
**Logged**: All voucher usage tracked

**Create New Vouchers**:

```sql
INSERT INTO vouchers (code, duration) VALUES ('NEWCODE123', 120);
```

---

## ⚙️ Quick Configuration

Edit [config.php](config.php) to:

```php
// MikroTik Router Connection
define('MIKROTIK_IP', '192.168.40.177');
define('MIKROTIK_USER', 'ComonHS');
define('MIKROTIK_PASS', '@comonhs.mikrotik');

// Quiz Settings
define('QUIZ_PASS_PERCENTAGE', 0.8);      // 80% = 4/5 questions
define('QUIZ_ACCESS_TIME_MINUTES', 60);   // 1 hour

// Security
define('ENABLE_LOGGING', true);
define('SHOW_ERRORS_TO_USER', false);     // Set to false in production
```

---

## 📊 Monitoring & Analytics

### Check Today's Statistics

```sql
-- View today's quiz access
SELECT COUNT(*) as attempts,
       SUM(CASE WHEN score >= 4 THEN 1 ELSE 0 END) as passed
FROM users
WHERE access_type = 'quiz' AND DATE(last_access) = CURDATE();

-- View today's voucher usage
SELECT COUNT(*) as redeemed, SUM(duration) as total_minutes
FROM vouchers
WHERE used = 1 AND DATE(used_at) = CURDATE();
```

### Monitor Logs

Check `/logs/access.log` for:

- ✅ Security events
- ✅ Quiz completions
- ✅ Voucher redemptions
- ✅ Error conditions

---

## 🛠️ Common Admin Tasks

### Create Vouchers for Testing

```sql
INSERT INTO vouchers (code, duration) VALUES
('APRIL2025-001', 60),
('APRIL2025-002', 120),
('APRIL2025-003', 180);
```

### View Unused Vouchers

```sql
SELECT code, duration
FROM vouchers
WHERE used = 0
ORDER BY created_at DESC;
```

### Mark Voucher as Used (Manual)

```sql
UPDATE vouchers
SET used = 1, used_by_ip = '192.168.1.100', used_at = NOW()
WHERE code = 'TESTCODE';
```

### View Access Statistics

```sql
SELECT DATE(last_access) as date, COUNT(*) as access_count
FROM users
GROUP BY DATE(last_access)
ORDER BY date DESC
LIMIT 7;
```

See [ADMIN_GUIDE.md](ADMIN_GUIDE.md) for more queries.

---

## 🚀 Deployment Checklist

- [ ] Database created and tables initialized
- [ ] MikroTik credentials updated in config.php
- [ ] Database credentials updated in config.php
- [ ] `/logs` directory exists and is writable
- [ ] SHOW_ERRORS_TO_USER set to false
- [ ] DEBUG_MODE set to false
- [ ] Tested quiz flow (pass with 4/5, fail with 3/5)
- [ ] Tested voucher redemption
- [ ] Verified MikroTik integration works
- [ ] Checked logs are being written
- [ ] Tested on mobile device

---

## 📞 Troubleshooting

### Quiz not submitting?

- Check browser console for errors
- Verify session is enabled
- Check if answer name attribute exists

### Voucher not working?

- Verify code exists in database
- Check if code is already used
- Review logs for specific error

### MikroTik not connecting?

- Verify IP address is correct
- Check credentials in config.php
- Test connectivity: `telenet 192.168.40.177 8728`

### Logs not being written?

- Verify `/logs` directory exists
- Check directory is writable
- Verify ENABLE_LOGGING = true

See [DOCUMENTATION.md](DOCUMENTATION.md) for complete troubleshooting guide.

---

## 📁 Project Structure

```
aralinks/
├── 📄 config.php                    ← Configure here!
├── 📄 functions.php                 (Utility functions)
├── 📄 db.php                        (Database connection)
├── 📄 index.php                     (Home page)
├── 📄 voucher.php                   (Voucher form)
├── 📄 validate.php                  (Voucher processing)
├── 📄 style.css                     (Styling)
│
├── 📁 quizzes/
│   ├── quiz.php                     (Quiz display)
│   ├── process.php                  (Quiz processing)
│   ├── success.php                  (Success page)
│   └── quiz-failed.php              (Failure feedback)
│
├── 📁 assets/images/
│   └── background.png               (Background image)
│
├── 📁 logs/                         (Auto-generated)
│   └── access.log
│
├── 📄 database_setup.sql            (Database schema)
├── 📄 DOCUMENTATION.md              (Full docs)
├── 📄 ADMIN_GUIDE.md                (Admin reference)
├── 📄 IMPROVEMENTS_SUMMARY.md       (What changed)
└── 📄 README.md                     (This file)
```

---

## 🎓 Educational Content

The quiz covers 50 questions about:

### Online Safety (10 questions)

- Password security
- Phishing recognition
- Malware protection
- 2FA importance

### Digital Citizenship (10 questions)

- Netiquette
- Respectful behavior
- Cyberbullying awareness
- Privacy basics

### Intellectual Property (10 questions)

- Copyright respect
- Citation importance
- Plagiarism prevention
- Creative commons

### Online Privacy (10 questions)

- Personal information protection
- Social media privacy
- Location sharing risks
- Account security

### General Responsibility (10 questions)

- Screen time balance
- Positivity online
- Information verification
- Digital footprint awareness

---

## 🔄 Integration with MikroTik

When a student passes the quiz or redeems a voucher:

1. ✅ Device info (MAC, IP) captured
2. ✅ API call sent to MikroTik router
3. ✅ Device added to bypass list
4. ✅ Access granted for specified duration
5. ✅ Event logged with timestamp

---

## 📈 Analytics & Reporting

Data available for:

- Quiz pass/fail rates
- Voucher redemption patterns
- Peak usage times
- Device distribution
- Geographic patterns
- Repeat users

---

## 🔒 Compliance & Safety

✅ **FERPA Compliant** - Student data handled securely  
✅ **Privacy Protected** - Minimal data collection  
✅ **Audit Trail** - All actions logged  
✅ **Configurable** - Easy to adjust policies

---

## 📞 Support

### For Configuration Issues

- Check [config.php](config.php) comments
- Review [DOCUMENTATION.md](DOCUMENTATION.md) configuration section

### For Database Issues

- Run [database_setup.sql](database_setup.sql) to reset
- Check MySQL/PHP error logs

### For Security Concerns

- Review [DOCUMENTATION.md](DOCUMENTATION.md) security section
- Check [logs/access.log](logs/access.log) for suspicious activity

### For Feature Requests

- See [DOCUMENTATION.md](DOCUMENTATION.md) "Future Enhancements" section

---

## 📝 Version Info

- **Version**: 2.0 (Complete Overhaul)
- **Release Date**: February 23, 2026
- **Status**: Production Ready ✅
- **PHP Version**: 7.4+
- **Database**: MySQL 5.7+

---

## 👨‍💻 Credits

**Original Project**: Dexter B. Cargullo  
**Complete Overhaul**: GitHub Copilot  
**Institution**: Comon High School  
**Purpose**: Internet Advocacy & Responsible Use Education

---

## 📄 License

This project is for use by Comon High School. All rights reserved.

---

## 🚦 Getting Help

1. **Start Here**: Read [IMPROVEMENTS_SUMMARY.md](IMPROVEMENTS_SUMMARY.md)
2. **Then Read**: [DOCUMENTATION.md](DOCUMENTATION.md)
3. **For Admin**: See [ADMIN_GUIDE.md](ADMIN_GUIDE.md)
4. **Troubleshooting**: [DOCUMENTATION.md Troubleshooting Section](DOCUMENTATION.md#troubleshooting)

---

**Ready to deploy? Start with the [IMPROVEMENTS_SUMMARY.md](IMPROVEMENTS_SUMMARY.md)!**
