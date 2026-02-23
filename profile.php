<?php
date_default_timezone_set("Asia/Manila");

// Load uploaded posts
$uploads = [];
$logFile = 'uploads/log.txt';

/*------------DELETE-------------*/
if (isset($_POST['delete_post']) && isset($_POST['filename'])) {
    $filenameToDelete = $_POST['filename'];
    
    if (file_exists($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $updatedLines = [];
        
        foreach ($lines as $line) {
            $uploadData = json_decode($line, true);
            
            if ($uploadData && $uploadData['filename'] !== $filenameToDelete) {
                $updatedLines[] = $line;
            }
        }
        
        // Write updated lines back to file
        file_put_contents($logFile, implode(PHP_EOL, $updatedLines) . PHP_EOL);
    }
    
    // Delete actual image file
    $imagePath = 'uploads/' . $filenameToDelete;
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
    
    // Redirect to prevent form resubmission
    header("Location: profile.php");
    exit();
}

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $uploadData = json_decode($line, true);
        if ($uploadData) {
            $uploads[] = $uploadData;
        }
    }
    //Reverse to show newest first
    $uploads = array_reverse($uploads);
}

$currentDateTime = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artispo | Profile</title>
    <link rel="stylesheet" href="./css/profile.css">
    <link rel="stylesheet" href="./css/posts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <!-- Top Navigation -->
    <header class="top-nav">
        <div class="nav-content">
            <div class="logo">
                <img src="./img/Artispo Logo(1).png" alt="Artispo Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="explore.php">Explore</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="profile.php" class="active">Profile</a></li>
                </ul>
            </nav>
        </div>

        <section class="floral-header">
            <div class="floral-content">
                <div class="floral-search">
                    <img src="./img/magnifying-glass.png" alt="Search Icon" class="search-icon">
                    <input type="text" placeholder="Search for images, videos, products, and more!">
                </div>
            </div>
        </section>
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

    <!-- Main Profile Content -->
    <main class="profile-content">
        <div class="profile-card">
            <i class="fas fa-bell notification"></i>
            <div class="profile-img">
                <img src="https://images.unsplash.com/photo-1690994268660-f1b243691eb6?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=736" alt="Profile Picture">
            </div>
            <h2>Juana Dela Cruz</h2>
            <p>Edit profile</p>
            <div class="profile-links">
                <span>My Posts</span>
                <span>Favorites</span>
                <span>Saves</span>
            </div>
            <a href="upload.php" class="add-post"><i class="fas fa-plus"></i></a>
        </div>
    </main>

    <!-- PHP INCLUDE -->
    <?php include './includes/posts.php'; ?>

    <!-- Display Date and Time -->
    <div class="datetime-display">
        <p>Today is: <?php echo $currentDateTime; ?></p>
    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-container">
            <!-- About Section -->
            <div class="footer-section about">
                <div class="logo-area">
                    <img src="./img/Artispo_logo.png" alt="Artispo Logo" class="footer-logo">
                    <div>
                        <h2>Artispo</h2>
                        <p class="tagline">Fuel Your Imagination</p>
                    </div>
                </div>
                <h3>About Us</h3>
                <p>We believe art is more than creation, it is building connection through expression.
                    Artispo is a platform where one can browse art inspirations, acquire potential,
                    and foster those into a creative power.</p>
            </div>

            <!-- Contact Section -->
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

            <!-- Quick Links -->
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="explore.php">Explore</a></li>
                    <ul class="sub-links">
                        <li><a href="#">Categories</a></li>
                        <li><a href="#">Mix Mode</a></li>
                    </ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <ul class="sub-links">
                        <li><a href="#">Settings</a></li>
                    </ul>
                </ul>
            </div>

            <!-- Services -->
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