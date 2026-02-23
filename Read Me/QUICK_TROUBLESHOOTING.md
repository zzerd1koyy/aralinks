# ⚡ ARALINKS Quick Troubleshooting Checklist

## 🚀 Before You Start

**Verify System is Accessible**

```
□ Can you access http://localhost/aralinks/system-status.php?
  - YES → Continue to section for specific issue
  - NO → Check XAMPP Apache is running, then check network
```

---

## 🔴 Critical Issues (Do These First)

### Issue 1️⃣: "Connection Refused" / "No Internet Access"

**Step 1: Verify MikroTik API Connection**

```
□ Visit: http://localhost/aralinks/system-status.php
□ Check "🔌 MikroTik Connection" section
□ If RED: Stop. Fix MikroTik connection first
□ If GREEN: Continue to Step 2
```

**Step 2: Check Database**

```
□ Open PHPMyAdmin: http://localhost/phpmyadmin
□ Click database "aralinks" left sidebar
□ Look for tables: users, vouchers
□ If missing: Visit http://localhost/aralinks/setup_tables.php → Click Setup
□ If present: Continue to Step 3
```

**Step 3: Test Quiz Manually**

```
□ Open http://localhost/aralinks/quizzes/quiz.php
□ Answer 4 out of 5 questions correctly
□ Should see "Access Granted" page
□ If FAILS: Check error in /logs/access.log
□ If SUCCESS: Continue to Step 4
```

**Step 4: Check MikroTik Authorization**

```
□ In WinBox: IP → Hotspot → Bindings
□ Look for your test device entry
□ Should see IP binding after successful quiz
□ If MISSING: Check MikroTik → API user activation if off
□ If PRESENT: Verify hotspot service is running
```

---

## 🟡 Common Issues

### Issue 2️⃣: "Invalid MAC" Error / MAC Not Captured

**Checklist**:

```
□ Step A: Verify MikroTik hotspot URL
  - In WinBox: IP → Hotspot → Host Profiles
  - Check "Login" tab → Redirect on Login URL
  - MUST contain: $(client-mac)
  - SHOULD LOOK LIKE: http://192.168.88.1/aralinks/quizzes/quiz.php?ip=$(client-ip)&mac=$(client-mac)

□ Step B: If URL is WRONG, FIX IT and save

□ Step C: Reload hotspot (WinBox → IP → Hotspot → select profile → right-click Disable then Enable)

□ Step D: Test again - connect WiFi on phone, check device-info.php
  - If GREEN (MAC shown): ✅ Fixed! Try quiz again
  - If YELLOW (MAC missing): Go to Step E

□ Step E: Check if WiFi captive portal redirects are working
  - Disconnect WiFi
  - Clear browser cache
  - Reconnect to WiFi
  - Should auto-redirect to quiz page within 10 seconds
  - If NO redirect: Check MikroTik hotspot service is running
  - If YES: MAC should now appear
```

**Quick Fix Summary**:

```
MikroTik WinBox → IP → Hotspot → Host Profiles
  → Select your profile → Edit → Login tab
  → Verify Redirect contains: $(client-mac)
  → Apply and Reload hotspot
```

---

### Issue 3️⃣: "API Authentication Failed" Error

**Checklist**:

```
□ Step A: Verify API user exists in MikroTik
  - WinBox → System → Users
  - Look for user named "aralinks"
  - If MISSING: Create it (see MIKROTIK_SETUP.md Step 7)
  - If PRESENT: Continue to Step B

□ Step B: Verify API is ENABLED for this user
  - WinBox → System → Users → select "aralinks"
  - Check boxes under "API" column
  - If unchecked: CHECK the box ✓
  - If checked: Continue to Step C

□ Step C: Verify password in ARALINKS config.php
  - Edit: c:\xampp\htdocs\aralinks\config.php
  - Line with: define('MIKROTIK_PASS', '...');
  - MUST match password set in MikroTik
  - If different: Update it to match
  - Save file

□ Step D: Test connection again
  - http://localhost/aralinks/system-status.php
  - Should show GREEN for "API Authentication"
```

**Quick Fix Summary**:

```
1. WinBox → System → Users → aralinks → Check API checkbox
2. Verify password in config.php matches MikroTik password
3. Reload system-status.php to verify
```

---

### Issue 4️⃣: "MAC Appears Twice in One Day" (Should be Blocked)

