<?php
// scroll_posts.php

date_default_timezone_set("Asia/Manila");

$logFile = 'uploads/log.txt';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; 
$offset = ($page - 1) * $limit;

$postsHtml = '';
$dataHtml = '';

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $allUploads = [];
    foreach ($lines as $line) {
        $uploadData = json_decode($line, true);
        if ($uploadData) {
            $allUploads[] = $uploadData;
        }
    }
}

$allUploads = array_reverse($allUploads);
$totalPosts = count($allUploads);
$currentUploads = array_slice($allUploads, $offset, $limit);

if (!empty($currentUploads)) {
    
    foreach ($currentUploads as $index => $upload) {
        
        // --- POST CARD HTML ---
        // NOTE: I added style="width:100%; aspect-ratio:1/1; object-fit:cover;" to ensure it works
        $postsHtml .= '<div class="post-card" onclick="openModal(' . ($offset + $index) . ')">';
            
            // Delete Button
            $postsHtml .= '<form method="POST" class="delete-form" onclick="event.stopPropagation()">';
                $postsHtml .= '<input type="hidden" name="filename" value="' . htmlspecialchars($upload['filename']) . '">';
                $postsHtml .= '<button type="submit" name="delete_post" class="delete-btn" onclick="return confirm(\'Delete this post?\');">';
                    $postsHtml .= '<i class="fas fa-trash"></i>';
                $postsHtml .= '</button>';
            $postsHtml .= '</form>';
            
            // Image
            $postsHtml .= '<img src="uploads/' . htmlspecialchars($upload['filename']) . '" ';
            $postsHtml .= 'alt="Post Image" ';
            // FORCE THE STYLE HERE TO BE SURE
            $postsHtml .= 'style="width: 100%; height: 100%; aspect-ratio: 1/1; object-fit: cover; display: block;" ';
            $postsHtml .= 'onerror="this.src=\'https://via.placeholder.com/600x600?text=Image+Not+Found\'">';
            
        $postsHtml .= '</div>';

        // --- HIDDEN DATA FOR MODAL ---
        $dataHtml .= '<div class="post-data" ';
            $dataHtml .= 'data-filename="' . htmlspecialchars($upload['filename']) . '" ';
            $dataHtml .= 'data-datetime="' . htmlspecialchars($upload['datetime']) . '" ';
            $dataHtml .= 'data-caption="' . htmlspecialchars($upload['caption'] ?? '') . '">';
        $dataHtml .= '</div>';
    }
}

echo json_encode([
    'html' => $postsHtml,
    'data' => $dataHtml,
    'hasMore' => ($offset + $limit) < $totalPosts
]);