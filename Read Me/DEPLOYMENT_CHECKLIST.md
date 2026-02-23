# ✅ ARALINKS Live System Integration Checklist

**Follow this checklist to go from setup → testing → live deployment**

---

## Phase 1: Pre-Deployment Setup (30 minutes)

### ✓ 1.1 Database Initialization

```
□ Visit: http://localhost/aralinks/setup_tables.php
□ Click "Setup Database" button
□ Verify success message: "✅ All tables created successfully"
□ Check: Sample vouchers are created (TEST001, etc.)
□ Confirm: No "❌ Error" messages appear

⏱️ Expected time: 2 minutes
```

### ✓ 1.2 System Verification

```
□ Visit: http://localhost/aralinks/system-status.php
□ Review each section:
  - ⚙️ Configuration: Should show MikroTik IP, port, user
  - 🔌 MikroTik Connection: Should show ✅ PASS
  - 🗄️ Database: Should show ✅ PASS
  - 📡 Network Info: Note your Server IP
  - 📁 File System: Should show ✅ Writable

□ If any RED sections:
  - Fix the issue before continuing
  - Use QUICK_TROUBLESHOOTING.md for help

⏱️ Expected time: 3 minutes
```

### ✓ 1.3 Configuration Verification

```
□ Edit: c:\xampp\htdocs\aralinks\config.php

□ Verify these lines have CORRECT VALUES:

  Line ~20:  define('MIKROTIK_IP', 'YOUR_ROUTER_IP');
  Line ~21:  define('MIKROTIK_USER', 'aralinks');
  Line ~22:  define('MIKROTIK_PASS', 'YOUR_ROUTER_PASSWORD');
  Line ~23:  define('MIKROTIK_PORT', 8728);

  Line ~29:  define('QUIZ_PASS_PERCENTAGE', 0.8);  // 80% = 4/5
  Line ~30:  define('QUIZ_ACCESS_TIME_MINUTES', 60);
  Line ~31:  define('QUIZ_QUESTIONS_COUNT', 5);

□ If any values are wrong: Update them and save
□ Verify config.php file is saved (Ctrl+S)

⏱️ Expected time: 5 minutes
```

### ✓ 1.4 MikroTik Pre-Deployment Check

```
□ Access WinBox: 192.168.88.1 (or your MikroTik IP)
□ Verify username/password for API user "aralinks" in System → Users
□ Verify API checkbox is ✓ ENABLED for this user
□ Verify hotspot profile is created: IP → Hotspot → Host Profiles
□ Verify WiFi interface is configured: IP → Hotspot → Wireless
□ Verify hotspot service is RUNNING: IP → Hotspot → Service → ✓ running

□ Note your internal server IP (not 192.168.88.1):
  Your server IP: ______________________

⏱️ Expected time: 10 minutes
```

---

## Phase 2: Testing on Localhost (45 minutes)

### ✓ 2.1 Test Device MAC Capture

```
□ Visit: http://localhost/aralinks/device-info.php
□ You should see:
  - Server IP: [your server IP]
  - Device MAC: [should show as AA:BB:CC:DD:EE:FF or "Not Provided"]
  - Status indicator

□ Expected result:
  - If ✅ GREEN: MAC capture is working (great for cloud testing)
  - If 🟡 YELLOW: MAC not captured (normal for localhost, will work on WiFi)

⏱️ Expected time: 2 minutes
```

### ✓ 2.2 Test Quiz Flow (Localhost)

```
□ Visit: http://localhost/aralinks/quizzes/quiz.php
□ Progress bar should show: "Question 1 of 5"
□ Read each question and select answer
□ You MUST get exactly 4 or 5 correct to pass (80% rule)
□ After last question, click "Submit Quiz"

IMPORTANT: To ensure PASS, use these answers:
  Question about password: B (strong combination)
  Question about cyberbully: A (report it)
  Question about phishing: C (don't click link)
  Question about copyright: B (give credit)
  Question about privacy: A (don't share personal info)

  → This should give you 5/5 = 100% pass rate

□ After submission, you should see:
  - ✅ "Access Successfully Granted"
  - "Access Type: Quiz"
  - "Duration: 60 minutes"
  - "Next available: Tomorrow"

□ Expected result: ✅ SUCCESS

⏱️ Expected time: 5 minutes
```

