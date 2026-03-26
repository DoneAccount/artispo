<?php
/* ---------------- ADDED: LOAD POSTS FROM MySQL FOR EXPLORE PAGE ---------------- */
require_once './includes/db_startup.php';

$uploads = [];
$connection = make_db_connection("localhost", "artispo", "root", "");

$stmt = $connection->prepare(
    "SELECT p.post_id, p.image, p.date_posted, p.description, u.username, u.profile_picture
     FROM posts p
     INNER JOIN users u ON u._id = p.user_id_fk
     ORDER BY p.date_posted DESC, p.post_id DESC"
);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $profilePic = !empty($row['profile_picture']) ? "uploads/profile_pics/" . $row['profile_picture'] : "./img/profile-placeholder.png";

    $uploads[] = [
        'post_id' => $row['post_id'],
        'filename' => $row['image'],
        'datetime' => date("F d, Y - h:i A", strtotime($row['date_posted'])),
        'caption' => $row['description'] ?? '',
        'username' => $row['username'],
        'profile_pic' => $profilePic,
        'hashtags' => ''
    ];
}

/* ---------------- ADDED: FILTER SYSTEM ---------------- */

$filter = $_GET['filter'] ?? 'all';
/* ---------------- END ADDED ---------------- */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Artispo | Artwork Detail</title>
  <link rel="stylesheet" href="./css/explore.css" />
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
  <a href="explore.php" class="icon">
    <img src="./img/Compass.png" alt="Compass">
  </a>
  <!-- TODO: hrefs should point to GET query parameters -->
  <!-- ADDED FILTER -->
  <a href="explore.php?filter=images" class="icon">
    <img src="./img/Gallery.png" alt="Gallery">
  </a>
  <!-- ADDED FILTER -->
  <a href="explore.php?filter=videos" class="icon">
    <img src="./img/Videos.png" alt="Videos">
  </a>
  <!-- ADDED FILTER -->
  <a href="explore.php?filter=art" class="icon">
    <img src="./img/paint-brush-icon.png" alt="Art">
  </a>
  <a href="index.php" class="icon">
    <img src="./img/Home.png" alt="Home">
  </a>
  <a href="settings.php" class="icon">
    <img src="./img/Settings.png" alt="Settings">
  </a>
</aside>

  
  <!-- Main Content -->
<main class="explore-main">
    <div class="explore-header">
      <div>
        <h2>Explore</h2>
        <p>mix mode</p>
      </div>
      <div class="search-box">
        <input type="text" placeholder="Search account">
        <span>🔍</span>
      </div>
      <div class="icons-right">
        <a href=" " class="bell">🔔</a>
        <a href="profile.php" class="profile">👤</a>      </div>
    </div>

   <!-- Gallery Grid -->
    <div class="gallery">
  
 <!-- ---------------- ADDED: DISPLAY USER UPLOADED POSTS IN EXPLORE ---------------- -->

<?php if (!empty($uploads)): ?>
    
    <?php foreach ($uploads as $index => $upload): ?>
        
        <div class="item image-item medium" onclick="openPostModal(<?php echo $index; ?>)">
            
            <div class="item-icon">
                <img src="./img/Gallery.png" alt="Image Icon">
            </div>

            <div class="media-container">
                <img src="uploads/<?php echo htmlspecialchars($upload['filename']); ?>" alt="Uploaded Post">
            </div>

        </div>

    <?php endforeach; ?>

<?php endif; ?>



<!-- ---------------- END ADDED ---------------- -->

      <!-- Example Image Box 
<a href="post.php" class="item-link">
  <div class="item image-item small">
    <div class="item-icon"><img src="./img/Gallery.png" alt="Image Icon"></div>
    <div class="media-container">
      <img src="https://www.shutterstock.com/image-illustration/realistic-over-abstract-cat-pet-600nw-2440374701.jpg" alt="Image Example">
    </div>
  </div>
</a>


     Example Art Box
      <div class="item art-item medium">
        <div class="item-icon"><img src="./img/paint-brush-icon.png" alt="Art Icon"></div>
        <div class="media-container"><img src="https://www.pictoclub.com/wp-content/uploads/2021/09/painting-brushes-scaled.jpg" alt="Art Example"></div>
      </div>  

      Example Video Box 
  <a href="post.php" class="item-link">
  <div class="item video-item small">
    <div class="item-icon"><img src="./img/Videos.png" alt="Video Icon"></div>
    <div class="media-container">
      <video preload="none">
        <source src="Vid1.mp4" type="video/mp4">
      </video>
    </div>
  </div>
