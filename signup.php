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

    <!-- Sign-Up Box -->
    <div class="signup-box">
      <h2>Sign Up</h2>
      <form id="signupForm">
        <input type="text" id="username" name="username" placeholder="Username" required>

        <input type="email" id="email" name="email" placeholder="Email Address" required>

        <div class="password-container">
          <input type="password" id="password" name="password" placeholder="Password" minlength="8" required>
          <p><i>Password must be at least 8 characters*</i></p>
        </div>

        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-type password" minlength="8" required>
        <a href="home.php">
          <button type="submit" class="continue">Continue</button>

        <div class="or">or</div>

        <button type="button" class="google-btn">
          <img src="./img/google_logo.png" alt="Google Logo">
          Continue with Google
        </button>

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
    document.getElementById("signupForm").addEventListener("submit", function (e) {
      e.preventDefault();
      const password = document.getElementById("password");
      const confirmPassword = document.getElementById("confirmPassword");

      // Let built-in validation handle empty or invalid inputs
      if (!this.checkValidity()) return;

      // Check if passwords match
      if (password.value !== confirmPassword.value) {
        e.preventDefault();
        alert("⚠️ Passwords do not match. Please re-type your password.");
        confirmPassword.focus();
        return;
      }

      alert("🎨 Account created successfully!");
      console.log("User signed up successfully:", {
        username: document.getElementById("username").value.trim(),
        email: document.getElementById("email").value.trim(),
      });

      this.reset();
    });
  </script>

</body>
</html>