### ✓ 2.3 Test Voucher Redemption (Localhost)

```
□ Visit: http://localhost/aralinks/voucher.php
□ Enter a voucher code from the test set:
  - TEST001, TEST002, TEST003 (from database setup)
  - Or one you created manually
□ Click "Redeem Voucher"

□ Expected result:
  - ✅ SUCCESS: "Voucher accepted - XX minutes access"
  - ❌ FAIL: "Voucher invalid or already used"

□ If FAIL:
  - Check code spelling
  - Verify it hasn't been used already (check database)
  - Create new voucher if needed

⏱️ Expected time: 3 minutes
```

### ✓ 2.4 Verify Database Records

```
□ Open PHPMyAdmin: http://localhost/phpmyadmin
□ Click database "aralinks" → table "users"
□ You should see entries from quiz/voucher tests above
□ Verify each entry has:
  - device_mac: Your MAC or IP (if on WiFi returns MAC)
  - access_type: "quiz" or "voucher"
  - last_access: Today's timestamp
  - score: Should show (if quiz)

□ Expected result: ✅ 2+ entries (one from quiz, one from voucher)

⏱️ Expected time: 2 minutes
```

### ✓ 2.5 Test Duplicate Access Prevention (Localhost)

```
□ Visit: http://localhost/aralinks/quizzes/quiz.php
□ Try to take quiz again immediately
□ Expected result:
  - Should see error: "This device already accessed today"
  - Or show quiz but completion shows "already used"

□ This verifies daily limit is working ✅

⏱️ Expected time: 3 minutes
```

### ✓ 2.6 Check Logs

```
□ Open file: c:\xampp\htdocs\aralinks\logs\access.log
□ Should contain entries like:
  [2024-01-15 14:25:13] [INFO] | IP: 127.0.0.1 | MAC: ...
  [2024-01-15 14:25:13] [INFO] | ... | ACTION: QUIZ_STARTED
  [2024-01-15 14:25:45] [INFO] | ... | ACTION: QUIZ_COMPLETED | DETAILS: Score 5/5
  [2024-01-15 14:25:46] [INFO] | ... | ACTION: QUIZ_ACCESS_GRANTED
  [2024-01-15 14:26:10] [INFO] | ... | ACTION: VOUCHER_REDEEMED

□ Expected result: ✅ Log entries appear with correct events
□ No ERROR or failed events

⏱️ Expected time: 2 minutes
```

**Phase 2 Result**: ✅ All localhost tests should PASS before going live

---

## Phase 3: WiFi Network Testing (1 hour)

### ⚠️ Requirements for this phase:

- WiFi device or phone with WiFi
- Server running on XAMPP with IP accessible from WiFi network
- MikroTik Hex GR3 powered on and configured (per MIKROTIK_SETUP.md)
- MikroTik connected to same network as XAMPP server

### ✓ 3.1 Test WiFi Connectivity

```
□ On your WiFi device (phone, laptop, tablet):
□ Look for SSID "ARALinks-WiFi" (or whatever you named it)
□ Connect to WiFi
□ Enter WiFi password you set in MikroTik

□ Expected result:
  - ✅ CONNECTED to WiFi with internet access
  - Phone may show "Captive portal detected"

⏱️ Expected time: 1 minute
```

### ✓ 3.2 Test Auto-Redirect to Quiz

```
□ After connecting to WiFi
□ Open browser (Chrome, Safari, Firefox, etc.)
□ Try to visit any webpage: www.google.com
□ Expected behavior:
  - Instead of opening Google, AUTO-REDIRECT to your quiz page
  - Should see: "Question 1 of 5" with first question

□ If this doesn't happen:
  - Check logs for error: /logs/access.log
  - Verify hotspot profile has redirect URL correctly set
  - See QUICK_TROUBLESHOOTING.md → Issue 2

⏱️ Expected time: 2 minutes
```

### ✓ 3.3 Test MAC Address Capture

```
□ While on WiFi, visit: http://[YOUR_SERVER_IP]/aralinks/device-info.php
□ Expected to see:
  - Server IP: [your server IP]
  - Device MAC: AA:BB:CC:DD:EE:FF (actual MAC of your WiFi device)
  - Status: 🟢 GREEN "Captured"

□ If YELLOW or not visible:
  - MAC not being sent by MikroTik
  - Check hotspot login URL has $(client-mac)
  - See QUICK_TROUBLESHOOTING.md → Issue 2

⏱️ Expected time: 2 minutes
```