⚠️ **This should NOT happen. Indicates a bug or time sync issue.**

**Checklist**:

```
□ Step A: Check server/MikroTik time sync
  - WinBox → System → Clock
  - Verify time is correct
  - If wrong: → Sync from Network (requires internet on MikroTik)

□ Step B: Check database time function
  - PHPMyAdmin → SQL tab
  - Run: SELECT NOW(), CURDATE();
  - Time should match your actual time
  - If off: Check XAMPP server time settings

□ Step C: Review access logs
  - Check: /logs/access.log
  - Look for lines with same MAC on same day
  - See exactly what happened
  - Report to support with timestamp

□ Step D: Manually clear if needed (one-time fix)
  - PHPMyAdmin → Database "aralinks" → users table
  - Find the duplicate entry
  - Right-click → Delete
  - User can now access again today (not recommended)
```

---

### Issue 5️⃣: Voucher Code "Not Valid" But Should Be

**Checklist**:

```
□ Step A: Verify voucher code format
  - Codes MUST be 5-20 alphanumeric characters (A-Z, 0-9)
  - Cannot contain: spaces, special characters, lowercase
  - If code has lowercase or special chars: ❌ INVALID

□ Step B: Check if voucher was already used
  - PHPMyAdmin → Database aralinks → vouchers table
  - Search for code
  - Column "used" shows 0 (not used) or 1 (already redeemed)
  - If "used" = 1: ❌ Already used, create a new one
  - If "used" = 0: Continue to Step C

□ Step C: Check for typos
  - Code entered: "PROMO01"
  - Code in database: "PROMP01" (typo "O" vs "P")?
  - Test with exactly correct code

□ Step D: Clear rate limiting (if rate-limited)
  - PHPMyAdmin → tools → Browser session
  - Clear session data
  - Or wait 5 minutes for rate limit to reset automatically

□ Step E: Verify database connection
  - system-status.php → 🗄️ Database section
  - Should show GREEN ✅
```

**Quick Fix**:

```
1. Check code format: 5-20 chars, A-Z 0-9 only, UPPERCASE
2. Verify code exists: PHPMyAdmin → aralinks → vouchers → Search
3. If not found: Create new with: INSERT INTO vouchers VALUES (...)
4. If rate limited: Wait 5 min or clear session
```

---

### Issue 6️⃣: Student Passes Quiz But Cannot Browse Internet

**Checklist**:

```
□ Step A: Verify quiz completion was recorded
  - PHPMyAdmin → aralinks → users table
  - Look for student's MAC address
  - If FOUND: access_type should be "quiz"
  - If NOT FOUND: Student didn't actually pass, start over
  - If FOUND but type="voucher": They used voucher instead, likely fine

□ Step B: Verify MikroTik received authorization
  - WinBox → IP → Hotspot → Bindings
  - Look for student's IP/MAC
  - Should show unlimited access or time remaining
  - If MISSING: Authorization didn't reach MikroTik
  - If PRESENT: Issue is with MikroTik routing

□ Step C: Check MikroTik hotspot service
  - WinBox → IP → Hotspot → Service
  - Status should show "running"
  - If "stopped": Right-click → Enable

□ Step D: Check MikroTik interface routing
  - WinBox → IP → Firewall → NAT
  - Look for "masquerade" rules
  - If none exist: Create it (see MIKROTIK_SETUP.md)

□ Step E: Manual test from another device
  - If possible, test WiFi from different phone/laptop
  - Does it work? Then first device may have cached DNS
  - If not: Issue is with MikroTik, not ARALINKS
```

**Quick Fix**:

```
1. Check system-status.php → 🔌 MikroTik Connection → Should be ✅
2. WinBox → IP → Hotspot → Service → Should show ✓ running
3. WinBox → IP → Hotspot → Bindings → Should show student's device
4. If step 3 missing: MikroTik API not responding, check connection
```

---

## 🟢 Verified Working Tests

After fixing above, run these to confirm:

### Test 1: System Health Check

```
□ Visit: http://localhost/aralinks/system-status.php
□ ALL sections should show ✅ GREEN
□ If any YELLOW or RED: Fix that first
```

### Test 2: Device Detection

```
□ Visit: http://localhost/aralinks/device-info.php
□ Should show:
  - Server IP: [your IP]
  - Device MAC: [MAC address in AA:BB:CC:DD:EE:FF format]
  - Status: 🟢 GREEN (Captured)
□ If YELLOW or missing:
  - MAC not being sent by MikroTik
  - Check redirect URL includes $(client-mac)
```

