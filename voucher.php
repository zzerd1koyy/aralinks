<?php
session_start();
require_once 'functions.php';

// Initialize CSRF token
generateCSRFToken();

$error_message = '';
$success_message = '';

// Check for response from validation
if (isset($_SESSION['voucher_response'])) {
    $response = $_SESSION['voucher_response'];
    if (!$response['success']) {
        $error_message = $response['message'];
    }
    unset($_SESSION['voucher_response']);
}

// Get IP and MAC from session or URL
$ip = $_SESSION['ip'] ?? $_GET['ip'] ?? $_SERVER['REMOTE_ADDR'];
$mac = $_SESSION['mac'] ?? $_GET['mac'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Voucher Login - ARALINKS</title>
<link rel="stylesheet" href="style.css">
<style>
    .voucher-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #f5c6cb;
    }
    
    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #c3e6cb;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: black;
    }
    
    .form-group input {
        width: 100%;
        padding: 12px;
        border: 2px solid #007bff;
        border-radius: 6px;
        font-size: 16px;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #0056b3;
        box-shadow: 0 0 5px rgba(0, 86, 179, 0.3);
    }
    
    .form-group input::placeholder {
        color: #999;
    }
    
    .btn {
        width: 100%;
        padding: 12px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .btn:hover {
        background: #0056b3;
    }
    
    .btn:active {
        transform: scale(0.98);
    }
    
    .back-link {
        display: inline-block;
        margin-top: 15px;
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
    }
    
    .back-link:hover {
        text-decoration: underline;
    }
    
    .info-text {
        font-size: 14px;
        color: #666;
        margin-top: 10px;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 6px;
        border-left: 4px solid #007bff;
    }
    
    @media (max-width: 480px) {
        .voucher-container {
            padding: 15px;
        }
        
        .box {
            padding: 15px !important;
        }
        
        .form-group input {
            font-size: 16px; /* Prevents zoom on iOS */
        }
    }
</style>
</head>
<body>

<div class="voucher-container">
    <div class="box">
        <h2>🎫 Enter Voucher Code</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <form action="validate.php" method="POST">
            <div class="form-group">
                <label for="code">Voucher Code:</label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    placeholder="Enter your voucher code" 
                    required 
                    autofocus
                    maxlength="20"
                    pattern="[A-Za-z0-9]{5,20}"
                    title="Code should be 5-20 alphanumeric characters"
                >
            </div>
            
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <button type="submit" class="btn">Connect</button>
        </form>
        
        <div class="info-text">
            <strong>ℹ️ Note:</strong> Enter the 5-20 character code provided with your voucher.
        </div>
        
        <a href="index.php" class="back-link">← Back to Home</a>
    </div>
</div>

</body>
</html>