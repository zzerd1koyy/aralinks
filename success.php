<?php
$time = $_GET['time'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Connected</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="box">
<h2>✅ Access Granted</h2>
<p>You are connected for <?php echo $time; ?> minutes.</p>

<p>You may now browse the internet.</p>

</div>

</body>
</html>