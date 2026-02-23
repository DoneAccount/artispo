<?php
date_default_timezone_set("Asia/Manila");

// Initialize variables
$error = null;
$success = null;
$uploadedImage = null;
$uploadedDatetime = null;
$uploadedCaption = null; // Store caption

// Handle POST request for file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //For Caption
    $caption = trim($_POST['caption'] ?? '');
    //Max 5MB
    $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
    $maxSize = 5 * 1024 * 1024; // 5MB in bytes
    
    //Error checking
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        
        //File type validation
        if (!in_array($fileType, $allowedTypes)) {
            $error = "Error: Only PNG and JPG/JPEG files are allowed.";
        }
        //File size validation
        elseif ($fileSize > $maxSize) {
            $error = "Error: File size must be less than 5MB.";
        }
        else {
            //Make unique id every upload
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid('upload_', true) . '.' . $fileExtension;
            $uploadPath = 'uploads/' . $newFileName;
            
            //Create folder if does not exist
            if (!is_dir('uploads/')) {
                if (!mkdir('uploads/', 0755, true)) {
                    $error = "Error: Could not create uploads folder.";
                }
            }
            
            //Move file to the folder
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                // Record upload datetime
                $upload_time = date("F d, Y - h:i A");
                
                // Save to log file
                $logEntry = json_encode([
                    'filename' => $newFileName,
                    'datetime' => $upload_time,
                    'caption' => $caption
                ]);
                file_put_contents('uploads/log.txt', $logEntry . PHP_EOL, FILE_APPEND);
                
                // Set success message and preview data
                $success = "Successfully uploaded!";
                $uploadedImage = 'uploads/' . $newFileName;
                $uploadedDatetime = $upload_time;
                $uploadedCaption = $caption;
            } else {
                $error = "Error: Failed to upload the file. Check folder permissions or server settings.";
            }
        }
    } else {
        //errors
        $uploadError = $_FILES['image']['error'] ?? 'Unknown';
        $errorMessages = [
            0 => 'No error',
            1 => 'File exceeds upload_max_filesize in php.ini',
            2 => 'File exceeds MAX_FILE_SIZE in form',
            3 => 'File only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing temporary folder',
            7 => 'Failed to write file to disk',
            8 => 'A PHP extension stopped the file upload'
        ];
        $error = "Error: " . ($errorMessages[$uploadError] ?? 'Unknown upload error (code: ' . $uploadError . ')');
    }
}

$currentDateTime = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image - Artispo</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="upload.css">
</head>

