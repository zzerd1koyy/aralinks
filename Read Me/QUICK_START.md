# ⚡ ARALINKS Quick Start Guide (5 Minutes)

For non-technical administrators who just want to get the system running.

---

## Step 1: Set Up Database (3 minutes)

### Option A: Using phpMyAdmin (Easiest)

1. Open **phpMyAdmin** in your browser
   - Usually: `http://localhost/phpmyadmin`

2. Click **"New"** on the left sidebar

3. Enter database name: `aralinks`
   - Character set: **utf8mb4_unicode_ci**
   - Click **Create**

4. Click on `aralinks` database (left sidebar)

5. Go to **SQL tab** at the top

6. Copy the contents of **database_setup.sql** file

7. Paste it into the SQL box

8. Click **Go** (blue button at bottom)

✅ **Database is now setup!**

---

### Option B: Using Command Line

Open Terminal/Command Prompt:

```bash
mysql -u root -p aralinks < database_setup.sql
```

Enter your password when prompted.

✅ **Database is now setup!**

---

## Step 2: Configure Settings (1 minute)

### Edit config.php

1. Open: `c:\xampp\htdocs\aralinks\config.php`

2. Find line with `MIKROTIK_IP` - Update to your router IP:

   ```php
   define('MIKROTIK_IP', '192.168.40.177');  ← Your router IP
   ```

3. Update MikroTik username and password:

   ```php
   define('MIKROTIK_USER', 'ComonHS');       ← Your username
   define('MIKROTIK_PASS', '@comonhs.mikrotik');  ← Your password
   ```

4. Save the file (Ctrl+S)

✅ **Configuration complete!**

---

## Step 3: Test the System (1 minute)

### Test Quiz

1. Go to: `http://localhost/aralinks/`

2. Click **"PROCEED"** on the welcome modal

3. Click **"Answer Quiz"** button

