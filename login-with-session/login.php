<?php
    require 'sessions.php';

    if($logged_in){
        header('Location: account.php');
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $correct_email = "s@sample.com";
        $correct_pass = "password123";

        if($_POST['email'] == $correct_email && $_POST['password'] == $correct_pass ){
            login();
            header('Location: account.php');
            exit;
        } else {
            echo "Invalid login.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Sniglet:wght@400&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        <div class="logo">
            <img src="Artispo_logo.png" alt="Logo">
            <h1>Artispo</h1> 
        </div>
    </header>

    <!--Login-->
    <div class="login-box">
        <h2>Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <div class="password-container">
                <input type="password" name="password" placeholder="Password" required>
                <a href="#" class="forgot-password">Forgot Password?</a> 
            </div>
            <button type="submit" class="continue">Log In</button>
        </form>
        <div class="no-account">
            <p>Don't have an account? <a href="#">Sign up</a></p>  
        </div>
        <p class="terms">
            By logging in, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
        </p>
    </div>
    <div class="bottom-bg"></div>
    <div class="circle"></div>
    <div class="green-line"></div>
    <div class="green-line second"></div>
    <div class="green-block"></div>

    <div class="hero-images">
        <img src="painting.jpg" alt="Image 1" class="img1">  <!-- Replace with your image paths -->
        <img src="forest_painting.jpg" alt="Image 2" class="img2">
        <img src="flowers_black_bg.jpg" alt="Image 3" class="img3">
    </div>
</body>
</html>