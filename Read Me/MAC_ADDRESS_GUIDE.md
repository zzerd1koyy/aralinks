# 🔐 MAC Address-Based Device Identification

## Overview

The ARALINKS system has been upgraded to use **MAC addresses** instead of IP addresses as the primary device identifier. This provides better security and prevents redundant logins when a device's IP address changes.

---

## 🎯 Why MAC Address?

| Aspect          | IP Address                   | MAC Address           |
| --------------- | ---------------------------- | --------------------- |
| **Uniqueness**  | Shared on same network       | Unique per device     |
| **Stability**   | Changes frequently           | Permanent per device  |
| **Security**    | Easy to spoof                | Harder to spoof       |
| **Use Case**    | Network routing              | Device identification |
| **Daily Limit** | Can be bypassed by IP change | Cannot be bypassed    |

---

## 📊 How It Works

### 1. **Device Detection**

When a student connects to ARALinks WiFi:

- MikroTik hotspot captures device's **MAC address**
- MAC is passed to quiz/voucher page via URL parameter
- System stores MAC in session and database

### 2. **Daily Limit Check**

Before granting access:

```
DB Query: SELECT * FROM users WHERE device_mac = ? AND DATE(last_access) = TODAY
If found: "Already accessed today"
If not found: Grant access
```

### 3. **Secure Logging**

All access recorded with:

- ✅ Device MAC address
- ✅ Device IP address
- ✅ Access type (quiz/voucher)
- ✅ Timestamp

---

## 🔍 What is a MAC Address?

**MAC** = Media Access Control

**Format**: `AA:BB:CC:DD:EE:FF` or `AA-BB-CC-DD-EE-FF`

**What it represents**:

- First half (AA:BB:CC) = Manufacturer ID
- Second half (DD:EE:FF) = Device serial number

**Examples**:

- `48:5A:B6:2E:9D:7C` (Real device)
- `00:1A:2B:3C:4D:5E` (Test format)

---

## 🚀 System Flow

### Before (IP-Based) ❌

```
Day 1: Student A (IP: 192.168.1.100) → Quiz Pass → 1 hour access
Later: Same student changes network (IP: 192.168.1.200) → Can quiz again!
```

### After (MAC-Based) ✅

```
Day 1: Student A (MAC: AA:BB:CC:DD:EE:FF) → Quiz Pass → 1 hour access
Later: Same student connects from different location → BLOCKED (same MAC)
Next Day: Same student connects → CAN quiz again (daily reset)
```

---

## 📱 For Students

### How to Access WiFi

1. **Connect to ARALinks WiFi** from your device
2. **Open browser** - Auto-redirects to portal
3. **Your MAC is automatically captured**
4. **Choose access method**:
   - 📚 **Quiz**: 5 questions → 4 correct → 1 hour
   - 🎫 **Voucher**: Enter code → Custom duration

### Important Notes

- ✅ You can **only access ONCE per day** (by device MAC)
- ✅ If you change networks/IPs, you're still limited (same MAC)
- ✅ One device per person (recommended)
- ✅ Check `device-info.php` to see your MAC

### Cannot Access?

**Reason 1: "Already Used Today"**

- Your device already connected today
- Come back tomorrow for another free quiz
- OR use a voucher for immediate access

**Reason 2: "Device Not Recognized"**

- MAC address not captured
- Make sure you connect through hotspot portal
- Not connecting directly to WiFi password

---

## 🛠️ For Administrators

### Database Changes

**New columns in `vouchers` table**:

```
used_by_mac  - MAC address that redeemed voucher
used_by_ip   - IP address for logging (backup)
```

### Tracking Daily Usage

**Find all devices that accessed today**:

```sql
SELECT device_mac, COUNT(*) as attempts, MAX(last_access)
FROM users
WHERE DATE(last_access) = CURDATE()
GROUP BY device_mac;
```

**Find student who tried multiple times**:

```sql
SELECT device_mac, device_ip, COUNT(*) as attempts
FROM users
WHERE date(last_access) = CURDATE()
GROUP BY device_mac
HAVING attempts > 1;
```

**View voucher usage by MAC**:

```sql
SELECT code, used_by_mac, used_by_ip, used_at
FROM vouchers
WHERE used = 1 AND DATE(used_at) = CURDATE()
ORDER BY used_at DESC;
```

---

## 🔒 Security Benefits

### 1. **No IP Spoofing Workaround**

- Student can't just change IP to bypass daily limit
- MAC is unique to device

### 2. **Better Device Tracking**

- Know exactly which device accessed
- MAC never changes (unless user spoofs - difficult)

### 3. **Easier Device Audit**

- "Which devices accessed 10 times today?" (suspicious)
- "What's the MAC of device X?" (for blacklisting)

