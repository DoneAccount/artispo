<?php

require_once "./includes/db_startup.php";
require_once "./includes/input_sanitization.php";
require_once "./includes/sessions.php";

// Upon posting, acquire all variables
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST["username"];
  $email = $_POST["email"];
  $password = $_POST["password"];
  $confirm_password = $_POST["confirmPassword"];

  $connection = make_db_connection("localhost", "artispo", "root", "");

  // Check for errors while registering
  $errors_array = [];

  if (!is_username_unique($username)) {
    $errors_array[] = "Username already exists.";
  }

  if (!is_email_unique($email)) {
    $errors_array[] = "Email is already used.";
  }

  if ($password != $confirm_password) {
    $errors_array[] = "Passwords do not match.";
  }

  array_merge($errors_array, is_password_strong($password));

  if (empty($errors_array)) {
    register($connection, $username, $email, password_hash($password, PASSWORD_ARGON2_DEFAULT_MEMORY_COST));
    login();
    header("Location: home.php");
  }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Artispo | Sign Up</title>
  <link rel="stylesheet" href="./css/signup.css">
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

    <!-- Error box -->
    <?php if (!empty($errors_array)): ?>
    <div class="error-box">
      <?php foreach ($errors_array as $error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Sign-Up Box -->
    <div class="signup-box">
      <h2>Sign Up</h2>
      <form id="signupForm" method="POST">
        <input type="text" id="username" name="username" placeholder="Username" required>

        <input type="email" id="email" name="email" placeholder="Email Address" required>

        <div class="password-container">
          <input type="password" id="password" name="password" placeholder="Password" minlength="8" required>
          <p><i>Password must be at least 8 characters*</i></p>
        </div>

        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-type password" minlength="8" required>
        <a href="home.php">
          <button type="submit" class="continue">Continue</button>

        <!-- Removed the google button
        <div class="or">or</div>

        <button type="button" class="google-btn">
          <img src="./img/google_logo.png" alt="Google Logo">
          Continue with Google
        </button>
        -->

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
</body>
</html>