### ✓ 3.4 Complete Quiz on WiFi

```
□ Be redirected (or navigate to) quiz page on WiFi
□ Take the 5-question quiz
□ Answer 4 or 5 correctly to PASS
□ Submit quiz

□ Expected result:
  - ✅ "Access Successfully Granted" message
  - Can now browse internet on this device
  - No more automatic redirects to quiz

□ Test internet access:
  - Try opening www.google.com - should work
  - Try opening www.youtube.com - should work
  - If blocked: Check MikroTik hotspot bindings

⏱️ Expected time: 5 minutes
```

### ✓ 3.5 Test Duplicate Attempt Blocking

```
□ While on same WiFi device that just passed quiz:
□ Open new browser tab
□ Go to: http://[YOUR_SERVER_IP]/aralinks/quizzes/quiz.php
□ Try to take quiz again

□ Expected result:
  - ❌ ERROR: "This device already received access today"
  - Or quiz page says "Already used today - next attempt tomorrow"
  - Device MAC prevents duplicate access

⏱️ Expected time: 2 minutes
```

### ✓ 3.6 Test Voucher Route

```
□ On same WiFi device that got quiz access:
□ You should NOT be able to use voucher (already has access)
□ Try anyway: Visit http://[YOUR_SERVER_IP]/aralinks/voucher.php
□ Enter a voucher code

□ Expected result:
  - ❌ ERROR: "This device already has access today"
  - System should prevent double-dipping

□ If on DIFFERENT device:
  - Should accept voucher and grant access

⏱️ Expected time: 2 minutes
```

### ✓ 3.7 Verify Database from WiFi Test

```
□ Back on your admin computer
□ PHPMyAdmin: http://localhost/phpmyadmin
□ Database "aralinks" → table "users"
□ Should see new entry with:
  - device_mac: [WiFi device's actual MAC]
  - device_ip: [WiFi device's IP from router]
  - access_type: "quiz" or "voucher"
  - last_access: Just now (current timestamp)

□ Expected result: ✅ MAC properly recorded in database

⏱️ Expected time: 2 minutes
```

### ✓ 3.8 Test from Second Device (Different MAC)

```
□ Use a DIFFERENT WiFi device (different MAC address)
□ Connect to same WiFi network
□ Should auto-redirect to quiz
□ Should see quiz page (not "already used" message)
□ Complete quiz with 4+ correct answers

□ Expected result:
  - ✅ Different MAC = Different access
  - Both devices can access same day
  - Database shows TWO entries (one per MAC)

⏱️ Expected time: 5 minutes
```

**Phase 3 Result**: ✅ WiFi quiz, MAC capture, and access should all work

---

## Phase 4: Stress Testing (30 minutes)

### ✓ 4.1 Test Rate Limiting

```
□ On one device, visit voucher page
□ Enter WRONG voucher code 5+ times rapidly
□ Expected on attempt 6:
  - ❌ "Too many attempts. Try again in 5 minutes"

□ This prevents brute-force attacks ✅

⏱️ Expected time: 2 minutes
```

### ✓ 4.2 Test Multiple Concurrent Users

```
□ If possible, have 5+ people connect WiFi at same time
□ Each should get redirected to quiz independently
□ Each should get different progress bars
□ Database should record each, by MAC

□ Expected result: ✅ System handles concurrent users

⏱️ Expected time: 10 minutes
```

### ✓ 4.3 Test Database Under Load

```
□ In database, verify no duplicate MACs on same day
□ Run: SELECT device_mac FROM users
       WHERE DATE(last_access)=CURDATE()
       GROUP BY device_mac HAVING COUNT(*)>1;
□ Result should be: (empty - no rows)

□ If you see results: Bug in duplicate prevention, investigate

⏱️ Expected time: 2 minutes
```

### ✓ 4.4 Check for Errors After Testing

```
□ Review: /logs/access.log
□ Look for any ERROR or FAILED entries
□ If none: ✅ System is stable
□ If some: Review what failed and fix

⏱️ Expected time: 5 minutes
```

