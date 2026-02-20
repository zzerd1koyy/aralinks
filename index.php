<!DOCTYPE html>
<html>
<head>
<title>ARALINKS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- GLASS ATTENTION MODAL -->
<div id="myModal" class="modal">
  <div class="modal-content">
    <h2>COMON HIGH SCHOOL WIFI</h2>

    <p>
      Comon High School is now providing public WiFi access to all students, teachers, and staff to enhance their educational experience, stay connected, and access essential resources for academic success. The school encourages responsible internet use and reminds everyone to uphold respect for others. Any misuse of the internet is strictly prohibited and may result in disciplinary action.
    </p>

    <p>
      Thank you for your attention, and we hope that this new service will be of great benefit to you all.
    </p>

    <span class="close">PROCEED</span>
  </div>
</div>

<!-- LOGIN OPTIONS -->
<div class="box" id="loginBox">
  <h2>Welcome to ARALinks WiFi</h2>
  <p>Choose access method:</p>

  <a href="quizzes/quiz.php" class="btn">🧠 Answer Quiz (1 Hour Access)</a>
  <a href="voucher.php" class="btn">🎫 Enter Voucher Code</a>
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