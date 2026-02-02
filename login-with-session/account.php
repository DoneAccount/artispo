<?php
    require 'sessions.php';
    require_login($logged_in);
?>

<!DOCTYPE html>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artispo | Log In</title>
    <style>
        * {
            font-family: "Poppins", sans-serif;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        body{
            background-color: #f6e9d5;
        }

        h1{
            font-size:50px;
            margin-left:20px;
        }

        p{
            font-size: 27px;
        }

        a {
        color: white;
        text-decoration: none;
        }
    
        button{
        background-color: #e25f01;
        padding: 12px 24px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-family: "Poppins", sans-serif;
        margin-top: 10px;
        }

        button:hover {
        background-color: #c24700;
        transform: scale(1.05);
        }

        button:active {
        background-color: #b14105;
        transform: scale(0.98);
        }

        p, button{
            margin-left: 70px;            
        }
    </style>
    <body>
        <h1><b>Welcome, User!</b></h1>
        <p><b><i>Create today.</i></b></p>
        <button><b><a href="logout.php">Log Out</a></b></button>
    </body>
</html>

