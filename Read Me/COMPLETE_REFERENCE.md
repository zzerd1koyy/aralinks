# 🎯 ARALINKS Complete Reference Guide

**Your WiFi authentication system is deployed. This file shows you how to use everything.**

---

## 📁 What Each File Does

### 🔧 Core System Files (DO NOT DELETE)

| File                      | Purpose                                                       | Edit Needed?                         |
| ------------------------- | ------------------------------------------------------------- | ------------------------------------ |
| `config.php`              | Central configuration (MikroTik IP, passwords, quiz settings) | ✏️ **YES** - Set your values         |
| `db.php`                  | Database connection                                           | ✏️ Maybe - If your DB details change |
| `functions.php`           | Security functions (logging, CSRF, validation)                | ❌ No unless adding features         |
| `index.php`               | Home/main page                                                | ❌ Usually no                        |
| `style.css`               | Styling/responsive design                                     | ✏️ Maybe - If want to rebrand        |
| `quizzes/quiz.php`        | Quiz display page                                             | ✏️ Maybe - To add questions          |
| `quizzes/process.php`     | Quiz logic/grading                                            | ❌ No                                |
| `quizzes/success.php`     | Success/result page                                           | ❌ Usually no                        |
| `quizzes/quiz-failed.php` | Failure feedback page                                         | ❌ Usually no                        |
| `voucher.php`             | Voucher redemption page                                       | ❌ Usually no                        |
| `validate.php`            | Voucher validation backend                                    | ❌ No                                |

### 🛠️ Setup & Diagnosis Tools (Run Once or As Needed)

| File                | Purpose                  | When to Use             | What It Does                               |
| ------------------- | ------------------------ | ----------------------- | ------------------------------------------ |
| `setup_tables.php`  | Database initialization  | First time setup        | Creates users, vouchers, access_log tables |
| `device-info.php`   | MAC capture verification | Troubleshoot MAC issues | Shows your device's MAC address            |
| `system-status.php` | Health check dashboard   | Daily or troubleshoot   | Shows if system is working                 |

### 📚 Documentation Files (Read as Needed)

All documentation files are stored in the Read Me/ folder.

| File                                | Purpose                          | Read When            | Length    |
| ----------------------------------- | -------------------------------- | -------------------- | --------- |
| `Read Me/README.md`                 | Project overview                 | First time           | 5 min     |
| `Read Me/QUICK_START.md`            | Fast setup guide                 | Deploying            | 10 min    |
| `Read Me/DEPLOYMENT_CHECKLIST.md`   | Phase-by-phase testing           | Before going live    | 30 min    |
| `Read Me/MIKROTIK_SETUP.md`         | MikroTik hardware config         | Setting up router    | 45 min    |
| `Read Me/MAC_ADDRESS_GUIDE.md`      | How MAC security works           | Understanding system | 15 min    |
| `Read Me/ADMIN_OPERATIONS_GUIDE.md` | Daily/weekly tasks + SQL queries | Managing system      | 20 min    |
| `Read Me/QUICK_TROUBLESHOOTING.md`  | Step-by-step fixes               | Something broken     | 10-30 min |

### 📁 Directories

```
/logs/                 → access.log file (system activity log)
/assets/               → (future) Images, CSS, fonts
/assets/images/        → Logo, icons
/quizzes/              → Quiz system files
```

---

## 🚀 Quick Start Actions

### 🎯 I Just Downloaded This System - What Do I Do?

**Step 1: Setup (10 minutes)**

```
1. Edit config.php with your MikroTik IP and password
2. Visit http://localhost/aralinks/setup_tables.php
3. Click "Setup Database" button
4. See success message ✅
```

**Step 2: Test Locally (5 minutes)**

```
1. Visit http://localhost/aralinks/quizzes/quiz.php
2. Answer 4/5 questions correctly
3. Should see "Access Granted" ✅
```

**Step 3: Test on WiFi (10 minutes)**

```
1. Connect WiFi from phone
2. Should auto-redirect to quiz
3. Complete quiz
4. Should have internet ✅
```

**Step 4: Go Live**

```
1. Update question bank if desired
2. Generate real vouchers
3. Announce to students
4. Monitor daily with system-status.php
```