### Test 3: Quiz Flow

```
□ Visit: http://localhost/aralinks/quizzes/quiz.php
□ Answer 4 out of 5 questions correctly
□ Should see "✅ Access Successfully Granted"
□ Information displayed should include:
  - Access Type: Quiz
  - Duration: 60 minutes
  - Next Available: Tomorrow
```

### Test 4: Internet Access

```
□ On your phone connected to WiFi
□ Try to access www.google.com
□ Should open successfully (means MikroTik allowed it)
□ Should NOT see login page again (means session active)
□ If still see login page:
  - Device cache issue: Restart phone WiFi connection
  - Or MikroTik didn't register authorization
```

### Test 5: Daily Limit

```
□ Successfully pass quiz and get internet access
□ Immediately disconnect WiFi
□ Reconnect WiFi on same device
□ Try to access quiz again
□ Should see: "This device already accessed today"
□ If doesn't show error:
  - MAC not being tracked properly
  - Check device-info.php shows MAC captured
```

---

## 📊 Debug Information to Collect

**If something doesn't work**, collect this info:

```
□ MAC address (from device-info.php):
  _________________________________________________

□ Last 10 lines from /logs/access.log:
  (Copy-paste error lines)
  _________________________________________________

□ Database query result:
  SELECT * FROM users WHERE device_mac='XX:XX:XX:XX:XX:XX' LIMIT 5;
  _________________________________________________

□ MikroTik verification:
  - WinBox IP: _________________
  - API user exists? YES / NO / NOT SURE
  - Is it enabled? YES / NO / NOT SURE

□ Test connectivity result from system-status.php:
  - Configuration: ✅ / 🟡 / ❌
  - MikroTik Connection: ✅ / 🟡 / ❌
  - Database: ✅ / 🟡 / ❌
```

---

## 🔑 Key File Locations

| Issue                   | File to Check                                     |
| ----------------------- | ------------------------------------------------- |
| Errors in quiz          | `/logs/access.log` and `/quizzes/quiz.php`        |
| Errors in voucher       | `/logs/access.log` and `/voucher.php`             |
| Configuration wrong     | `/config.php` (IP, user, password, quiz settings) |
| Default database values | `/setup_tables.php`                               |
| MAC validation issues   | `/functions.php` (look for validateMACAddress)    |
| MikroTik settings       | `/MIKROTIK_SETUP.md`                              |
| Admin procedures        | `/ADMIN_OPERATIONS_GUIDE.md`                      |

---

## 🎯 30-Second Diagnostic

**If system down, do THIS:**

```
Timeline: 30 seconds

[5 sec]  1. Open: http://localhost/aralinks/system-status.php
         2. Note: Which section shows RED?

[5 sec]  3. If "🔌 MikroTik Connection" is RED:
         4. Check MikroTik is powered on and responding
         5. Check config.php has correct IP:PORT

[5 sec]  6. If "🗄️ Database" is RED:
         7. Check XAMPP MySQL is running
         8. Try http://localhost/phpmyadmin

[5 sec]  9. If "📡 Network Info" shows wrong IP:
         10. Check your server IP is correct
         11. Update MikroTik hotspot URL if needed

[5 sec]  12. If ALL GREEN but quiz not working:
         13. Check /logs/access.log for actual error
         14. If no logs: PHP error logging might be off
```

---

## 🆘 When All Else Fails

1. **Restart XAMPP**
   - Close XAMPP Control Panel
   - Wait 10 seconds
   - Start Apache and MySQL again

2. **Restart MikroTik**
   - WinBox button → Reboot (top menu)
   - Wait 2 minutes for startup
   - Test again

3. **Clear Browser Cache**
   - Some issues are cached pages
   - Ctrl+Shift+Delete and clear all
   - Try again

4. **Check File Permissions**
   - Logs directory: `c:\xampp\htdocs\aralinks\logs\` should exist and be writable
   - If not: Create folder and set permissions to 777

5. **Database Integrity Check**
   ```sql
   -- Run in PHPMyAdmin
   CHECK TABLE users;
   CHECK TABLE vouchers;
   REPAIR TABLE users;
   REPAIR TABLE vouchers;
   ```

---

**Last Updated**: 2024  
**For Support**: Check logs first, then collect debug info above and contact administrator
