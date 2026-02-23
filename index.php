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
      <strong>Comon High School</strong> is now providing <strong>free WiFi access</strong> to all students, teachers, and staff to enhance their educational experience, stay connected, and access essential resources for academic success.
    </p>

    <p>
      As part of our commitment to <strong>responsible internet use</strong>, we provide two access methods:
    </p>

    <p>
      The school encourages <strong>responsible internet use</strong> and reminds everyone to uphold respect for others. Any misuse of the internet is strictly prohibited and may result in disciplinary action.
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

  <a href="quizzes/quiz.php" class="btn">🧠 Answer Quiz (1 Hour)</a>
  <a href="voucher.php" class="btn">🎫 Voucher Code</a>
</div>

<!-- FOOTER -->
<p class="footer-text">
  Comon High School ICT Department. All rights reserved. ARALinks by Dexter B. Cargullo
</p>

<script>
document.querySelector(".close").onclick = function(){
  document.getElementById("myModal").style.display = "none";
  document.getElementById("loginBox").style.display = "block";
};
</script>

</body>
</html>