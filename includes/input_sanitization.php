<?php
    require_once "./includes/db_startup.php";

    // This file contains all input sanitization related functions
    function validate_email($email) {
        $filtered_email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);

        if ($filtered_email !== false) {
            return true;
        } 
        return false;
    }

    function is_email_unique($email) {
        $connection = make_db_connection("localhost", "artispo", "root", "");
        $matching_emails = sqlRequest($connection, "SELECT email FROM users WHERE email = ?", [$email]);

        return empty($matching_emails);
    }

    function is_username_unique($username) {
        $connection = make_db_connection("localhost", "artispo", "root", "");
        $matching_usernames = sqlRequest($connection, "SELECT username FROM users WHERE username = ?", [$username]);

        return empty($matching_usernames);
    }

    function sanitizeInput($data) {
        return trim(htmlspecialchars($data));
    }

    function is_password_strong($password) {
    // Error array
    $error_array = [];

    // Password must have a minimum of 8 characters
    if (strlen($password) <= 8) {
        $error_array[] = "Password must be longer than 8 characters.";
    }

    // Password must have at least one number
    if (!preg_match('~[0-9]~', $password)) {
        $error_array[] = "Password must have at least one number.";
    }

    // Password must have at least one special character
    if (!preg_match('/[@#_$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $password)) {
        $error_array[] = "Password must have at least one special character.";
    }

    return $error_array;
    }

?>