---

## 🎮 Common Tasks & How to Do Them

### ❓ "How do I check if everything is working?"

```
→ Visit: http://localhost/aralinks/system-status.php
→ All sections should show ✅ GREEN
→ If any RED: Use QUICK_TROUBLESHOOTING.md
```

### ❓ "How do I create vouchers for students?"

```
→ Use PHPMyAdmin: http://localhost/phpmyadmin
→ Go to: aralinks database → vouchers table → Insert
→ Fill in:
   - code: PROMO001 (5-20 alphanumeric)
   - duration: 60 (minutes)
   - used: 0 (not used yet)
→ Click Insert
→ Give code to students to enter in voucher.php

OR use direct SQL from ADMIN_OPERATIONS_GUIDE.md
```

### ❓ "How do I change quiz difficulty?"

```
→ Edit: config.php
→ Change: define('QUIZ_PASS_PERCENTAGE', 0.8);
→ Values:
   0.6 = Easy (3 out of 5)
   0.8 = Normal (4 out of 5) ← Current
   1.0 = Hard (5 out of 5)
```

### ❓ "How do I add more quiz questions?"

```
→ Edit: /quizzes/quiz.php
→ Scroll to $questions array (around line 40)
→ Add new question in same format
→ System auto-calculates total and picks 5 random

See ADMIN_OPERATIONS_GUIDE.md section "Quiz Management"
```

### ❓ "How do I check what students accessed?"

```
→ PHPMyAdmin → aralinks → users table
→ Shows each device's access history with MAC/IP

Or check /logs/access.log for detailed event log
```

### ❓ "A student says they can't access - what do I do?"

```
Step 1: Check QUICK_TROUBLESHOOTING.md
Step 2: Get student's MAC from device-info.php
Step 3: Look in database for that MAC
Step 4: Check /logs/access.log for errors
Step 5: Follow troubleshooting scenarios based on error

PRO TIP: Most issues = MAC not captured or MikroTik API down
```

### ❓ "How do I reset a student's daily limit (let them quiz again)?"

```
⚠️ Use with caution - this breaks the daily limit system

→ PHPMyAdmin → aralinks → users table
→ Find student's MAC address
→ WHERE device_mac='AA:BB:CC:DD:EE:FF' AND DATE(last_access)=TODAY
→ Delete that row
→ Student can now quiz again today
```

### ❓ "System is slow / lots of errors - what's wrong?"

```
Step 1: Check system-status.php → MikroTik section
  If RED → MikroTik connection issue

Step 2: Check database size
  PHPMyAdmin → aralinks → Check all tables
  If very large → Archive old logs/users

Step 3: Check /logs/access.log for error patterns

Step 4: Check XAMPP Java memory
  XAMPP Control Panel → MySQL → Config → my.ini
  Increase max_connections if many users
```

### ❓ "I want to change the WiFi access duration from 60 to 120 minutes"

```
→ Edit: config.php
→ Change: define('QUIZ_ACCESS_TIME_MINUTES', 120);
→ Save file
→ Next quiz will grant 120 min access
```

### ❓ "I lost all my files accidentally - how do I restore?"

```
If you have BACKUP:
→ Extract fresh copy of all files
→ Database is in XAMPP mysql folder - preserve it
→ Files can be re-downloaded from project

If you DON'T have backup:
→ You'll need to recreate files
→ Contact admin / project repository
```

---

## 📊 Monitoring Dashboard

**Bookmark these URLs for daily monitoring:**

```
System Health:      http://localhost/aralinks/system-status.php
View Today's Users: PHPMyAdmin → aralinks → users table (filter by today)
View Logs:          Read /logs/access.log
Check Database:     http://localhost/phpmyadmin
```

**Daily Check (2 minutes)**:

```
□ system-status.php → All GREEN?
□ PHPMyAdmin → user count increasing?
□ No errors in /logs/access.log?
→ If all yes: ✅ System is healthy
```

---

## 🔑 Key Concepts

### Device Identification: MAC Address

