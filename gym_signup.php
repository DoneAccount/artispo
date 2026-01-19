<?php
    /* 
    Note to future self:
    Maintain consistency when using single quotes (') and double quotes (").
    I've been mixing them up throughout this activity :(
    All echo var_dump was used for debugging
    */

    // Page Functions
    function signup_page() {
        return '<form method="POST" action="">
            <label for="fullname">Full Name:</label>
            <input type="text" name="fullname" id="fullname">
            <label for="email">Email:</label>
            <input type="text" name="email" id="email">
            <label for="password">Password (Min 8 chars, 1 number):</label>
            <input type="password" name="password" id="password">
            <label for="membership">Membership Level:</label>
            <select name="membership" id="membership">
                <option value="Basic">Basic</option>
                <option value="Pro">Pro</option>
                <option value="Exclusive">Exclusive</option>
            </select>
            <input type="checkbox" name="terms" id="terms">
            <label for="terms" class="terms">I agree to the Terms and Conditions</label>
            <button type="submit" name="signup">Sign Up</button>
        </form>';
    }
    // Note to self: use includes next time instead of using a return function im too lazy to fix this now

    function confirmed_signup_page($fullname, $email, $membership) {
        return '<div class="signup-box"><h1>Registration Complete!</h1>
        <p>Welcome, ' . htmlspecialchars($fullname) . '!</p>
        <p>A confirmation email has been sent to: ' . htmlspecialchars($email) . '</p>
        <p>Membership level: <strong>' . $membership . '</strong></p>
        </div>';
    }

    // Error Checking Functions, returns true if an error is found.
    function validate_name($name) {
        //echo "validate name" . var_dump($name == "");
        if ($name == "") {
            return true;
        }
        return false;
    }

    function validate_email($email) {
        //echo "validate email" . var_dump(filter_var($email, FILTER_VALIDATE_EMAIL) === false);
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return true;
        }
        return false;
    }

    function validate_password_length($password) {
        //echo "validate pass length" . var_dump(strlen($password) <= 8);
        if (strlen($password) <= 8) {
            return true;
        }
        return false;
    }

    function validate_password_digit($password) {
        //echo "validate pass digit" . var_dump(preg_match("/\d/", $password));
        if (preg_match("/\d/", $password)) {
            return false;
        }
        return true;
    }

    function validate_terms($terms) {
        //echo "validate terms" . var_dump($terms != "on");
        if ($terms != "on") {
            return true;
        }
        return false;
    }

    // Function to check if an error is made in the inputs, returns boolean value
    function error_exists($errors_array) {
        $error_exists = false;

        foreach ($errors_array as $error_type => $exists) {
            if ($exists) {
                $error_exists = true;
            }
        }
        return $error_exists;
    }

    // Function to display the errors inside an unordered list.
    function display_errors($errors_array) {
        $errors_to_display = ["<div class='errors'><h4>Please fix the following errors:</h4>", "<ul class='error'>"];
        
        // Check each type of error and update the page depending on the 
        if ($errors_array["name"]) {
            $errors_to_display[] = "<li>Name must not be empty.</li>";
        }
        if ($errors_array["email"]) {
            $errors_to_display[] =  "<li>Please enter a valid email.</li>";
        }
        if ($errors_array["password_length"]) {
            $errors_to_display[] =  "<li>Password must be at least 8 characters long.</li>";
        }
        if ($errors_array["password_digit"]) {
            $errors_to_display[] =  "<li>Password must have at least one digit.</li>";
        }
        if ($errors_array["terms"]) {
            $errors_to_display[] =  "<li>You must have read and agreed to the Terms of Service to proceed.</li>";
        }
        
       $errors_to_display[] =  "</ul></div>";
    //   echo "errors to display list" . var_dump($errors_to_display);
       return $errors_to_display;
    }

    // Set the current page to the signup page
    $current_page = signup_page();
    $display_errors = false;

    // Initialize errors to display (becuase php is mean :c)
    $errors_to_display = ["a", "b"];

    // Check if user posted
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        // Gather all variables upon POSTing
        $fullname = $_POST["fullname"] ?? "NULL";
        $email = $_POST["email"] ?? "noemail@email.com";
        $password = $_POST["password"] ?? "";
        $membership = $_POST["membership"] ?? "No Membership";
        $terms = $_POST["terms"] ?? 'off';

        // Check for any errors
        $errors = [
            "name" => validate_name($fullname),
            "email" => validate_email($email),
            "password_length" => validate_password_length($password),
            "password_digit" => validate_password_digit($password),
            "terms" => validate_terms($terms)
        ];

        if (!(error_exists($errors))) {
            $current_page = confirmed_signup_page($fullname, $email, $membership);
            $display_errors = false;
        } else {
            $display_errors = true;
            $errors_to_display = display_errors($errors);
        }

        //echo "display errors" . var_dump($display_errors);
        //echo "error exists" . var_dump(error_exists($errors));
    }

    /*
    YESSSSSSSSSSSSSSSSSS YTESSSSSSSSSSSSS ITS WORKING ITS WORKINGGGGG LETS GO
    LETS GOOOOOOOOOOOOOOOOOOOOOOOO RAHGHHHHHHHHHHHGHGHGHGHGHHHHHHHHHHHHH
    HAHAHAHAHAHHAHAHAHAAHAH YESSSSSSSSSSSS
    */
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Iron Gym Signup</title>
        <link href="styles.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, intial-scale=1.0">
    </head>
    <body>
        <h1>Join Iron Gym</h1>
        <?php
            if ($display_errors) {
                foreach($errors_to_display as $markup_text) {
                    echo $markup_text;
                }
            }
        ?>
        <?= $current_page ?>
    </body>
</html>

