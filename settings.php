<?php
require_once './includes/sessions.php';
require_once './includes/db_startup.php';

$username = $_SESSION['username'] ?? 'Guest';
$connection = make_db_connection("localhost", "artispo", "root", "");

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_btn'])) {
    logout();
    header('Location: login.php');
    exit;
}

// Handle profile picture upload
if (isset($_FILES['new_profile_pic']) && $_FILES['new_profile_pic']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . "/uploads/profile_pics/";
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $tmpName = $_FILES['new_profile_pic']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['new_profile_pic']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png'];

    if (!in_array($ext, $allowed)) {
        echo "<script>alert('Only JPG, JPEG, PNG files are allowed.');</script>";
    } else {
        $newFileName = $username . '.' . $ext;
        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($tmpName, $destination)) {
            foreach ($allowed as $e) {
                if ($e !== $ext && file_exists($uploadDir . $username . '.' . $e)) {
                    unlink($uploadDir . $username . '.' . $e);
                }
            }

            $stmt = $connection->prepare("UPDATE users SET profile_picture = ? WHERE username = ?");
            $stmt->execute([$newFileName, $username]);

            header("Location: settings.php");
            exit();
        } else {
            echo "<script>alert('Failed to move uploaded file. Check folder permissions.');</script>";
        }
    }
}

// Handle bio save
if (isset($_POST['save_bio'])) {
    $newBio = $_POST['bio_content'];
    $stmt = $connection->prepare("UPDATE users SET bio = ? WHERE username = ?");
    $stmt->execute([$newBio, $username]);

    header("Location: settings.php");
    exit();
}

// Get user profile picture and bio from database
$stmt = $connection->prepare("SELECT profile_picture, bio FROM users WHERE username = ?");
$stmt->execute([$username]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

$userBio = $userData['bio'] ?? "";
$dbProfilePic = $userData['profile_picture'] ?? "";

if (!empty($dbProfilePic)) {
    $profilePic = "uploads/profile_pics/" . $dbProfilePic;
} else {
    $profilePic = "./img/profile-placeholder.png";
}
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
      <a href="profile.php" class="back-button">← Back</a>
      <div class="logo">
        <img src="./img/Artispo_logo_long.png" alt="Artispo Logo">
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <aside class="sidebar">
    <a href="explore.php" class="icon"><img src="./img/Compass.png" alt="Compass"></a>
    <div class="icon"><img src="./img/Gallery.png" alt="Gallery"></div>
    <div class="icon"><img src="./img/Videos.png" alt="Videos"></div>
    <div class="icon"><img src="./img/paint-brush-icon.png" alt="Art"></div>
    <a href="home.php" class="icon"><img src="./img/Home.png" alt="Home"></a>
    <a href="settings.php" class="icon active"><img src="./img/Settings.png" alt="Settings"></a>
  </aside>

  <!-- Main Settings Content -->
  <main class="profile-content">
    <div class="profile-card">
      <h2>Settings</h2>
      <p class="username-info">
        Signed in as <strong><?php echo htmlspecialchars($username); ?></strong>
      </p>

      <div class="settings-container">
        <p class="settings-coming-soon">Settings coming soon.</p>

        <ul class="settings-list">
          <li class="settings-item">
            <strong>Profile</strong>
            <div class="settings-item-description">Update display name, bio, and profile picture.</div>
            
            <!-- Profile Picture Section -->
            <div class="profile-settings-section">
              <div class="profile-pic-container">
                <form id="profilePicForm" method="POST" enctype="multipart/form-data" action="settings.php">
                  <label for="profilePicInput" class="profile-pic-label">
                    <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="profile-pic-preview">
                  </label>
                  <input type="file" name="new_profile_pic" id="profilePicInput" accept="image/*" class="profile-pic-input" onchange="this.form.submit();">
                </form>
                <p class="profile-pic-hint">Click to change profile picture</p>
              </div>
            </div>

            <!-- Bio Section -->
            <div class="bio-settings-section">
              <h4 class="bio-label">Bio</h4>
              
              <!-- Display Mode -->
              <div class="bio-display" id="bioDisplay">
                <?php if (!empty($userBio)): ?>
                  <p class="bio-text"><?php echo htmlspecialchars($userBio); ?></p>
                  <button class="edit-bio-btn" onclick="toggleBioEdit()">Edit</button>
                <?php else: ?>
                  <p class="no-bio">No bio yet. Click to add one!</p>
                  <button class="edit-bio-btn" onclick="toggleBioEdit()">Add Bio</button>
                <?php endif; ?>
              </div>

              <!-- Edit Mode -->
              <form class="bio-edit-form" id="bioEditForm" method="POST" action="settings.php">
                <textarea name="bio_content" placeholder="Write something about yourself..." class="bio-textarea"><?php echo htmlspecialchars($userBio); ?></textarea>
                <div class="bio-form-buttons">
                  <button type="submit" name="save_bio" class="bio-save-btn">Save</button>
                  <button type="button" class="bio-cancel-btn" onclick="toggleBioEdit()">Cancel</button>
                </div>
              </form>
            </div>
          </li>
          <li class="settings-item">
            <strong>Security</strong>
            <div class="settings-item-description">Change password and manage login options.</div>
            <form method="POST" class="logout-form">
              <button type="submit" name="logout_btn" class="logout-btn">Logout</button>
            </form>
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

  <script>
    function toggleBioEdit() {
      var displayDiv = document.getElementById('bioDisplay');
      var editForm = document.getElementById('bioEditForm');

      if (displayDiv.style.display === 'none') {
        displayDiv.style.display = 'flex';
        editForm.style.display = 'none';
      } else {
        displayDiv.style.display = 'none';
        editForm.style.display = 'flex';
      }
    }
  </script>
</body>
</html>
