<?php
function get_user_profile_pic($connection, $username) {
    $stmt = $connection->prepare("SELECT profile_picture FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && !empty($user['profile_picture'])) {
        return "uploads/profile_pics/" . $user['profile_picture'];
    }
    
    return "./img/profile-placeholder.png";
}