</a> -->


      <!-- More Boxes Below 
      <div class="item image-item medium">
        <div class="item-icon"><img src="./img/Gallery.png" alt="Image Icon"></div>
        <div class="media-container"><img src="https://www.rileystreet.com/cdn/shop/articles/shutterstock_410271079_1024x1024.jpg?v=1624398205" alt="Image 2"></div>
      </div>

      <div class="item art-item large">
        <div class="item-icon"><img src="./img/paint-brush-icon.png" alt="Art Icon"></div>
        <div class="media-container"><img src="https://www.deserres.ca/cdn/shop/articles/pcrea-29_1_520x500_520x500_f9ab0a82-dec9-4e1c-8653-668725ca3bfb.jpg?v=1734624432&width=500" alt="Art 2"></div>
      </div>

      <div class="item video-item medium">
        <div class="item-icon"><img src="./img/Videos.png" alt="Video Icon"></div>
        <div class="media-container">
          <video controls preload="none">
            <source src="Vid2.mp4" type="video/mp4">
          </video>
        </div>
      </div>

      <div class="item image-item small">
        <div class="item-icon"><img src="./img/Gallery.png" alt="Image Icon"></div>
        <div class="media-container"><img src="https://t4.ftcdn.net/jpg/00/76/80/97/360_F_76809767_Gb6A91Jm9DvdFe6UuUHQkzhcUyYjZCJf.jpg" alt="Image 3"></div>
      </div>

      <div class="item art-item medium">
        <div class="item-icon"><img src="./img/paint-brush-icon.png" alt="Art Icon"></div>
        <div class="media-container"><img src="https://www.shutterstock.com/image-photo/wooden-easel-blank-canvas-on-600nw-2332856331.jpg" alt="Art 3"></div>
      </div>

      <div class="item video-item large">
        <div class="item-icon"><img src="./img/Videos.png" alt="Video Icon"></div>
        <div class="media-container">
          <video controls preload="none">
            <source src="Vid3.mp4" type="video/mp4">
          </video>
        </div>
      </div>

      <div class="item image-item large">
        <div class="item-icon"><img src="./img/Gallery.png" alt="Image Icon"></div>
        <div class="media-container"><img src="https://images.fineartamerica.com/images/artworkimages/mediumlarge/3/6-original-abstract-art-contemporary-modern-art-oversized-large-prints-painting-megan-duncanson-megan-duncanson.jpg" alt="Image 4"></div>
      </div>

      <div class="item art-item small">
        <div class="item-icon"><img src="./img/paint-brush-icon.png" alt="Art Icon"></div>
        <div class="media-container"><img src="https://cdn.britannica.com/35/179035-050-BDD4FF0E/Oil-paints-consistency-paste-variety-colors-brushes.jpg " alt="Art 4"></div>
      </div>

      <div class="item video-item medium">
        <div class="item-icon"><img src="./img/Videos.png" alt="Video Icon"></div>
        <div class="media-container">
          <video controls preload="none">
            <source src="Vid4.mp4" type="video/mp4">
          </video>
        </div>
      </div>

      <div class="item image-item medium">
        <div class="item-icon"><img src="./img/Gallery.png" alt="Image Icon"></div>
        <div class="media-container"><img src="https://images.pexels.com/photos/1070534/pexels-photo-1070534.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="Image 5"></div>
      </div>

      <div class="item art-item large">
        <div class="item-icon"><img src="./img/paint-brush-icon.png" alt="Art Icon"></div>
        <div class="media-container"><img src="https://www.shuttleart.com/cdn/shop/files/9_f6d2a2b8-12e1-4b53-90bb-a63f639c1be9_700x700.jpg?v=1733987901" alt="Art 5"></div>
      </div>

      <div class="item image-item small">
        <div class="item-icon"><img src="./img/Gallery.png" alt="Image Icon"></div>
        <div class="media-container"><img src="https://media.istockphoto.com/id/543829586/photo/oil-painting-canal-in-venice-italy-famous-tourist-place-colorful.jpg?s=612x612&w=0&k=20&c=P399FavKM16AgsSEWvaXEcgqBL2v_HapE7wHH3oDR0g=" alt="Image 6"></div>
      </div>

      <div class="item video-item small">
        <div class="item-icon"><img src="./img/Videos.png" alt="Video Icon"></div>
        <div class="media-container">
          <video controls preload="none">
            <source src="Vid5.mp4" type="video/mp4">
          </video>
        </div>
      </div>

    </div>
  </main> More Boxes Below -->


  <script>
  window.addEventListener('DOMContentLoaded', () => {
    const videos = document.querySelectorAll('video');
    videos.forEach(video => {
      video.muted = true;      // required for autoplay
      video.autoplay = true;
      video.loop = true;       // optional
      video.play().catch(err => console.log(err)); // prevents errors if blocked
    });
  });
  </script>

  
</script>

<?php include './includes/posts_explore.php'; ?>

</body>
</html>
</body>
</html>

</body>
</html>