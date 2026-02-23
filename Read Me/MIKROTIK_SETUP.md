# 🔧 MikroTik Hex GR3 Hotspot Setup Guide

## ARALINKS Integration with MikroTik Hotspot

Complete setup instructions for connecting MikroTik Hex GR3 hotspot to ARALINKS WiFi system.

---

## 📋 Prerequisites

- **MikroTik Hex GR3** device with RouterOS 6.48+
- **ARALINKS Portal** installed and running on server
- **Network connectivity** between Hex GR3 and portal server
- **Admin access** to MikroTik

---

## 🚀 Step 1: MikroTik Basic Network Setup

### 1.1 Access MikroTik WinBox

1. Download **WinBox**: https://mikrotik.com/download
2. Run WinBox.exe
3. Enter Hex GR3 IP: `192.168.88.1` (default)
4. Username: `admin` (no password by default)
5. Click **Connect**

### 1.2 Configure Network Interface

1. Go to: **IP → Addresses**
2. Verify ether1-gateway has IP: `192.168.1.1/24`
   - If not, add: Click **+** → Set Address & Interface
3. Go to: **IP → Firewall → NAT**
4. Add NAT rule for internet (masquerade)
   - Chain: `srcnat`
   - Out. Interface: `ether1-gateway`
   - Action: `masquerade`

---

## 🔐 Step 2: Configure Hotspot Profile

### 2.1 Create Hotspot Profile

1. Go to: **IP → Hotspot**
2. Click **Hotspot Profile** tab
3. Click **+** to create new profile
4. Name: `ARALINKS`
5. Go to **Login Tab**:

**CRITICAL: Configure Login Page URL**

```
Name: ARALINKS
Login Page:
http://your-server-ip/aralinks/quizzes/quiz.php?ip=$(client-ip)&mac=$(client-mac)
```

Replace `your-server-ip` with:

- Your XAMPP server IP (e.g., `192.168.1.100`)
- OR domain name (e.g., `aralinks.school.local`)

**Example**:

```
http://192.168.1.100/aralinks/quizzes/quiz.php?ip=$(client-ip)&mac=$(client-mac)
```

6. **HTML/SMS Redirection**: Leave default
7. **SSL Certificate**: Optional (set later for HTTPS)
8. Click **OK**

**IMPORTANT VARIABLES**:

- `$(client-ip)` → Student's IP address
- `$(client-mac)` → Student's MAC address ⭐
- `$(redirect-orig)` → Original URL requested

---

## 📡 Step 3: Configure Hotspot Interface

### 3.1 Create Hotspot on LAN Interface

1. Go to: **IP → Hotspot**
2. Click **Hotspots** tab
3. Click **+** to create new hotspot
4. **Basic Settings**:
   - Name: `ARALINKS`
   - Interface: `ether2` (or your LAN interface with WiFi)
   - Address Pool: `192.168.2.0/24`
   - Profile: `ARALINKS`

5. Click **Next** → Finish

---

## 📶 Step 4: Configure WiFi Interface

### 4.1 Set Up WiFi SSID

1. Go to: **Wireless**
2. Click on interface (likely `wlan1`)
3. Click **Enabled** checkbox
4. Set **Mode**: `ap bridge`
5. Set **SSID**: `ARALinks-WiFi` (or your choice)
6. Set **Band**: `2ghz-b/g/n` (or `5ghz` if available)
7. Set **Frequency**: `auto`
8. Click **Security Profiles** tab
9. Set **WPA2 Password**: `Your-WiFi-Password`
10. Click **Apply → OK**

---

## 🔑 Step 5: IP Pool & DHCP Configuration

### 5.1 Create IP Pool for Hotspot

1. Go to: **IP → Pool**
2. Click **+**
3. Name: `hotspot-pool`
4. Ranges: `192.168.2.2-192.168.2.254`
5. Click **OK**

### 5.2 Create DHCP Server

1. Go to: **IP → DHCP Server**
2. Click **→** (tab for server)
3. Click **+**
4. Name: `ARALINKS-DHCP`
5. Interface: `ether2` (same as hotspot interface)
6. Address Pool: `hotspot-pool`
7. Click **OK**
8. Go to **Server** tab
9. Enable the server by clicking it (will show in list)
10. Right-click → **Enable**

### 5.3 Add DHCP Network

1. Still in **DHCP Server**, go to **Networks** tab
2. Click **+**
3. Address: `192.168.2.0/24`
4. Gateway: `192.168.2.1`
5. DNS: Your DNS server (e.g., `8.8.8.8` or server IP)
6. Click **OK**

---

## 🎯 Step 6: Configure IP Binding (Access Control)

### 6.1 Create IP Binding List

**This is what ARALINKS uses to grant access!**

1. Go to: **IP → Hotspot → IP Bindings**
2. This is where ARALINKS adds devices after quiz/voucher pass
3. MikroTik API will add entries here automatically via `validate.php` and `process.php`