4. Answer **4 out of 5 questions correctly** to pass
   (For testing, just pick any 4 answers - doesn't matter which)

5. You should see **"Access Granted!"** page

✅ **Quiz works!**

---

### Test Voucher

1. Go back to: `http://localhost/aralinks/`

2. Click **"Voucher Code"** button

3. Enter test code: `TEST001`

4. Click **"Connect"**

5. You should see **"Access Granted!"** page

✅ **Voucher works!**

---

## Step 4: Create Real Vouchers (1 minute)

### Using phpMyAdmin

1. Open **phpMyAdmin** → `aralinks` database

2. Click **vouchers** table

3. Click **Insert** tab

4. Create a voucher:
   - Code: `APRIL2025-TEACHER-001` (any alphanumeric, 5-20 chars)
   - Duration: `60` (minutes)
   - Click **Go**

5. Repeat for more vouchers

✅ **Vouchers created!**

---

### Bulk Create with SQL

1. Go to **SQL tab** in phpMyAdmin

2. Paste:

```sql
INSERT INTO vouchers (code, duration) VALUES
('APRIL-2025-001', 60),
('APRIL-2025-002', 120),
('APRIL-2025-003', 180),
('APRIL-2025-004', 240),
('APRIL-2025-005', 60);
```

3. Click **Go**

✅ **Multiple vouchers created!**

---

## 🔍 Monitoring Access

### Check Today's Statistics

1. Open **phpMyAdmin** → `aralinks` database

2. Click **SQL tab**

3. Paste and run:

```sql
SELECT
  COUNT(*) as total_access,
  SUM(CASE WHEN access_type='quiz' THEN 1 ELSE 0 END) as quiz_access,
  SUM(CASE WHEN access_type='voucher' THEN 1 ELSE 0 END) as voucher_access
FROM users
WHERE DATE(last_access) = CURDATE();
```

This shows you:

- **total_access**: Total WiFi access grants today
- **quiz_access**: How many from quiz
- **voucher_access**: How many from vouchers

---

### Check Quiz Pass Rate

```sql
SELECT
  COUNT(*) as quiz_attempts,
  SUM(CASE WHEN score >= 4 THEN 1 ELSE 0 END) as passed,
  SUM(CASE WHEN score < 4 THEN 1 ELSE 0 END) as failed
FROM users
WHERE access_type = 'quiz'
AND DATE(last_access) = CURDATE();
```

---

### View Voucher Usage

```sql
SELECT
  code,
  duration,
  used_by_ip,
  used_at
FROM vouchers
WHERE used = 1
AND DATE(used_at) = CURDATE()
ORDER BY used_at DESC;
```

---

## 📝 Creating Voucher Batches

### For Teachers

```sql
INSERT INTO vouchers (code, duration) VALUES
('TEACHER-101', 240),
('TEACHER-102', 240),
('TEACHER-103', 240);
```

### For Special Events

```sql
INSERT INTO vouchers (code, duration) VALUES
('SPORTS-DAY-001', 120),
('SPORTS-DAY-002', 120),
('SPORTS-DAY-003', 120);
```

### For Different Durations

```sql
INSERT INTO vouchers (code, duration) VALUES
('SHORT-60-001', 60),
('MEDIUM-120-001', 120),
('LONG-240-001', 240);
```

---

## 🚨 Troubleshooting

### Students Can't Pass Quiz

- **Expected**: Need to answer 4 out of 5 questions correctly
- **If failing at 4/5**: There might be an issue - check logs

### Voucher Code Not Working

1. Check code exists in database
2. Make sure it hasn't been used already
3. Verify code format (alphanumeric only, 5-20 chars)

### No Access After Passing Quiz/Voucher

- MikroTik might not be connected
- Check MikroTik IP address in config.php
- Verify username/password are correct

### Can't Find Logs

- Check: `/xampp/htdocs/aralinks/logs/access.log`
- Or: Right-click → Properties → edit in VS Code

---

## 📞 Quick Tips

1. **To see all unused vouchers:**

   ```sql
   SELECT code, duration FROM vouchers WHERE used = 0;
   ```

2. **To delete a test voucher:**

   ```sql
   DELETE FROM vouchers WHERE code = 'TEST001';
   ```

3. **To see which IP addresses accessed today:**

   ```sql
   SELECT DISTINCT device_ip FROM users WHERE DATE(last_access) = CURDATE();
   ```

4. **To reset all test data:**
   ```sql
   DELETE FROM users;
   DELETE FROM vouchers;
   ```
   Then re-run `database_setup.sql` to reload sample data.

---

## ✅ Success Checklist

- [ ] Database created with tables
- [ ] MikroTik IP address updated in config.php
- [ ] MikroTik username/password updated
- [ ] Quiz tested and working
- [ ] Voucher tested and working
- [ ] Real vouchers created in database
- [ ] Students can see it on `http://localhost/aralinks/`
- [ ] Logs being written to `/logs/access.log`

---

## 🎓 Student Experience

**When a student connects to WiFi:**

1. Browser redirects to: `http://localhost/aralinks/`

2. They see: Welcome modal with info about the system

3. They choose:
   - 📚 **Quiz** → Answer 5 random questions → Need 4 correct → Get 1 hour access
   - 🎫 **Voucher** → Enter code → Get custom duration access

4. If successful → Show success page with access duration

5. Device automatically added to MikroTik bypass list

---

## 📊 Daily Maintenance (2 minutes)

Each morning:

1. Check access statistics (query above)
2. Create new voucher batches if needed
3. Review logs for any errors

That's it! The system handles everything else automatically.

---

## 🔒 Important Security Notes

✅ All passwords are secure (database + MikroTik)  
✅ No personal data is stored  
✅ All access is logged  
✅ Rate limiting prevents brute force  
✅ System validates all inputs

---

## 📞 Need Help?

1. **Can't find something?** → Check README.md
2. **Want detailed info?** → Check DOCUMENTATION.md
3. **Admin tasks?** → Check ADMIN_GUIDE.md
4. **What changed?** → Check IMPROVEMENTS_SUMMARY.md

---

**That's it! Your system is ready to use. 🎉**

Questions? Check the other documentation files or contact your ICT Department.
