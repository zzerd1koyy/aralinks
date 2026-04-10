<?php
require_once 'config.php';

$query = [];
if (isset($_GET['ip']) && $_GET['ip'] !== '') {
  $query['ip'] = $_GET['ip'];
}
if (isset($_GET['mac']) && $_GET['mac'] !== '') {
  $query['mac'] = $_GET['mac'];
}

$qs = !empty($query) ? ('?' . http_build_query($query)) : '';
$quizLink = 'quizzes/quiz.php' . $qs;
$voucherLink = 'voucher.php' . $qs;
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ARALINKS - Free WiFi with Purpose</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .modal-content p {
      line-height: 1.6;
      font-size: 16px;
    }

    @media (max-width: 480px) {
      .modal-content p {
        font-size: 14px;
      }
    }
  </style>
</head>

<body>

  <!-- GLASS ATTENTION MODAL -->
  <div id="myModal" class="modal">
    <div class="modal-content">
      <h2>Welcome to ARALinks</h2>

      <p>
        <strong>Comon High School</strong> is now providing <strong>free WiFi access</strong> to all students, teachers,
        and staff to enhance their educational experience, stay connected, and access essential resources for academic
        success. The school encourages <strong>responsible internet use</strong> and reminds everyone to uphold respect
        for others. Any misuse of the internet is strictly prohibited and may result in disciplinary action.
      </p>

      <p>
        Thank you for your attention, and we hope this new service will be of great benefit to you all.
      </p>

      <span class="close">PROCEED</span>
    </div>
  </div>

  <!-- LOGIN OPTIONS -->
  <div class="box" id="loginBox">
    <h2>Welcome to ARALinks WiFi</h2>
    <p>Choose your access method:</p>
    <p style="font-size: 14px; color: #555;">Quiz mode: 10 questions, 12 minutes per correct answer.</p>

    <a href="<?php echo htmlspecialchars($quizLink); ?>" class="btn">🧠 Answer Quiz / Trivia</a>
    <a href="<?php echo htmlspecialchars($voucherLink); ?>" class="btn">🎫 Enter Voucher Code</a>
    <a href="offline-library.php" class="btn">📚 Open Offline Library</a>
  </div>

  <!-- FOOTER -->
  <p class="footer-text">
    Comon High School ICT Department. All rights reserved. ARALinks by Dexter B. Cargullo
  </p>

  <script>
    document.querySelector(".close").onclick = function () {
      document.getElementById("myModal").style.display = "none";
      document.getElementById("loginBox").style.display = "block";
    };
  </script>

</body>

</html>