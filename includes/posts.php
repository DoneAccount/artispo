<?php
date_default_timezone_set("Asia/Manila");

// Load posts from MySQL (uploads/log.txt is legacy)
require_once './includes/sessions.php';
require_once './includes/db_startup.php';

$uploads = [];
$currentDateTime = date('Y-m-d H:i:s');

$userIdFk = (int)($_SESSION['user_id'] ?? 0);
$limit = 5; // keep in sync with infinite scroll offset logic
$limit = (int)$limit;
$connection = make_db_connection("localhost", "artispo", "root", "");

if ($userIdFk > 0) {
    $offset = 0;

    $stmt = $connection->prepare(
        "SELECT p._id, p.post_id, p.image, p.date_posted, p.description, u.username, u.profile_picture
         FROM posts p
         INNER JOIN users u ON u._id = p.user_id_fk
         WHERE user_id_fk = ?
         ORDER BY p.date_posted DESC, p.post_id DESC
         LIMIT ? OFFSET ?"
    );
    $stmt->bindValue(1, $userIdFk, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $commentStmt = $connection->prepare("
        SELECT c.comment_content, c.date_posted, u.username, u.profile_picture 
        FROM comments c 
        JOIN users u ON c.user_id_fk = u._id 
        WHERE c.post_id_fk = ? 
        ORDER BY c.date_posted ASC
    ");

    foreach ($rows as $row) {
        $profilePic = !empty($row['profile_picture']) ? "uploads/profile_pics/" . $row['profile_picture'] : "./img/profile-placeholder.png";

        $commentStmt->execute([$row['_id']]);
        $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($comments as &$c) {
            $c['profile_picture'] = !empty($c['profile_picture']) ? "uploads/profile_pics/" . $c['profile_picture'] : "./img/profile-placeholder.png";
            $c['date_posted'] = date("M d, Y", strtotime($c['date_posted']));
        }

        $uploads[] = [
            'db_id' => $row['_id'],
            'post_id' => $row['post_id'],
            'filename' => $row['image'],
            'datetime' => date("F d, Y - h:i A", strtotime($row['date_posted'])),
            'caption' => $row['description'] ?? '',
            'username' => $row['username'],
            'profile_pic' => $profilePic,
            'comments' => $comments,
            'hashtags' => ''
        ];
    }
}
?>


<!-- Posts Section with Horizontal Layout -->
<section class="posts-section" style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <div class="posts-header">
        <h2>My Posts</h2>
    </div>
    
    <?php if (empty($uploads)): ?>
        <div class="no-posts-message">
            <p>No posts yet. Click the + button to create your first post!</p>
        </div>
    <?php else: ?>
        
        <div class="posts-grid">
                <?php foreach ($uploads as $index => $upload): ?>
                    <div class="post-card" onclick="openModal(<?php echo $index; ?>)">
                        <!--DELETE BUTTON-->
                        <form method="POST" class="delete-form" onclick="event.stopPropagation()">
                            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($upload['post_id']); ?>">
                            <button type="submit" name="delete_post" class="delete-btn" onclick="return confirm('Delete this post?');">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        <img src="uploads/<?php echo htmlspecialchars($upload['filename']); ?>" alt="Post Image" onerror="this.src='https://via.placeholder.com/600x600?text=Image+Not+Found'">
                        <div class="horizontal-post-card-info">
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

    <?php endif; ?>

</section>
<!-- Modal Post Details -->
<div id="postModal" class="modal">
    <div class="modal-content">
        <div class="modal-image">
            <img id="modalImage" src="" alt="Post Image">
        </div>
        <div class="modal-details">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <div class="modal-user">
                <div class="profile-img-small">
                    <img id="modalUserProfilePic" src="./img/profile-placeholder.png" alt="Profile">
                </div>
                <div class="modal-user-info">
                    <h3 id="modalUsername">Unknown User</h3>
                    <span id="modalDateTime"></span>
                </div>
            </div>
            
            <div class="modal-caption">
                <p id="modalCaption"></p>
            </div>
            
            <div class="modal-comments">
                <h4>Comments</h4>
                <div id="commentsList" class="comments-list"></div>
                <form method="POST" class="comment-form">
                    <input type="hidden" name="comment_post_db_id" id="modalPostDbId">
                    <div class="comment-input-group">
                        <img src="<?php echo $profilePic ?? './img/profile-placeholder.png'; ?>" alt="My Profile" class="comment-user-img">
                        <input type="text" name="comment_content" placeholder="Add a comment..." required>
                        <button type="submit" name="submit_comment"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </form>
            </div>

            <div class="modal-meta">
                <p><i class="far fa-clock" style="margin-right: 8px;"></i>Posted on: <span id="modalDateTimeFull"></span></p>
            </div>
            
            <div class="modal-actions">
                <button type="button">
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
             data-db-id="<?php echo $upload['db_id']; ?>"
             data-filename="<?php echo htmlspecialchars($upload['filename']); ?>"
             data-datetime="<?php echo htmlspecialchars($upload['datetime']); ?>"
             data-caption="<?php echo htmlspecialchars($upload['caption'] ?? ''); ?>"
             data-username="<?php echo htmlspecialchars($upload['username'] ?? 'Unknown User'); ?>"
             data-profile-pic="<?php echo htmlspecialchars($upload['profile_pic'] ?? './img/profile-placeholder.png'); ?>"
             data-comments='<?php echo htmlspecialchars(json_encode($upload['comments']), ENT_QUOTES, 'UTF-8'); ?>'
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
            dbId: post.dataset.dbId,
            filename: post.dataset.filename,
            datetime: post.dataset.datetime,
            caption: post.dataset.caption,
            username: post.dataset.username,
            profilePic: post.dataset.profilePic,
            comments: JSON.parse(post.dataset.comments || '[]'),
            hashtags: post.dataset.hashtags
        });
    });

    // Modal functions
    function openModal(index) {
        const post = postsData[index];
        if (!post) return;

        const modal = document.getElementById('postModal');
        const modalImage = document.getElementById('modalImage');
        const modalDateTime = document.getElementById('modalDateTime');
        const modalDateTimeFull = document.getElementById('modalDateTimeFull');
        const modalCaption = document.getElementById('modalCaption');
        const modalUsername = document.getElementById('modalUsername');
        const modalUserProfilePic = document.getElementById('modalUserProfilePic');

        document.getElementById('modalPostDbId').value = post.dbId;
        modalImage.src = 'uploads/' + post.filename;
        modalDateTime.textContent = post.datetime;
        modalDateTimeFull.textContent = post.datetime;
        modalUsername.textContent = post.username || 'Unknown User';
        modalUserProfilePic.src = post.profilePic || './img/profile-placeholder.png';
        renderComments(post.comments);
        
        let captionText = post.caption || '<i>No caption</i>';
        captionText = captionText.replace(/#(\w+)/g, '<span class="hashtag">#$1</span>');
        modalCaption.innerHTML = captionText;

        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function renderComments(comments) {
        const list = document.getElementById('commentsList');
        list.innerHTML = '';
        if (comments.length === 0) {
            list.innerHTML = '<p class="no-comments">No comments yet.</p>';
            return;
        }
        comments.forEach(c => {
            list.innerHTML += `
                <div class="comment-item">
                    <img src="${c.profile_picture}" alt="${c.username}">
                    <div class="comment-text"><strong>${c.username}</strong> ${c.comment_content}<br><small>${c.date_posted}</small></div>
                </div>`;
        });
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

    document.getElementById('modalImage').addEventListener('error', function() {
        this.src = 'https://via.placeholder.com/600x600?text=Image+Not+Found';
    });
</script>
<style>
    /* Comments Section inside Modal */
    .modal-comments {
        margin-top: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 150px;
    }
    .modal-comments h4 { margin: 0 0 10px 0; color: #e27b2d; border-bottom: 1px solid #e27b2d; padding-bottom: 5px; }
    .comments-list { flex: 1; overflow-y: auto; max-height: 250px; margin-bottom: 10px; }
    .comment-item { display: flex; gap: 10px; margin-bottom: 12px; font-size: 14px; }
    .comment-item img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 1px solid #e27b2d; }
    .comment-text { background: #fff; padding: 8px 12px; border-radius: 12px; flex: 1; line-height: 1.4; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .comment-text small { color: #888; font-size: 11px; }
    .no-comments { color: #888; font-style: italic; font-size: 13px; text-align: center; margin-top: 10px; }
    
    .comment-form { margin-top: auto; border-top: 1px solid #eee; padding-top: 10px; }
    .comment-input-group { display: flex; align-items: center; gap: 10px; }
    .comment-user-img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; }
    .comment-input-group input { 
        flex: 1; 
        padding: 10px 15px; 
        border: 1px solid #ddd; 
        border-radius: 20px; 
        outline: none; 
        font-size: 14px;
    }
    .comment-input-group input:focus { border-color: #e27b2d; }
    .comment-input-group button { 
        background: none; 
        border: none; 
        color: #e27b2d; 
        cursor: pointer; 
        font-size: 18px; 
        padding: 5px;
        transition: transform 0.2s;
    }
    .comment-input-group button:hover { transform: scale(1.1); }

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
        color: #e27b2d;
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
        background-color: #f6e7d0;
        border-radius: 12px;
        overflow: hidden;
        animation: modalSlide 0.3s ease;
        position: relative;
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
        background-color: #f6e7d0;
    }
    
/* Close Button inside the Post/Sidebar */
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
         background: none;
         outline: none;
    }
    
    .modal-user {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e27b2d;
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
        color: #e27b2d;
        font-weight: 500;
    }
    
    .modal-meta {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e27b2d;
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