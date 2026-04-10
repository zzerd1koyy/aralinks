<?php
require_once 'config.php';

$libraryUrl = defined('OFFLINE_LIBRARY_URL') ? OFFLINE_LIBRARY_URL : '#';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline Library - ARALINKS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .library-container {
            max-width: 650px;
            margin: 0 auto;
            padding: 20px;
        }

        .library-box {
            background: white;
            color: black;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .library-box h1 {
            margin-bottom: 10px;
            color: #0f5132;
        }

        .library-box p {
            line-height: 1.6;
            margin: 12px 0;
        }

        .button-row {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn-action {
            display: inline-block;
            min-width: 200px;
            padding: 12px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #198754;
            color: #fff;
        }

        .btn-primary:hover {
            background: #157347;
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #5c636a;
        }
    </style>
</head>

<body>
    <div class="library-container">
        <div class="library-box">
            <h1>Offline Library</h1>
            <p>Access school learning resources available on the local network.</p>
            <p>This does not require an internet voucher or quiz access.</p>

            <div class="button-row">
                <a class="btn-action btn-primary" href="<?php echo htmlspecialchars($libraryUrl); ?>" target="_blank"
                    rel="noopener noreferrer">Open Offline Library</a>
                <a class="btn-action btn-secondary" href="index.php">Back to Home</a>
            </div>
        </div>
    </div>
</body>

</html>