- **What**: Media Access Control - unique hardware identifier of WiFi card
- **Format**: AA:BB:CC:DD:EE:FF (hexadecimal pairs)
- **Why**: Can't fake with VPN, prevents one device from accessing multiple times/day
- **Where**: See it at `/device-info.php`
- **Details**: Read `MAC_ADDRESS_GUIDE.md`

### Access Methods: Quiz vs Voucher

- **Quiz**: Free, one per device per day, requires 4/5 correct answers
- **Voucher**: Paid, any time, just enter code (limited supply)
- **Same device**: Can't do both same day (already has access)
- **Different device**: Can use both same day

### Daily Limit: One Access Per Device Per Day

- **How**: System checks MAC address against today's date
- **Reset**: Automatic at midnight (midnight = 00:00)
- **Override**: As admin, can delete DB entry to reset (not recommended)

### Security Model: Defense in Depth

- **Layer 1**: MAC-based device tracking (prevent spoofing)
- **Layer 2**: Rate limiting (prevent brute force)
- **Layer 3**: CSRF tokens (prevent form hijacking)
- **Layer 4**: Input validation (prevent injection)
- **Layer 5**: Prepared statements (prevent SQL injection)

---

## 📞 Getting Help

### Issue Type → Where to Look

| Problem                  | Look Here                                       |
| ------------------------ | ----------------------------------------------- |
| "System won't start"     | `system-status.php`                             |
| "MAC not captured"       | `QUICK_TROUBLESHOOTING.md` → Issue 2            |
| "Quiz not working"       | `/logs/access.log` + `QUICK_TROUBLESHOOTING.md` |
| "No internet after quiz" | `QUICK_TROUBLESHOOTING.md` → Issue 1            |
| "API connection failed"  | `QUICK_TROUBLESHOOTING.md` → Issue 3            |
| "How to manage users"    | `ADMIN_OPERATIONS_GUIDE.md`                     |
| "How to set up MikroTik" | `MIKROTIK_SETUP.md`                             |
| "Need SQL queries"       | `ADMIN_OPERATIONS_GUIDE.md` → Database section  |

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────────┐
│      Student's Phone/Device             │
│    (WiFi Connected)                     │
└────────────────┬────────────────────────┘
                 │ Connect to WiFi
                 │
┌────────────────▼────────────────────────┐
│    MikroTik Hex GR3 Hotspot             │
│    (Captures MAC, redirects to quiz)    │
└────────────────┬────────────────────────┘
                 │ Redirect to quiz?
                 │
┌────────────────▼────────────────────────┐
│      XAMPP Server (Your Computer)       │
│  ┌──────────────────────────────────┐   │
│  │  quiz.php                        │   │
│  │  (5 random questions)            │   │
│  └──────────────────────────────────┘   │
│             │ Submit answers              │
│             ▼                             │
│  ┌──────────────────────────────────┐   │
│  │  process.php                     │   │
│  │  (Check score, verify MAC)       │   │
│  │  (Call MikroTik API)             │   │
│  └──────────────────────────────────┘   │
│             │ Success                    │
│             ▼                            │
│  ┌──────────────────────────────────┐   │
│  │  MySQL Database                  │   │
│  │  (Store user, usage, logs)       │   │
│  └──────────────────────────────────┘   │
└────────────────┬────────────────────────┘
                 │ Authorization successful
                 │