---

## 🔌 Step 7: MikroTik API Configuration

### 7.1 Create API User for ARALINKS

**This allows PHP to communicate with MikroTik**

1. Go to: **System → Users**
2. Click **+** to create new user
3. **Name**: `aralinks`
4. **Group**: `full`
5. **Password**: `YourSecurePassword123!`
6. Click **OK**

### 7.2 Enable API Service

1. Go to: **System → Services**
2. Find **api** (should be `[X]` checked)
3. If not, click checkbox to enable
4. Default port: `8728`

---

## 📝 Step 8: Update ARALINKS Configuration

### 8.1 Edit config.php

Edit: `c:\xampp\htdocs\aralinks\config.php`

```php
// MikroTik Router Configuration
define('MIKROTIK_IP', '192.168.88.1');        // Your Hex GR3 IP
define('MIKROTIK_USER', 'aralinks');           // User created above
define('MIKROTIK_PASS', 'YourSecurePassword123!');  // Password set above
define('MIKROTIK_PORT', 8728);                 // Default API port
define('MIKROTIK_TIMEOUT', 3);
```

### 8.2 Update Login Page URL

In MikroTik, go back to:
**IP → Hotspot → Hotspot Profiles → ARALINKS → Login Page**

Verify it matches:

```
http://192.168.1.100/aralinks/quizzes/quiz.php?ip=$(client-ip)&mac=$(client-mac)
```

---

## 🧪 Step 9: Testing the Setup

### Test 1: WiFi Connection

1. **Connect to WiFi**: `ARALinks-WiFi`
2. **Open Browser** on phone/laptop
3. Should **auto-redirect** to login page
4. If not, visit: `http://192.168.2.1/` manually

### Test 2: MAC Address Capture

1. Visit: `http://192.168.1.100/aralinks/device-info.php`
2. Check if **MAC address is showing**
   - ✅ Green = MAC captured
   - ⚠️ Yellow = MAC not captured

### Test 3: Quiz Access

1. Take **Internet Advocacy Quiz**
2. Answer **4 out of 5 questions correctly**
3. Should see: **"Access Granted"** page
4. Try to reload page → Should **bypass hotspot automatically**

### Test 4: Daily Limit

1. **Pass quiz** (get access)
2. **Disconnect and reconnect** WiFi
3. **Try quiz again** → Should show: **"Already used today"**
4. ✅ If it shows that → MAC-based limit working!

### Test 5: Voucher Access

1. Go to: `http://192.168.1.100/aralinks/voucher.php`
2. Enter test voucher: `TEST001`
3. Should show: **"Access Granted"**
4. Check if device added to MikroTik IP Binding list

---

## 🔍 Verify MikroTik Configuration

### Check Hotspot Status

In WinBox:

1. Go to: **IP → Hotspot**
2. Click **Hotspots** tab
3. Should show **ARALINKS** with:
   - Status: **[X]** enabled
   - Clients: (number of connected devices)

### Check Active Clients

1. Go to: **IP → Hotspot → Active** tab
2. Shows all clients connected
3. Should see:
   - MAC addresses
   - IP addresses
   - Status (authenticated if they passed quiz)

### Check IP Bindings

1. Go to: **IP → Hotspot → IP Bindings**
2. Should show devices added by ARALINKS
3. Each binding should have:
   - MAC address
   - Type: `bypassed`
   - Comment: `quiz-pass` or `voucher-redeemed`

---

## 🚨 Troubleshooting

### Problem 1: WiFi Shows But No Auto-Redirect

**Symptoms**: Connect to WiFi but no redirect page

**Solutions**:

1. Check DHCP is working: Open Terminal → `ipconfig /renew`
2. Verify hotspot interface enabled in MikroTik
3. Check firewall rules not blocking port 80
4. Try opening `http://192.168.2.1/` manually

**Fix**:

```
IP → Firewall → Filter Rules
Add: Allow HTTP (port 80)
Chain: Forward
Dst Port: 80
Action: Accept
```

---

### Problem 2: MAC Not Captured

**Symptoms**: device-info.php shows "❌ NOT CAPTURED"

**Solutions**:

1. Verify login page URL has `&mac=$(client-mac)`
2. Check MikroTik supports MAC variable (should, all versions)
3. Verify hotspot profile is assigned to hotspot

**Debug**:

- Add `&test=1` to URL to see raw parameters
- Check MikroTik logs: **System → Logs**

---

### Problem 3: Can't Connect to MikroTik from PHP

**Symptoms**: "MIKROTIK_ERROR" in logs

**Solutions**:

1. Verify API service enabled: **System → Services → api**
2. Check IP/port in config: `192.168.88.1:8728`
3. Verify API user created with correct password
4. Check firewall allows port 8728:

```
IP → Firewall → Filter Rules
Add: Allow API (port 8728)
Chain: Input
Src Address: 192.168.1.100 (your server)
Dst Port: 8728
Action: Accept
```

