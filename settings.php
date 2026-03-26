<?php
require_once './includes/sessions.php';

$username = $_SESSION['username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Artispo | Settings</title>
  <link rel="stylesheet" href="./css/settings.css">
  <link rel="stylesheet" href="./css/profile.css">
</head>

<body>
  <!-- Top Navigation -->
  <header class="top-nav">
    <div class="nav-content">
      <div class="logo">
        <img src="./img/Artispo_logo_long.png" alt="Artispo Logo">
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="icon"><img src="./img/Compass.png" alt="Compass"></div>
    <div class="icon"><img src="./img/Gallery.png" alt="Gallery"></div>
    <div class="icon"><img src="./img/Videos.png" alt="Videos"></div>
    <div class="icon"><img src="./img/paint-brush-icon.png" alt="Art"></div>
    <div class="icon"><img src="./img/Home.png" alt="Home"></div>
    <div class="icon"><img src="./img/Settings.png" alt="Settings"></div>
  </aside>

  <!-- Main Settings Content -->
  <main class="profile-content">
    <div class="profile-card">
      <h2>Settings</h2>
      <p style="color:#ddd; margin-bottom: 35px;">
        Signed in as <strong><?php echo htmlspecialchars($username); ?></strong>
      </p>

      <div style="text-align:left; max-width: 900px; margin: 0 auto;">
        <p style="color:#f6e7d0; margin-bottom: 18px;">Settings coming soon.</p>

        <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px;">
          <li style="background:#2f2f39; padding: 14px 16px; border-radius: 10px;">
            <strong>Profile</strong>
            <div style="color:#aaa; font-size: 0.95rem; margin-top: 6px;">Update display name, bio, and profile picture.</div>
          </li>
          <li style="background:#2f2f39; padding: 14px 16px; border-radius: 10px;">
            <strong>Security</strong>
            <div style="color:#aaa; font-size: 0.95rem; margin-top: 6px;">Change password and manage login options.</div>
          </li>
          <li style="background:#2f2f39; padding: 14px 16px; border-radius: 10px;">
            <strong>Privacy</strong>
            <div style="color:#aaa; font-size: 0.95rem; margin-top: 6px;">Control who can see your posts and contact you.</div>
          </li>
        </ul>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-section about">
        <div class="logo-area">
          <img src="./img/Artispo_logo.png" alt="Artispo Logo" class="footer-logo">
          <div>
            <h2>Artispo</h2>
            <p class="tagline">Fuel Your Imagination</p>
          </div>
        </div>
        <h3>About Us</h3>
        <p>We believe art is more than creation, it is building connection through expression. Artispo is a platform where one can browse art inspirations, acquire potential, and foster those into a creative power.</p>
      </div>

      <div class="footer-section contact">
        <h3>Contact Us</h3>
        <p><strong>Email:</strong> artispo@gmail.com</p>
        <p><strong>Contact No. (Ph):</strong> +631234567891</p>
        <div class="social-icons">
          <img src="./img/FB Logo.png" alt="Facebook">
          <img src="./img/IG Logo.png" alt="Instagram">
          <img src="./img/Tiktok Logo.png" alt="TikTok">
          <img src="./img/X Logo.png" alt="X">
        </div>
      </div>

      <div class="footer-section links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="home.php">Home</a></li>
          <li><a href="explore.php">Explore</a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="settings.php">Settings</a></li>
        </ul>
      </div>

      <div class="footer-section services">
        <h3>Services</h3>
        <ul>
          <li><a href="#">Artispo Guide</a></li>
          <li><a href="#">Help</a></li>
          <li><a href="#">Customer Service</a></li>
        </ul>
      </div>
    </div>
  </footer>
</body>
</html>