┌────────────────▼────────────────────────┐
│   MikroTik Hotspot Add IP Binding       │
│   (Device now has internet access)      │
└─────────────────────────────────────────┘
```

---

## 🎓 Learning Path

**New Admin? Start With These:**

```
1. Read:  README.md (5 min) - Overview
2. Read:  QUICK_START.md (10 min) - Basic setup
3. Do:    Follow DEPLOYMENT_CHECKLIST.md (1 hour) - Full test
4. Save:  Bookmark system-status.php (daily check)
5. Learn: Read ADMIN_OPERATIONS_GUIDE.md (as needed)
6. Help:  Use QUICK_TROUBLESHOOTING.md (when issues)
```

---

## 📝 Customization Guide

### I Want To Rebrand The System

```
1. Colors:  Edit style.css (look for color codes like #667eea)
2. Titles:  Edit header text in index.php
3. Logo:    Add to assets/images/ folder
4. Quiz questions: Edit /quizzes/quiz.php
5. Messages: Search for text in PHP files and edit
```

### I Want To Change Quiz Questions

```
1. Open: /quizzes/quiz.php
2. Find: $questions = [ ... ];
3. Add/remove/edit questions in same format
4. Save file
5. Next quiz will use new questions

See ADMIN_OPERATIONS_GUIDE.md for details
```

### I Want To Add HTTPS / SSL

```
1. Get SSL certificate (LetsEncrypt is free)
2. Configure XAMPP Apache for HTTPS
3. Update config.php if redirects needed
4. Update MikroTik hotspot URL to https://...

Beyond scope of this doc - consult XAMPP docs
```

---

## 🆘 Emergency Procedures

### System Completely Down

```
1. Check XAMPP Apache is running (tray icon)
2. Check XAMPP MySQL is running (tray icon)
3. If not: Start them
4. Restart web browser
5. Try system-status.php again
```

### Database Corrupted / Can't Delete

```
1. PHPMyAdmin → aralinks → Operations tab
2. Click "Check tables"
3. Click "Repair tables"
4. Database should be fixed
```

### Need to Clear All Data (Start Fresh)

```
⚠️ WARNING: This deletes all student records!

1. PHPMyAdmin → aralinks → users table
   → Right-click → Empty (truncate)
2. PHPMyAdmin → aralinks → vouchers table
   → Right-click → Empty (truncate)
3. Delete /logs/access.log file
4. Recreate by visiting setup_tables.php again
5. All student records are now gone, start over
```

### MikroTik API Stopped Responding

```
1. Try to ping MikroTik IP: ping [IP_ADDRESS]
   - If no response: Router is down, power cycle
2. If ping works: Check API service is running
   - WinBox → System → Services
   - Look for "api" service → Should be running
   - If not: Start it
3. Check aralinks user still exists and enabled
   - WinBox → System → Users → aralinks → Check API box
```

---

## 📈 Performance Tips

**If system is slow:**

```
□ Clear old logs:
  Rename /logs/access.log to access.log.backup
  Create fresh access.log (or it auto-creates)

□ Archive database:
  PHPMyAdmin → aralinks → users table
  Delete old entries: WHERE last_access < DATE_SUB(NOW(), INTERVAL 30 DAY)

□ Restart services:
  XAMPP Control Panel → Stop MySQL and Apache
  Wait 10 seconds
  Start them again

□ Check database size:
  PHPMyAdmin → aralinks → Check each table
  If rows > 100k: Consider archiving
```

---

## 📅 Maintenance Schedule

### Daily (2 min)

- Check system-status.php all GREEN
- Scan /logs/access.log for errors

### Weekly (15 min)

- Run user statistics query
- Backup database
- Archive logs if large
- Check MikroTik connectivity

### Monthly (30 min)

- Database maintenance
- Question bank review
- Voucher inventory check
- Performance analysis
- Security audit

### Quarterly (1 hour)

- Review all logs for patterns
- Update documentation
- Backup all files
- Plan improvements

---

## 💾 Backup Strategy

**Backup these files:**

```
/config.php              ← Settings
/quizzes/quiz.php        ← Questions
/logs/access.log         ← History
Database: aralinks       ← All user data
```

**How to backup:**

```
1. Database: PHPMyAdmin → aralinks → Export
2. Files: Copy entire aralinks folder
3. Frequency: Weekly minimum
4. Location: External drive or cloud storage
```

---

## 📞 Support Resources

- **MikroTik Help**: www.mikrotik.com/docs
- **PHP Documentation**: www.php.net
- **MySQL Help**: www.mysql.com/doc
- **XAMPP Support**: www.apachefriends.org

---

## 🎉 You're All Set!

Your ARALINKS system is:

- ✅ Secure (encrypted, validated, logged)
- ✅ Scalable (handles many users)
- ✅ Monitored (detailed logs)
- ✅ Documented (you're reading it!)

**Questions?** Check the relevant guide file. **Issues?** Use QUICK_TROUBLESHOOTING.md.

**Ready to launch!** 🚀

---

**Version**: 1.0  
**Last Updated**: 2024  
**For Updates**: Check project repository
