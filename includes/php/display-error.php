<?php 

    // Connects to register.php

    /*
    References:
    $errors_caught = [
        "name" => false,
        "email" => false,
        "password_length" => false,
        "password_digit" => false,
        "confirm_password" => false,
        "age" => false,
        "terms" => false
    ];
    */

    // Function for making an error paragraph element
    function make_error($message) {
        return "<p class='error-text'>" . $message . "</p>";
    }

    // Make errors to display
    $errors_to_display = [];

    if ($errors_caught["name"]) {
        $errors_to_display[] = "Please enter a valid name.";
    }
    if ($errors_caught["email"]) {
        $errors_to_display[] = "Please enter a valid email.";
    }
    if ($errors_caught["password_length"]) {
        $errors_to_display[] = "Password must be 8 characters or longer.";
    }
    if ($errors_caught["password_digit"]) {
        $errors_to_display[] = "Password must contain at least one digit.";
    }
    /* Use when confirm_password has been added
    if ($errors_caught["confirm_password"]) {
        $errors_to_display[] = "Confirm password does not match.";
    }    
    */
    if ($errors_caught["age"]) {
        $errors_to_display[] = "Please enter a valid age.";
    }
    if ($errors_caught["terms"]) {
        $errors_to_display[] = "Please read and agree to the Terms of Service.";
    }

?>

<div class="error-box">
    <?php 
    
    foreach ($errors_to_display as $displayed_error) {
        echo make_error($displayed_error);
    } 

    ?>
</div>