<?php
/**
 * ARALINKS Device Information Display
 * Helps students verify their MAC address is being captured correctly
 */

session_start();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Device Info - ARALINKS</title>
<link rel="stylesheet" href="style.css">
<style>
    .device-info-container {
        max-width: 600px;
        margin: 30px auto;
        padding: 20px;
    }
    
    .device-info-box {
        background: white;
        color: black;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .device-info-box h2 {
        color: #007bff;
        margin-bottom: 20px;
    }
    
    .info-item {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 15px;
        margin: 15px 0;
        border-radius: 6px;
    }
    
    .info-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    
    .info-value {
        font-size: 18px;
        font-weight: bold;
        color: #007bff;
        font-family: 'Courier New', monospace;
        word-break: break-all;
    }
    
    .status-ok {
        background: #d4edda;
        border-left-color: #28a745;
    }
    
    .status-ok .info-value {
        color: #155724;
    }
    
    .status-warning {
        background: #fff3cd;
        border-left-color: #ffc107;
    }
    
    .status-warning .info-value {
        color: #856404;
    }
    
    .note {
        background: #e7f3ff;
        border-left: 4px solid #0066cc;
        padding: 15px;
        margin: 20px 0;
        border-radius: 6px;
        font-size: 14px;
        line-height: 1.6;
        color: #004085;
    }
    
    .button-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    .btn {
        flex: 1;
        min-width: 150px;
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s;
        display: inline-block;
    }
    
    .btn-primary {
        background: #007bff;
        color: white;
    }
    
    .btn-primary:hover {
        background: #0056b3;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    @media (max-width: 480px) {
        .device-info-container {
            padding: 15px;
        }
        
        .device-info-box {
            padding: 20px;
        }
        
        .button-group {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
        }
    }
</style>
</head>
<body>

<div class="device-info-container">
    <div class="device-info-box">
        <h2>📱 Your Device Information</h2>
        
        <?php
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // Try to get MAC from session (if captured during hotspot redirect)
        $mac_from_session = $_SESSION['mac'] ?? '';
        
        // Check if MAC is valid
        $mac_valid = false;
        if (!empty($mac_from_session)) {
            $mac_valid = preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac_from_session);
        }
        ?>
        
        <div class="info-item <?php echo $mac_valid ? 'status-ok' : 'status-warning'; ?>">
            <div class="info-label">📍 IPv4 Address</div>
            <div class="info-value"><?php echo htmlspecialchars($ip); ?></div>
        </div>
        
        <div class="info-item <?php echo $mac_valid ? 'status-ok' : 'status-warning'; ?>">
            <div class="info-label">🔗 MAC Address (Device ID)</div>
            <div class="info-value">
                <?php 
                if ($mac_valid) {
                    echo htmlspecialchars($mac_from_session);
                } else {
                    echo '❌ NOT CAPTURED';
                }
                ?>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-label">🌐 Browser</div>
            <div class="info-value" style="font-size: 14px; font-family: Arial; color: #666;">
                <?php echo htmlspecialchars(substr($user_agent, 0, 60)); ?>
            </div>
        </div>
        
        <?php if (!$mac_valid): ?>
            <div class="note">
                <strong>⚠️ MAC Address Not Detected</strong><br><br>
                Your device's MAC address hasn't been captured yet. This can happen if:
                <ul>
                    <li>You accessed this page directly (not through the hotspot)</li>
                    <li>The hotspot redirect didn't pass MAC information</li>
                    <li>Your device doesn't provide MAC information</li>
                </ul>
                <strong>You can still try the quiz, but for best security, access through the WiFi hotspot portal.</strong>
            </div>
        <?php else: ?>
            <div class="note">
                <strong>✅ Device Recognized</strong><br><br>
                Your MAC address has been captured. You can now:
                <ul>
                    <li>Take the Internet Advocacy Quiz for 1 hour access</li>
                    <li>Or use a voucher code for custom duration</li>
                </ul>
                <strong>Note:</strong> You can only connect once per day per device (using this MAC address).
            </div>
        <?php endif; ?>
        
        <div class="button-group">
            <a href="index.php" class="btn btn-primary">← Back to Home</a>
            <a href="" class="btn btn-secondary" onclick="location.reload(); return false;">🔄 Refresh</a>
        </div>
    </div>
</div>

</body>
</html>