**Phase 4 Result**: ✅ System is stable and ready

---

## Phase 5: Live Deployment (Ongoing)

### ✓ 5.1 Generate Real Vouchers

For your actual student/teacher usage:

```sql
-- For teachers (120 min free access)
INSERT INTO vouchers (code, duration, used, created_at) VALUES
('TEACHER001', 120, 0, NOW()),
('TEACHER002', 120, 0, NOW()),
('TEACHER003', 120, 0, NOW());

-- For students (promotional - 60 min)
INSERT INTO vouchers (code, duration, used, created_at) VALUES
('PROMO2024', 60, 0, NOW());

-- For admin testing
INSERT INTO vouchers (code, duration, used, created_at) VALUES
('ADMIN001', 60, 0, NOW()),
('ADMIN002', 60, 0, NOW());
```

### ✓ 5.2 Communicate with Users

Make sure students know:

```
□ WiFi SSID: "ARALinks-WiFi" (or whatever you named it)
□ WiFi Password: [your password]
□ When they connect, they'll see a quiz page
□ If they score 4/5: Free internet for 1 hour
□ If they score <4/5: They can try again tomorrow or use voucher code
□ One attempt per device per day
□ Device identified by MAC address (can't fake it with VPN)
```

### ✓ 5.3 Daily Monitoring

Each morning, check:

```
□ System status: http://localhost/aralinks/system-status.php
  All should be ✅ GREEN

□ Recent activity: View /logs/access.log
  Look for any ERRORS or unexpected events

□ Database health: Count today's users
  SELECT COUNT(*) FROM users WHERE DATE(last_access)=CURDATE();

□ MikroTik status: Ping the router, verify it's running

Estimated time: 5 minutes daily
```

### ✓ 5.4 Weekly Maintenance

```
□ Backup database:
  PHPMyAdmin → aralinks → Export tab → Download

□ Archive old logs:
  Rename /logs/access.log to access.log.BACKUP
  Create new empty access.log

□ Review security:
  Any suspicious MAC addresses?
  Any repeated failures?

□ Create new vouchers if running low

Estimated time: 15 minutes weekly
```

### ✓ 5.5 Monthly Tasks

```
□ Performance analysis:
  Run report query from ADMIN_OPERATIONS_GUIDE.md

□ Backup system files

□ Update documentation if you made changes

□ Reset test accounts/vouchers

Estimated time: 30 minutes monthly
```

---

## 📋 Sign-Off Checklist

When all phases complete, mark:

```
✅ Phase 1: Pre-Deployment Setup - COMPLETE
   □ Database initialized
   □ System status shows ✅ GREEN
   □ Configuration verified
   □ MikroTik pre-deployment check passed

✅ Phase 2: Localhost Testing - COMPLETE
   □ Device MAC capture works
   □ Quiz flow works (pass & fail)
   □ Voucher redemption works
   □ Database records appear
   □ Duplicate access prevented
   □ Logs show all events

✅ Phase 3: WiFi Network Testing - COMPLETE
   □ WiFi connection successful
   □ Auto-redirect to quiz works
   □ MAC address captured on WiFi
   □ Quiz completes and grants access
   □ Internet access works after quiz
   □ Duplicate attempt blocked
   □ Database shows WiFi tests
   □ Multiple devices work independently

✅ Phase 4: Stress Testing - COMPLETE
   □ Rate limiting works (no brute force)
   □ Multiple concurrent users handled
   □ No database conflicts
   □ No errors in logs

🚀 SYSTEM READY FOR LIVE DEPLOYMENT
```

---

## 🎉 Congratulations!

Your ARALINKS WiFi authentication system is now:

- ✅ Secure (SQL injection fixed, CSRF protected)
- ✅ Scalable (MAC-based device tracking)
- ✅ User-friendly (auto-redirect, mobile responsive)
- ✅ Monitored (comprehensive logging)
- ✅ Tested (4 phases of validation)

**Next step**: Announce to students they can now connect to `ARALinks-WiFi` and take the quiz!

---

**Document Version**: 1.0  
**Last Updated**: 2024  
**For Support**: See QUICK_TROUBLESHOOTING.md or ADMIN_OPERATIONS_GUIDE.md
