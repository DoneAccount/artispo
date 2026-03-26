<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once './includes/db_startup.php';

$connection = make_db_connection("localhost", "artispo", "root", "");

$username = $_SESSION['username'] ?? 'Guest'; // use session username

if (isset($_FILES['new_profile_pic']) && $_FILES['new_profile_pic']['error'] === UPLOAD_ERR_OK) {

    $uploadDir = __DIR__ . "/uploads/profile_pics/";
    
    // Create folder if it doesn't exist
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

            // Delete old profile pics with other extensions
            foreach ($allowed as $e) {
                if ($e !== $ext && file_exists($uploadDir . $username . '.' . $e)) {
                    unlink($uploadDir . $username . '.' . $e);
                }
            }

            // Update database with the new filename
            $stmt = $connection->prepare("UPDATE users SET profile_picture = ? WHERE username = ?");
            $stmt->execute([$newFileName, $username]);

            // Update the $profilePic path so the new image shows immediately
            $profilePic = "uploads/profile_pics/" . $newFileName;

            // Optional: refresh the page to show new profile pic
            header("Location: profile.php");
            exit();
        } else {
            echo "<script>alert('Failed to move uploaded file. Check folder permissions.');</script>";
        }
    }
}
// ----- SET PROFILE PICTURE PATH -----
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

// Load uploaded posts
$uploads = [];
$logFile = 'uploads/log.txt';

/*------------SAVE BIO-------------*/
if (isset($_POST['save_bio'])) {
    $newBio = $_POST['bio_content'];
    
    $stmt = $connection->prepare("UPDATE users SET bio = ? WHERE username = ?");
    $stmt->execute([$newBio, $username]);

    header("Location: profile.php");
    exit();
}

