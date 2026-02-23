<?php
    session_start();
    $logged_in = $_SESSION['logged_in'] ?? false;

    function login(){
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        setcookie('user_preference', 'Dark Mode', time() + 3600);
    }

    function logout(){
        $_SESSION = [];

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time () - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        session_destroy();
    }

    // If login is required in a page, 
    function require_login(){
        if (empty($_SESSION['logged_in'])) {
            header('Location: login.php');
            exit;
    }
}

?>