<body>
    <header class="top-nav">
        <div class="nav-content">
        <div class="logo">
                <img src="Artispo Logo(1).png" alt="Artispo Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="home.html">Home</a></li>
                    <li><a href="explore.html">Explore</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="profile.html">Profile</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <aside class="sidebar">
        <div class="icon"><img src="Compass.png" alt="Compass"></div>
        <div class="icon"><img src="Gallery.png" alt="Gallery"></div>
        <div class="icon"><img src="Videos.png" alt="Videos"></div>
        <div class="icon"><img src="paint-brush-icon.png" alt="Art"></div>
        <div class="icon"><img src="Home.png" alt="Home"></div>
        <div class="icon"><img src="Settings.png" alt="Settings"></div>
    </aside>

    <main class="profile-content">
        <div class="upload-container">

            <h2>Upload Post</h2>

            <form action="upload.php" method="POST" enctype="multipart/form-data" id="uploadForm">

                <!-- Upload Box - Shows preview when image is selected -->
                <label for="imageUpload" class="upload-box" id="uploadBox">
                    <div class="preview-container" id="previewContainer" style="display: none;">
                        <img id="imagePreview" class="preview-img" src="" alt="Preview">
                        <p class="preview-filename" id="previewFilename"></p>
                        <span class="change-image-text">Click to change image</span>
                    </div>
                    <div class="upload-default" id="uploadDefault">
                        <span class="upload-icon">📷</span>
                        <div class="upload-text">Click to Upload Image</div>
                        <div class="upload-subtext">PNG, JPG or JPEG (Max 5MB)</div>
                    </div>
                </label>

                <input type="file" id="imageUpload" name="image" accept=".png,.jpg,.jpeg" required>
                <textarea name="caption" class="caption-input" placeholder="Write a caption..." rows="3"></textarea>

                <button type="submit" id="uploadButton">Upload Image</button>
            </form>

            <?php if (isset($error)): ?>
                <p class="message error"><?php echo $error; ?></p>
            <?php endif; ?>

            <!-- Success Preview Section - Shows after successful upload -->
            <?php if ($success && $uploadedImage): ?>
                <div class="success-preview">
                    <h3><?php echo $success; ?></h3>
                    <img src="<?php echo htmlspecialchars($uploadedImage); ?>" alt="Uploaded Image">
                    <p>Uploaded on: <?php echo htmlspecialchars($uploadedDatetime); ?></p>
                    <div>
                        <a href="profile.php" class="view-post-btn">View Post</a>
                        <a href="upload.php" class="upload-another-btn">Upload Another</a>
                    </div>
                </div>
            <?php endif; ?>

            <br>
            <a href="profile.php" class="back-link">Back to Profile</a>

        </div>
    </main>
    
    <!-- Display Date and Time -->
    <div class="datetime-display">
        <p>Today is: <?php echo $currentDateTime; ?></p>
    </div>
    
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section about">
                <div class="logo-area">
                    <img src="Artispo_logo.png" alt="Artispo Logo" class="footer-logo">
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
                    <img src="FB Logo.png" alt="Facebook">
                    <img src="IG Logo.png" alt="Instagram">
                    <img src="Tiktok Logo.png" alt="TikTok">
                    <img src="X Logo.png" alt="X">
                </div>
            </div>
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Explore</a></li>
                    <ul class="sub-links">
                        <li><a href="#">Categories</a></li>
                        <li><a href="#">Mix Mode</a></li>
                    </ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Profile</a></li>
                    <ul class="sub-links">
                        <li><a href="#">Settings</a></li>
                    </ul>
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

    <!-- JavaScript for Image Preview -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('imageUpload');
            const uploadBox = document.getElementById('uploadBox');
            const previewContainer = document.getElementById('previewContainer');
            const uploadDefault = document.getElementById('uploadDefault');
            const imagePreview = document.getElementById('imagePreview');
            const previewFilename = document.getElementById('previewFilename');
            const uploadButton = document.getElementById('uploadButton');

            imageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Please select a PNG or JPG/JPEG file.');
                        imageInput.value = '';
                        return;
                    }
                    
                    // Validate file size (5MB max)
                    const maxSize = 5 * 1024 * 1024;
                    if (file.size > maxSize) {
                        alert('File size must be less than 5MB.');
                        imageInput.value = '';
                        return;
                    }
                    
                    // Create preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        previewFilename.textContent = file.name;
                        previewContainer.style.display = 'flex';
                        uploadDefault.style.display = 'none';
                        uploadBox.classList.add('has-preview');
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.style.display = 'none';
                    uploadDefault.style.display = 'block';
                    uploadBox.classList.remove('has-preview');
                }
            });

            // Drag and drop functionality
            uploadBox.addEventListener('dragover', function(event) {
                event.preventDefault();
                uploadBox.style.backgroundColor = '#fff4e6';
                uploadBox.style.borderColor = '#ffb347';
            });

            uploadBox.addEventListener('dragleave', function(event) {
                event.preventDefault();
                uploadBox.style.backgroundColor = '#fff';
                uploadBox.style.borderColor = '#e27b2d';
            });

            uploadBox.addEventListener('drop', function(event) {
                event.preventDefault();
                uploadBox.style.backgroundColor = '#fff';
                uploadBox.style.borderColor = '#e27b2d';
                
                const file = event.dataTransfer.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Please select a PNG or JPG/JPEG file.');
                        return;
                    }
                    
                    // Validate file size (5MB max)
                    const maxSize = 5 * 1024 * 1024;
                    if (file.size > maxSize) {
                        alert('File size must be less than 5MB.');
                        return;
                    }
                    
                    // Create preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        previewFilename.textContent = file.name;
                        previewContainer.style.display = 'flex';
                        uploadDefault.style.display = 'none';
                        uploadBox.classList.add('has-preview');
                    };
                    reader.readAsDataURL(file);
                    
                    // Set the file to the input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    imageInput.files = dataTransfer.files;
                }
            });
        });
    </script>
</body>
</html>