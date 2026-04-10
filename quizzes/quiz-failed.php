<?php require_once '../config.php'; ?>
<?php
$score = isset($_GET['score']) ? htmlspecialchars($_GET['score']) : '0';
$total = isset($_GET['total']) ? htmlspecialchars($_GET['total']) : '10';
$reason = isset($_GET['reason']) ? htmlspecialchars($_GET['reason']) : 'failed';

// Validate numeric values
if (!is_numeric($score))
    $score = 0;
if (!is_numeric($total))
    $total = 10;
$minutesPerCorrect = defined('QUIZ_MINUTES_PER_CORRECT_ANSWER') ? QUIZ_MINUTES_PER_CORRECT_ANSWER : 12;
$earnedMinutes = intval($score) * $minutesPerCorrect;

$percentage = ($total > 0) ? floor(($score / $total) * 100) : 0;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result - ARALINKS</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .result-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .result-box {
            background: white;
            color: black;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .result-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .result-box h1 {
            margin: 10px 0;
            font-size: 28px;
        }

        .failed h1 {
            color: #dc3545;
        }

        .duplicate h1 {
            color: #ffc107;
        }

        .score-display {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 20px 0;
        }

        .score-bar {
            background: #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px 0;
            height: 30px;
        }

        .score-fill {
            height: 100%;
            background: linear-gradient(90deg, #dc3545, #f8a500);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            transition: width 0.5s ease;
        }

        .feedback {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            text-align: left;
        }

        .feedback h3 {
            margin-top: 0;
            color: #dc3545;
        }

        .feedback ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .feedback li {
            margin: 8px 0;
        }

        .suggestions {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            text-align: left;
        }

        .suggestions h3 {
            color: #155724;
            margin-top: 0;
        }

        .suggestions ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .suggestions li {
            margin: 8px 0;
            color: #155724;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .btn-primary,
        .btn-secondary {
            flex: 1;
            min-width: 150px;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: background 0.3s;
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
            background: #545b62;
        }

        .duplicate-message {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }

        @media (max-width: 480px) {
            .result-container {
                padding: 15px;
            }

            .result-box {
                padding: 20px;
            }

            .result-icon {
                font-size: 36px;
            }

            .result-box h1 {
                font-size: 22px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="result-container">
        <div class="result-box <?php echo $reason === 'duplicate' ? 'duplicate' : 'failed'; ?>">

            <?php if ($reason === 'duplicate'): ?>
                <div class="result-icon">⏱️</div>
                <h1>Already Used Today</h1>

                <div class="duplicate-message">
                    <p><strong>You have already used your free WiFi access for today.</strong></p>
                    <p>Each device is limited to <strong>1 free quiz access per day</strong> to ensure fair distribution of
                        resources.</p>
                </div>

                <div class="suggestions">
                    <h3>🎫 Alternative Options:</h3>
                    <ul>
                        <li>Use a <strong>Voucher Code</strong> for immediate access with custom duration</li>
                        <li>Try again <strong>tomorrow</strong> for another free quiz attempt</li>
                        <li>Contact your ICT department for special access requests</li>
                    </ul>
                </div>

            <?php else: ?>
                <div class="result-icon">❌</div>
                <h1>No Internet Time Earned</h1>

                <div class="score-display">Your Score: <?php echo $score; ?>/<?php echo $total; ?>
                    (<?php echo $percentage; ?>%)</div>

                <div class="score-bar">
                    <div class="score-fill" style="width: <?php echo $percentage; ?>%">
                        <?php echo $percentage; ?>%
                    </div>
                </div>

                <div class="feedback">
                    <h3>Your Quiz Result:</h3>
                    <ul>
                        <li>You scored: <strong><?php echo $score; ?> out of <?php echo $total; ?></strong></li>
                        <li>Each correct answer is worth: <strong><?php echo $minutesPerCorrect; ?> minutes</strong></li>
                        <li>Total minutes earned: <strong><?php echo $earnedMinutes; ?> minutes</strong></li>
                    </ul>
                </div>

                <div class="suggestions">
                    <h3>💡 Tips to Improve:</h3>
                    <ul>
                        <li>Read each question carefully before selecting an answer</li>
                        <li>Focus on understanding online safety and netiquette principles</li>
                        <li>Think about responsible internet use in your daily activities</li>
                        <li>At least 1 correct answer is needed to get internet time</li>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="button-group">
                <?php if ($reason === 'duplicate'): ?>
                    <a href="../voucher.php" class="btn-primary">💳 Use Voucher Code</a>
                <?php else: ?>
                    <a href="quiz.php" class="btn-primary">🔄 Try Again</a>
                <?php endif; ?>
                <a href="../index.php" class="btn-secondary">← Back Home</a>
            </div>
        </div>
    </div>

</body>

</html>