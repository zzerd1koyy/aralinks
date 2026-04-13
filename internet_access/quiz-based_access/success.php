<?php
session_start();
require_once '../config.php';

$time = isset($_GET['time']) ? htmlspecialchars($_GET['time']) : 'Unknown';
$type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'quiz';
$score = isset($_GET['score']) ? intval($_GET['score']) : null;
$total = isset($_GET['total']) ? intval($_GET['total']) : null;
$username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';
$password = isset($_GET['password']) ? htmlspecialchars($_GET['password']) : '';

// Validate time is numeric
if (!is_numeric($time)) {
    $time = '60';
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success - ARALINKS</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .success-box {
            background: white;
            color: black;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .success-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .success-box h1 {
            color: #28a745;
            margin: 10px 0;
        }

        .success-box p {
            font-size: 16px;
            line-height: 1.6;
            margin: 15px 0;
        }

        .access-details {
            background: #f0f8ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            text-align: left;
        }

        .access-details p {
            margin: 8px 0;
        }

        .time-display {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .info-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .next-steps {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .next-steps h3 {
            color: #155724;
            margin-top: 0;
        }

        .next-steps ul {
            text-align: left;
            margin: 10px 0;
            padding-left: 20px;
        }

        .next-steps li {
            margin: 8px 0;
            color: #155724;
        }

        .credential-box {
            background: #fff7e6;
            border-left: 4px solid #fd7e14;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            text-align: left;
        }

        .credential-box p {
            margin: 8px 0;
            font-size: 15px;
        }

        .credential-value {
            font-size: 18px;
            font-weight: bold;
            color: #fd7e14;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background: #0056b3;
        }

        @media (max-width: 480px) {
            .success-container {
                padding: 15px;
            }

            .success-box {
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .success-icon {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>

    <div class="success-container">
        <div class="success-box">
            <div class="success-icon">✅</div>
            <h1>Access Granted!</h1>
            <p>You are now connected to ARALinks WiFi.</p>

            <div class="access-details">
                <p><strong>Access Duration:</strong></p>
                <p class="time-display"><?php echo $time; ?> Minutes</p>
                <p style="margin-top: 15px; font-size: 14px;">
                    <?php if ($type === 'voucher'): ?>
                        This access was provided by voucher redemption.
                    <?php else: ?>
                        This access was earned from your quiz score.
                        <?php if ($score !== null && $total !== null): ?>
                            <br>You answered <strong><?php echo $score; ?></strong> out of
                            <strong><?php echo $total; ?></strong> correctly.
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>

            <?php if ($type === 'quiz' && $username !== '' && $password !== ''): ?>
                <div class="credential-box">
                    <p><strong>Use these MikroTik Hotspot credentials:</strong></p>
                    <p>Username: <span class="credential-value"><?php echo $username; ?></span></p>
                    <p>Password: <span class="credential-value"><?php echo $password; ?></span></p>
                    <p style="font-size: 13px; color: #666;">This account profile can be shared by up to
                        <?php echo QUIZ_HOTSPOT_SHARED_USERS; ?> users as configured in MikroTik.</p>
                </div>
            <?php endif; ?>

            <div class="next-steps">
                <h3>🎯 Next Steps</h3>
                <ul>
                    <li>Open your browser to connect to the internet</li>
                    <li>Your device has been authenticated for <?php echo $time; ?> minutes</li>
                    <li>Browse responsibly and respect others online</li>
                    <li>When your access expires, you can take another quiz or use a voucher</li>
                </ul>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <label>Access Type</label>
                    <div class="value"><?php echo ucfirst($type); ?></div>
                </div>
                <div class="info-item">
                    <label>Session Time</label>
                    <div class="value"><?php echo date('H:i:s'); ?></div>
                </div>
            </div>

            <p style="font-size: 12px; color: #999; margin-top: 20px;">
                Remember: This internet access is for academic purposes. Adhere to school policies and use the internet
                responsibly.
            </p>

            <a href="../index.php" class="back-btn">← Back to Home</a>
        </div>
    </div>

</body>

</html>