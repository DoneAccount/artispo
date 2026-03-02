<?php
date_default_timezone_set("Asia/Manila");

// Load uploaded posts
$uploads = [];
$logFile = 'uploads/log.txt';

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $uploadData = json_decode($line, true);
        if ($uploadData) {
            $uploads[] = $uploadData;
        }
    }
    // Reverse to show newest first
    $uploads = array_reverse($uploads);
}

$currentDateTime = date('Y-m-d H:i:s');
?>

<!-- Posts Section with Horizontal Layout -->
<section class="posts-section" style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <div class="posts-header">
        <h2>My Posts</h2>
        <div class="scroll-buttons">
            <button class="scroll-btn" onclick="scrollPosts('left')">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="scroll-btn" onclick="scrollPosts('right')">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    
    <?php if (empty($uploads)): ?>
        <div class="no-posts-message">
            <p>No posts yet. Click the + button to create your first post!</p>
        </div>
    <?php else: ?>
        <div class="horizontal-posts-container" id="postsContainer">
            <div class="horizontal-posts" id="postsTrack">
                <?php foreach ($uploads as $index => $upload): ?>
                    <div class="horizontal-post-card" onclick="openModal(<?php echo $index; ?>)">
                        <img src="uploads/<?php echo htmlspecialchars($upload['filename']); ?>" alt="Post Image" onerror="this.src='https://via.placeholder.com/350x250?text=Image+Not+Found'">
                        <div class="horizontal-post-card-info">
                            <p class="horizontal-post-card-date"><?php echo htmlspecialchars($upload['datetime']); ?></p>
                            <p class="horizontal-post-card-caption">
                                <?php if (!empty($upload['hashtags'])): ?>
                            <div class="post-hashtags">
                                <?php 
                                    $tags = explode(",", $upload['hashtags']);
                                    foreach ($tags as $tag):
                                ?>
                                    <span class="hashtag">
                                        <?php echo htmlspecialchars(trim($tag)); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                                <?php 
                                $caption = $upload['caption'] ?? 'No caption';
                                // Highlight hashtags
                                $caption = preg_replace('/#(\w+)/', '<span class="hashtag">#$1</span>', htmlspecialchars($caption));
                                echo $caption;
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- Modal for Post Details -->
<div id="postModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <div class="modal-image">
            <img id="modalImage" src="" alt="Post Image">
        </div>
        <div class="modal-details">
            <div class="modal-user">
                <div class="profile-img-small">
                    <img src="https://images.unsplash.com/photo-1690994268660-f1b243691eb6?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=736" alt="Profile">
                </div>
                <div class="modal-user-info">
                    <h3>Juana Dela Cruz</h3>
                    <span id="modalDateTime"></span>
                </div>
            </div>
            
            <div class="modal-caption">
                <p id="modalCaption"></p>
            </div>
            
            <div class="modal-meta">
                <p><i class="far fa-clock" style="margin-right: 8px;"></i>Posted on: <span id="modalDateTimeFull"></span></p>
            </div>
            
            <div class="modal-actions">
                <button onclick="likePost()">
                    <i class="far fa-heart"></i> Like
                </button>
                <button onclick="sharePost()">
                    <i class="far fa-share-square"></i> Share
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data storage for JavaScript -->
<div id="postsData" style="display: none;">
    <?php foreach ($uploads as $upload): ?>
        <div class="post-data" 
             data-filename="<?php echo htmlspecialchars($upload['filename']); ?>"
             data-datetime="<?php echo htmlspecialchars($upload['datetime']); ?>"
             data-caption="<?php echo htmlspecialchars($upload['caption'] ?? ''); ?>"
             data-hashtags="<?php echo htmlspecialchars($upload['hashtags'] ?? ''); ?>">
        </div>
    <?php endforeach; ?>
</div>

<!-- JavaScript for Horizontal Scroll and Modal -->
<script>
    // Store posts data in JavaScript
    const postsData = [];
    document.querySelectorAll('.post-data').forEach(post => {
        postsData.push({
            filename: post.dataset.filename,
            datetime: post.dataset.datetime,
            caption: post.dataset.caption,
            hashtags: post.dataset.hashtags
        });
    });

    // Horizontal scroll function
    function scrollPosts(direction) {
        const container = document.getElementById('postsContainer');
        const scrollAmount = 380;
        
        if (direction === 'left') {
            container.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        } else {
            container.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        }
    }

    // Modal functions
    function openModal(index) {
        const post = postsData[index];
        if (!post) return;

        const modal = document.getElementById('postModal');
        const modalImage = document.getElementById('modalImage');
        const modalDateTime = document.getElementById('modalDateTime');
        const modalDateTimeFull = document.getElementById('modalDateTimeFull');
        const modalCaption = document.getElementById('modalCaption');

        modalImage.src = 'uploads/' + post.filename;
        modalDateTime.textContent = post.datetime;
        modalDateTimeFull.textContent = post.datetime;
        
        let captionText = post.caption || 'No caption';
        modalCaption.innerHTML = captionText;

        if (post.hashtags) {
            let tags = post.hashtags.split(",");
            tags.forEach(tag => {
                modalCaption.innerHTML += 
                    ' <span class="hashtag">' + tag.trim() + '</span>';
            });
        }

        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('postModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('postModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    function likePost() {
        alert('Post liked! ❤️');
    }

    function sharePost() {
        const shareText = 'Check out this amazing artwork on Artispo!';
        
        if (navigator.share) {
            navigator.share({
                title: 'Artispo Post',
                text: shareText,
                url: window.location.href
            }).catch(console.error);
        } else {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link copied to clipboard! 📋');
            }).catch(() => {
                alert('Share feature available. Link: ' + window.location.href);
            });
        }
    }

    document.querySelectorAll('.horizontal-post-card img').forEach(img => {
        img.addEventListener('error', function() {
            this.src = 'https://via.placeholder.com/350x250?text=Image+Not+Found';
        });
    });

    document.getElementById('modalImage').addEventListener('error', function() {
        this.src = 'https://via.placeholder.com/600x600?text=Image+Not+Found';
    });
