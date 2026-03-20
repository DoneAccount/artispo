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
        "SELECT post_id, image, date_posted, description
         FROM posts
         WHERE user_id_fk = ?
         ORDER BY date_posted DESC, post_id DESC
         LIMIT ? OFFSET ?"
    );
    $stmt->bindValue(1, $userIdFk, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $currentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($currentPosts as $index => $post) {
        // Prepare values used by both HTML and modal JS
        $postId = $post['post_id'];
        $filename = $post['image'];
        $datetime = date("F d, Y - h:i A", strtotime($post['date_posted']));
        $caption = $post['description'] ?? '';

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
        $dataHtml .= 'data-filename="' . htmlspecialchars($filename) . '" ';
        $dataHtml .= 'data-datetime="' . htmlspecialchars($datetime) . '" ';
        $dataHtml .= 'data-caption="' . htmlspecialchars($caption) . '">';
        $dataHtml .= '</div>';
    }
}

echo json_encode([
    'html' => $postsHtml,
    'data' => $dataHtml,
    'hasMore' => ($offset + $limit) < $totalPosts
]);