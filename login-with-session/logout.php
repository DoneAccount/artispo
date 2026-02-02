<?php
    require 'sessions.php';
    logout();
    header('Location: login.php');
    exit;
?>