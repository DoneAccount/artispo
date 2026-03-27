<?php
// scroll_posts.php

date_default_timezone_set("Asia/Manila");

require_once './includes/sessions.php';
require_once './includes/db_startup.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$limit = (int)$limit;
$offset = (int)$offset;

$postsHtml = '';
$dataHtml = '';
$totalPosts = 0;

$userIdFk = (int)($_SESSION['user_id'] ?? 0);

if ($userIdFk > 0) {
    $connection = make_db_connection("localhost", "artispo", "root", "");

    // Total count for hasMore
    $stmt = $connection->prepare("SELECT COUNT(*) AS cnt FROM posts WHERE user_id_fk = ?");
    $stmt->execute([$userIdFk]);
    $countRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalPosts = (int)($countRow['cnt'] ?? 0);

    // Page slice
    $stmt = $connection->prepare(
        "SELECT p._id, p.post_id, p.image, p.date_posted, p.description, u.username
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
    $currentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $commentStmt = $connection->prepare("
        SELECT c.comment_content, c.date_posted, u.username, u.profile_picture 
        FROM comments c 
        JOIN users u ON c.user_id_fk = u._id 
        WHERE c.post_id_fk = ? 
        ORDER BY c.date_posted ASC
    ");

    foreach ($currentPosts as $index => $post) {
        $commentStmt->execute([$post['_id']]);
        $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($comments as &$c) {
            $c['profile_picture'] = !empty($c['profile_picture']) ? "uploads/profile_pics/" . $c['profile_picture'] : "./img/profile-placeholder.png";
            $c['date_posted'] = date("M d, Y", strtotime($c['date_posted']));
        }

        // Prepare values used by both HTML and modal JS
        $dbId = $post['_id'];
        $postId = $post['post_id'];
        $filename = $post['image'];
        $datetime = date("F d, Y - h:i A", strtotime($post['date_posted']));
        $caption = $post['description'] ?? '';
        $username = $post['username'] ?? 'Unknown User';
        $profilePic = "./img/profile-placeholder.png";
        $matches = glob("uploads/profile_pics/" . $username . ".*");
        if (!empty($matches)) {
            $profilePic = $matches[0];
        }

        // --- POST CARD HTML ---
        $postsHtml .= '<div class="post-card" onclick="openModal(' . ($offset + $index) . ')">';

        // Delete Button
        $postsHtml .= '<form method="POST" class="delete-form" onclick="event.stopPropagation()">';
        $postsHtml .= '<input type="hidden" name="post_id" value="' . htmlspecialchars($postId) . '">';
        $postsHtml .= '<button type="submit" name="delete_post" class="delete-btn" onclick="return confirm(\'Delete this post?\');">';
        $postsHtml .= '<i class="fas fa-trash"></i>';
        $postsHtml .= '</button>';
        $postsHtml .= '</form>';

        // Image
        $postsHtml .= '<img src="uploads/' . htmlspecialchars($filename) . '" ';
        $postsHtml .= 'alt="Post Image" ';
        $postsHtml .= 'style="width: 100%; height: 100%; aspect-ratio: 1/1; object-fit: cover; display: block;" ';
        $postsHtml .= 'onerror="this.src=\'https://via.placeholder.com/600x600?text=Image+Not+Found\'">';

        $postsHtml .= '</div>';

        // --- HIDDEN DATA FOR MODAL ---
        $dataHtml .= '<div class="post-data" ';
        $dataHtml .= 'data-db-id="' . htmlspecialchars($dbId) . '" ';
        $dataHtml .= 'data-filename="' . htmlspecialchars($filename) . '" ';
        $dataHtml .= 'data-datetime="' . htmlspecialchars($datetime) . '" ';
        $dataHtml .= 'data-caption="' . htmlspecialchars($caption) . '" ';
        $dataHtml .= 'data-username="' . htmlspecialchars($username) . '" ';
        $dataHtml .= 'data-profile-pic="' . htmlspecialchars($profilePic) . '" ';
        $dataHtml .= 'data-comments=\'' . htmlspecialchars(json_encode($comments), ENT_QUOTES, 'UTF-8') . '\'>';
        $dataHtml .= '</div>';
    }
}

echo json_encode([
    'html' => $postsHtml,
    'data' => $dataHtml,
    'hasMore' => ($offset + $limit) < $totalPosts
]);