</script>

<style>
    /* Horizontal Posts Container */
    .horizontal-posts-container {
        width: 100%;
        overflow-x: auto;
        padding: 20px 0;
        margin: 20px 0;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #e27b2d #f6e7d0;
    }
    
    .horizontal-posts-container::-webkit-scrollbar {
        height: 8px;
    }
    
    .horizontal-posts-container::-webkit-scrollbar-track {
        background: #f6e7d0;
        border-radius: 4px;
    }
    
    .horizontal-posts-container::-webkit-scrollbar-thumb {
        background: #e27b2d;
        border-radius: 4px;
    }
    
    .horizontal-posts {
        display: flex;
        gap: 20px;
        padding: 10px 5px;
        min-width: min-content;
    }
    
    .horizontal-post-card {
        flex: 0 0 auto;
        width: 350px;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }
    
    .horizontal-post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(226, 123, 45, 0.25);
    }
    
    .horizontal-post-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }
    
    .horizontal-post-card-info {
        padding: 15px;
        background: #fffbf5;
        border-top: 3px solid #e27b2d;
    }
    
    .horizontal-post-card-date {
        color: #666;
        font-size: 14px;
        margin-bottom: 8px;
    }
    
    .horizontal-post-card-caption {
        color: #444;
        font-size: 14px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .no-posts-message {
        text-align: center;
        padding: 40px;
        background: #f6e7d0;
        border-radius: 12px;
        color: #666;
        margin: 20px 0;
    }
    
    .posts-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .posts-header h2 {
        color: #35363F;
        margin: 0;
    }
    
    .scroll-buttons {
        display: flex;
        gap: 10px;
    }
    
    .scroll-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #e27b2d;
        background: #fff;
        color: #e27b2d;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.3s ease;
    }
    
    .scroll-btn:hover {
        background: #e27b2d;
        color: #fff;
    }
    
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(5px);
    }
    
    .modal-content {
        margin: 5% auto;
        display: flex;
        max-width: 1000px;
        width: 95%;
        background-color: #fff;
        border-radius: 12px;
        overflow: hidden;
        animation: modalSlide 0.3s ease;
    }
    
    @keyframes modalSlide {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modal-image {
        flex: 1.5;
        background-color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 500px;
    }
    
    .modal-image img {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }
    
    .modal-details {
        flex: 1;
        padding: 30px;
        display: flex;
        flex-direction: column;
        background-color: #fffbf5;
    }
    
    .modal-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: #000;
        font-size: 35px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
        transition: color 0.2s;
    }
    
    .modal-close:hover {
        color: #e27b2d;
    }
    
    .modal-user {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .modal-user .profile-img-small {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        overflow: hidden;
        border: 2px solid #e27b2d;
    }
    
    .modal-user .profile-img-small img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .modal-user-info h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }
    
    .modal-user-info span {
        font-size: 13px;
        color: #888;
    }
    
    .modal-caption {
        flex: 1;
        overflow-y: auto;
    }
    
    .modal-caption p {
        font-size: 16px;
        line-height: 1.8;
        color: #444;
    }
    
    .modal-caption .hashtag {
        color: white;
        font-weight: 500;
    }
    
    .modal-meta {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        color: #888;
        font-size: 14px;
    }
    
    .modal-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }
    
    .modal-actions button {
        flex: 1;
        padding: 12px;
        border: 2px solid #e27b2d;
        background-color: transparent;
        color: #e27b2d;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .modal-actions button:hover {
        background-color: #e27b2d;
        color: #fff;
    }
    
    @media (max-width: 768px) {
        .modal-content {
            flex-direction: column;
            margin: 10% auto;
            width: 95%;
        }
        
        .modal-image {
            max-height: 50vh;
        }
        
        .horizontal-post-card {
            width: 280px;
        }
        
        .horizontal-post-card img {
            height: 200px;
        }
    }

    .modal-content {
    position: relative;   
}

.post-hashtags {
    margin-top: 8px;
}

.hashtag {
    display: inline-block;
    background-color: orange;
    color: white;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 13px;
    margin-right: 6px;
    margin-top: 4px;
}
</style>
