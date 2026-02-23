<?php
    require 'sessions.php';

    if($logged_in){
        header('Location: account.php');
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $correct_email = "s@sample.com";
        $correct_pass = "password123";

        if($_POST['email'] == $correct_email && $_POST['password'] == $correct_pass ){
            login();
            header('Location: account.php');
            exit;
        } else {
            echo "Invalid login.";
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Artispo | Log In</title>
  <link rel="stylesheet" href="./css/login.css">
</head>
<body>

  <!-- HEADER -->
  <header>
    <div class="logo">
      <img src="./img/Artispo_logo.png" alt="Artispo Logo">
      <h1>Artispo</h1>
    </div>
  </header>

  <!-- MAIN -->
  <main>
    <!-- Decorative Images -->
    <div class="hero-images">
      <img src="./img/painting.jpg" class="img1" alt="Painting 1">
      <img src="./img/forest_painting.jpg" class="img2" alt="Forest Painting">
      <img src="./img/flowers_black_bg.jpg" class="img3" alt="Floral Art">
    </div>

    <!-- Log-In Box -->
    <div class="login-box">
      <h2>Log In</h2>
      <form id="loginForm">
        <input type="text" id="username" name="username" placeholder="Username or Email Address" required>

        <div class="password-container">
          <input type="password" id="password" name="password" placeholder="Password" minlength="8" required>
          <a href="#" class="forgot-password">Forgot Password?</a>
        </div>

        <button type="submit" class="continue"><b>Continue</b></button>

        <p class="no-account">
          <b>Don’t have an account yet?</b>
          <a href="signup.php">Sign Up</a>
        </p>

        <p class="terms">
          By continuing, you agree to Artispo’s
          <a href="#">Terms of Service</a> and
          <a href="#">Privacy Policy</a>.
        </p>
      </form>
    </div>

    <!-- Decorative shapes -->
    <div class="circle"></div>
    <div class="green-line"></div>
    <div class="green-line second"></div>
    <div class="green-block"></div>
    <div class="bottom-bg"></div>
  </main>

  <script>
    document.getElementById("loginForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const username = document.getElementById("username").value.trim();
      const password = document.getElementById("password").value.trim();

      if (!username || !password) {
        alert("⚠️ Please fill in all fields.");
        return;
      }

      if (password.length < 8) {
        alert("⚠️ Password must be at least 8 characters.");
        return;
      }

      alert("🎨 Logged in successfully!");
      // Redirect to home.php after validation
      window.location.href = "home.php";
    });
  </script>

</body>
</html>