### 4. **Prevents Shared Device Issues**

- Even if shared device, each person gets 1 access per day
- Can't rotate users to bypass limit

---

## ⚙️ Configuration

Edit `config.php` (already set):

```php
// Daily limit is now per MAC (not IP)
define('MAX_FREE_ACCESS_PER_DAY', 1);

// MAC must be valid to proceed
// validateMACAddress() checks format in functions.php
```

---

## 🧪 Testing MAC Capture

### Test Page: `device-info.php`

1. Visit: `http://localhost/aralinks/device-info.php`
2. See your captured MAC address
3. Verify format is correct (XX:XX:XX:XX:XX:XX)

### Test MAC Validation

Valid formats (system accepts):

- `AA:BB:CC:DD:EE:FF` ✅
- `aa-bb-cc-dd-ee-ff` ✅
- `AABBCCDDEEFF` ❌ (missing separators)

---

## 📋 MAC Capture Sources

### MikroTik Hotspot (Primary)

When student connects to ARALinks hotspot:

```
URL: http://quiz.php?ip=192.168.1.100&mac=AA:BB:CC:DD:EE:FF
```

✅ MAC passed automatically

### Direct Access (Fallback)

If accessing without hotspot redirect:

```
Cannot capture MAC automatically
⚠️ Shows warning on device-info.php
⚠️ May not pass daily limit checks
```

---

## 🔄 Database Schema Changes

### Users Table (Already Works)

```sql
device_ip       VARCHAR(45)    -- IP address (logging)
device_mac      VARCHAR(17)    -- MAC address (PRIMARY IDENTIFIER)
access_type     ENUM           -- 'quiz' or 'voucher'
score           INT            -- Quiz score
total_questions INT            -- Total questions
last_access     TIMESTAMP      -- When accessed
```

**Daily limit check uses**: `device_mac` + `DATE(last_access)`

### Vouchers Table (Updated)

```sql
used_by_mac     VARCHAR(17)    -- MAC that redeemed (NEW)
used_by_ip      VARCHAR(45)    -- IP address (for logging)
used_at         TIMESTAMP      -- When redeemed
```

---

## ❓ FAQ

**Q: What if student doesn't have MAC?**
A: System shows warning, quiz may not work correctly. They need to connect through hotspot portal.

**Q: Can students spoof MAC?**
A: Very difficult. Requires technical knowledge. MAC spoofing is detectable in logs.

**Q: What if device shares WiFi password?**
A: Each device has unique MAC. Only one per day. If friends share one device, limit applies to device, not person.

**Q: IP-based still tracked?**
A: Yes! Both MAC and IP logged for:

- Backup identification
- Logging/auditing
- Pattern detection

**Q: How to reset daily limit?**
A: Automatic at midnight (DATE compare). No manual reset needed.

---

## 🚨 Troubleshooting

### MAC Not Captured

**Symptoms**: Device info shows "❌ NOT CAPTURED"

**Solutions**:

1. Restart WiFi connection
2. Clear browser cache
3. Try incognito/private mode
4. Check if hotspot is properly configured
5. Verify MikroTik is sending MAC parameter

### MAC Validation Fails

**Symptoms**: "Device Not Recognized" error

**Possible causes**:

- MAC format invalid (missing colons)
- MAC format with dashes instead of colons
- Empty MAC parameter
- MAC contains invalid characters

**Fix**: Ensure MikroTik sends proper format `XX:XX:XX:XX:XX:XX`

---

## 📊 Monitoring Daily Access

### Quick Stats Query

```sql
SELECT
  DATE(last_access) as date,
  COUNT(DISTINCT device_mac) as unique_devices,
  COUNT(*) as total_access,
  SUM(CASE WHEN access_type='quiz' THEN 1 ELSE 0 END) as quiz_access,
  SUM(CASE WHEN access_type='voucher' THEN 1 ELSE 0 END) as voucher_access
FROM users
GROUP BY DATE(last_access)
ORDER BY date DESC
LIMIT 7;
```

This shows daily trends by device MAC.

---

## ✅ Setup Checklist

- [ ] Database updated with new schema
- [ ] `validateMACAddress()` added to functions.php
- [ ] `process.php` checks by MAC not IP
- [ ] `validate.php` validates MAC format
- [ ] `quiz.php` validates MAC format
- [ ] MikroTik properly sends MAC parameter
- [ ] Test with device-info.php
- [ ] Test daily limit (quiz twice in one day = 2nd fails)
- [ ] Verify logs show MAC addresses
- [ ] Admin trained on MAC-based queries

---

**For more details, see DOCUMENTATION.md**

**Questions? Check the logs at `/logs/access.log` for detailed events.**