---

### Problem 4: Daily Limit Not Working

**Symptoms**: Student can quiz multiple times same day

**Solutions**:

1. Verify MAC is being captured (check device-info.php)
2. Check database is storing MAC (use phpMyAdmin)
3. Verify quiz/process.php is checking by MAC not IP

**Debug Query**:

```sql
SELECT device_mac, COUNT(*) as attempts, MAX(last_access)
FROM users
WHERE DATE(last_access) = CURDATE()
GROUP BY device_mac;
```

---

## 📊 Monitoring & Maintenance

### Daily Checks

1. **Active Clients**: IP → Hotspot → Active
   - See how many students online
   - Check for suspicious activity

2. **Logs**: System → Logs
   - Check for errors
   - Monitor connections

3. **Database**: phpMyAdmin
   - Run stats query to see access patterns
   - Check for duplicate attempts

### Monthly Tasks

1. Clean up database (archive old logs)
2. Review access patterns
3. Create new voucher batches
4. Update security rules if needed

---

## 🔐 Security Hardening

### 1. Disable Default Admin User

1. **System → Users**
2. Right-click **admin** → **Disable**
3. Use only **aralinks** user for API

### 2. Restrict API Access

**IP → Firewall → Filter Rules**

Add rule:

```
Chain: Input
Src Address: 192.168.1.100 (only your server)
Dst Port: 8728
Action: Accept
```

Then add block:

```
Chain: Input
Dst Port: 8728
Action: Drop
```

### 3. Use HTTPS for Login Page

1. Get SSL certificate (self-signed OK for testing)
2. Update login URL to: `https://...`
3. Enable in MikroTik: **IP → Hotspot → Account → Disable HTTP**

---

## 📋 Quick Reference

### Important URLs

| Purpose     | URL                                              |
| ----------- | ------------------------------------------------ |
| Home        | `http://192.168.1.100/aralinks/`                 |
| Device Info | `http://192.168.1.100/aralinks/device-info.php`  |
| Setup DB    | `http://192.168.1.100/aralinks/setup_tables.php` |
| phpMyAdmin  | `http://192.168.1.100/phpmyadmin`                |

### MikroTik Access

| What          | Address                              |
| ------------- | ------------------------------------ |
| WinBox        | IP: `192.168.88.1` User: `admin`     |
| Web Interface | `http://192.168.88.1`                |
| SSH           | `192.168.88.1:22` (enable if needed) |

### Configuration Values

| Setting              | Value                       |
| -------------------- | --------------------------- |
| Hotspot Interface IP | `192.168.2.1/24`            |
| DHCP Range           | `192.168.2.2-192.168.2.254` |
| WiFi SSID            | `ARALinks-WiFi`             |
| API Port             | `8728`                      |
| API User             | `aralinks`                  |

---

## ✅ Final Checklist

- [ ] Hex GR3 configured with IP `192.168.88.1`
- [ ] WiFi SSID set to `ARALinks-WiFi`
- [ ] Hotspot profile created with login page URL
- [ ] Login page URL includes `&mac=$(client-mac)` variable
- [ ] API user `aralinks` created
- [ ] API service enabled on port 8728
- [ ] XAMPP server running on `192.168.1.100`
- [ ] ARALINKS config.php updated with MikroTik details
- [ ] Database tables created with setup_tables.php
- [ ] WiFi connection test successful
- [ ] Auto-redirect to login working
- [ ] MAC capture verified (green on device-info.php)
- [ ] Quiz test passed (4/5 correct)
- [ ] Access granted and device able to browse
- [ ] Daily limit tested (second attempt blocked)
- [ ] Logs show MAC addresses

---

## 🎓 How Students Experience It

1. **Connect to WiFi**: `ARALinks-WiFi`
2. **Auto-redirects** to ARALINKS login page
3. **Choose**: Quiz (free) or Voucher (paid)
4. **Quiz path**: Answer 5 questions → 4 correct → 1 hour access
5. **Automatic**: Device added to MikroTik bypass list
6. **Browse**: Full internet access for 1 hour
7. **Tomorrow**: Can quiz again (daily limit reset)

---

## 📞 Support

### Common Issues

| Issue              | Solution                             |
| ------------------ | ------------------------------------ |
| No auto-redirect   | Check hotspot interface, DHCP        |
| MAC not captured   | Verify login URL has `$(client-mac)` |
| Access not granted | Check MikroTik IP binding, API       |
| Can quiz twice     | Verify MAC-based daily limit         |

### Logs to Check

1. **ARALINKS**: `/xampp/htdocs/aralinks/logs/access.log`
2. **MikroTik**: **System → Logs** in WinBox
3. **PHP Errors**: XAMPP error log

---

**Setup Complete! Your ARALINKS hotspot is ready. 🎉**

Need help? Check logs first! Most issues show clear error messages there.