/*------------DELETE-------------*/
if (isset($_POST['delete_post']) && isset($_POST['post_id'])) {
    $postIdToDelete = $_POST['post_id'];

    // Look up the stored image filename so we can delete the file from disk too.
    $imageRows = sqlRequest(
        $connection,
        "SELECT image FROM posts WHERE post_id = ?",
        [$postIdToDelete]
    );

    if (!empty($imageRows) && !empty($imageRows[0]['image'])) {
        $filenameToDelete = $imageRows[0]['image'];
        $imagePath = 'uploads/' . $filenameToDelete;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Delete the DB row (FK constraints should handle related rows if/when enabled)
    sqlRequest(
        $connection,
        "DELETE FROM posts WHERE post_id = ?",
        [$postIdToDelete]
    );

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
date_default_timezone_set("Asia/Manila");
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.0/css/all.css">

    <style>

        /* Bio Section Styles */

        .bio-container {
            margin: 10px 0;
            text-align: center;
            min-height: 80px;
            width: 100%;
        }

        .bio-display {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #555;
            font-size: 14px;
            flex-wrap: wrap;
            padding: 0 10px;
        }

        .bio-display p {
            margin: 0;
            max-width: 300px;
            line-height: 1.4;
            text-align: center;
        }

        .edit-bio-btn {
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
        }

        .edit-bio-btn:hover {
            color: #333;
        }

        .bio-edit-form {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .bio-edit-form textarea {
            width: 80%;
            max-width: 300px;
            height: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            resize: none;
        }

        .bio-edit-form button {
            padding: 5px 15px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .bio-edit-form button.cancel-btn {
            background-color: #999;
        }

        .bio-edit-form button:hover {
            background-color: #555;
        }

        .no-bio {
            color: #aaa;
            font-style: italic;
        }

        /* Profile Links Icons */

        .profile-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .profile-links a {
            text-decoration: none;
            color: #666;
            font-size: 24px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #f5f5f5;
        }

        .profile-links a:hover {
            color: #ffb347;
            background-color: #fff0f0;
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-links a:active {
            transform: scale(0.95);
        }

        /* Active state (optional - for when a tab is selected) */

        .profile-links a.active {
            color: #ff6b6b;
            background-color: #fff0f0;
        }
        
.posts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* always 3 columns */
    gap: 10px;
    width: 100%;
    max-width: 1000px; /* adjust as needed */
    margin: 10px auto;  /* spacing from top and bottom */
}
.post-card {
    position: relative; 
    width: 100%;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    cursor: pointer;
}


.post-card img {
    width: 100% !important; /* Force width */
    height: 100% !important; /* Force height */
    object-fit: cover !important; /* Force image to fill the square */
    display: block;
}

/* Delete button styling */

.delete-form {
    position: absolute;
    top: 5px;
    right: 5px;
    z-index: 10;
}

.post-card:hover .delete-form {
    opacity: 1;
}

.delete-btn {
    background: rgba(0,0,0,0.6);
    color: white;
    border: none;
    padding: 6px 8px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
}

.delete-btn:hover {
    background: rgba(255,0,0,0.8);
}

/* Tablet */
@media (max-width: 768px) {
    .posts-grid {
        max-width: 400px;
    }
}

/* Mobile Phone */
@media (max-width: 480px) {
    .posts-grid {
        max-width: 100%; /* Full width on phone */
        gap: 3px;
    }
}


    </style>

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

</head>

<body>

    <!-- Top Navigation -->
    <header class="top-nav">
        <div class="nav-content">
            <div class="logo">
                <img src="./img/Artispo_logo_long.png" alt="Artispo Logo">
            </div>
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
        <a href="explore.php" class="icon"><img src="./img/Compass.png" alt="Compass"></a>
        <div class="icon"><img src="./img/Gallery.png" alt="Gallery"></div>
        <div class="icon"><img src="./img/Videos.png" alt="Videos"></div>
        <div class="icon"><img src="./img/paint-brush-icon.png" alt="Art"></div>
        <a href="home.php" class="icon"><img src="./img/Home.png" alt="Home"></a>
        <a href="settings.php" class="icon"><img src="./img/Settings.png" alt="Settings"></a>
    </aside>

    <!-- Main Profile Content -->
    <main class="profile-content">
        <div class="profile-card">

        <div class="profile-img">
            <form id="profilePicForm" method="POST" enctype="multipart/form-data" action="profile.php">
                <label for="profilePicInput" style="cursor:pointer;">
                    <img src="<?php echo $profilePic; ?>" alt="Profile Picture">
                </label>
                <input type="file" name="new_profile_pic" id="profilePicInput" accept="image/*" style="display:none;" onchange="this.form.submit();">
            </form>
        </div>

            <h2><?php echo htmlspecialchars($username); ?></h2>


            <!-- Bio Section -->
            <div class="bio-container">

                <!-- Display Mode -->
                <div class="bio-display" id="bioDisplay">

                    <?php if (!empty($userBio)): ?>

                        <p><?php echo htmlspecialchars($userBio); ?></p>

                        <button class="edit-bio-btn" onclick="toggleBioEdit()">
                            <i class="fas fa-pen"></i>
                        </button>

                    <?php else: ?>

                        <p class="no-bio">No bio yet. Click the button to add one!</p>

                        <button class="edit-bio-btn" onclick="toggleBioEdit()">
                            <i class="fas fa-plus"></i>
                        </button>

                    <?php endif; ?>

                </div>

                <!-- Edit Mode -->
                <form class="bio-edit-form" id="bioEditForm" method="POST" action="profile.php">

                    <textarea name="bio_content" placeholder="Write something about yourself..."><?php echo htmlspecialchars($userBio); ?></textarea>

                    <div>
                        <button type="submit" name="save_bio">Save</button>
                        <button type="button" class="cancel-btn" onclick="toggleBioEdit()">Cancel</button>
                    </div>

                </form>

            </div>

            <div class="profile-links">
                <a href="#" title="My Posts"><i class="fas fa-images"></i></a>
                <a href="#" title="Favorites"><i class="fas fa-heart"></i></a>
                <a href="#" title="Saves"><i class="fas fa-bookmark"></i></a>
            </div>

            <a href="upload.php" class="add-post">
                <i class="fas fa-plus"></i>
            </a>

        </div>
    </main>

    <!-----------PHP INCLUDE------------------>
    <?php include './includes/posts.php'; ?>

    <!-- LOADING ANIMATION FOR INFINITE SCROLL -->
    <div class="loader" id="loader" style="text-align:center; padding:20px; display:none;">
        <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #888;"></i>
    </div>

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
                        <li><a href="settings.php">Settings</a></li>
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

    <script>
        // INFINITE SCROLL LOGIC
        let page = 1;
        let isLoading = false;
        const loader = document.getElementById('loader');
        const postsGrid = document.querySelector('.posts-grid');
        const postsDataContainer = document.getElementById('postsData');

        // Function to load more posts
        function loadMorePosts() {
            if (isLoading) return;
            
            isLoading = true;
            loader.style.display = 'block'; // Show spinner

            page++; // Increase page number

            fetch('./includes/scroll_posts.php?page=' + page)
                .then(response => response.json())
                .then(data => {
                    if (data.html) {
                        // 1. Append the images to the grid
                        postsGrid.insertAdjacentHTML('beforeend', data.html);
                        
                        // 2. Append the hidden data for the Modal to work
                        postsDataContainer.insertAdjacentHTML('beforeend', data.data);

                        // 3. Update the JavaScript array for the Modal
                        document.querySelectorAll('.post-data').forEach(post => {
                            // Check if already exists in array to avoid duplicates
                            const exists = postsData.some(p => p.filename === post.dataset.filename);
                            if(!exists) {
                                postsData.push({
                                    filename: post.dataset.filename,
                                    datetime: post.dataset.datetime,
                                    caption: post.dataset.caption,
                                    username: post.dataset.username,
                                    profilePic: post.dataset.profilePic
                                });
                            }
                        });
                    }

                    if (!data.hasMore) {
                        // Stop trying to load if no more posts
                        window.removeEventListener('scroll', handleScroll);
                        loader.innerHTML = '<p style="color:#aaa;">No more posts to show.</p>';
                    }

                    isLoading = false;
                    loader.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error loading posts:', error);
                    isLoading = false;
                    loader.style.display = 'none';
                });
        }

        // Detect scroll
        function handleScroll() {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
                loadMorePosts();
            }
        }

        // Add event listener
        window.addEventListener('scroll', handleScroll);    
        document.addEventListener("DOMContentLoaded", function () {
            
        const backToTopBtn = document.getElementById("backToTopBtn");
        window.addEventListener("scroll", function () {
            if (window.scrollY > 300) {
                backToTopBtn.style.display = "block";
            } else {
                backToTopBtn.style.display = "none";
            }
        });
        
        backToTopBtn.addEventListener("click", function () {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    });

    </script>
    
    <!-- Back To Top Button -->

<button id="backToTopBtn">
    <i class="fas fa-arrow-up"></i> Back To Top
</button>

</body>
</html>