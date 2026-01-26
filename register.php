<?php

include_once "./includes/html/register.html";

// Initiated Variables
$errors_caught = [
    "name" => false,
    "email" => false,
    "password_length" => false,
    "password_digit" => false,
    "confirm_password" => false,
    "age" => false,
    "terms" => false
];

// Functions to validate user inputs
function validate_name($name) {
    return empty($name);
}

function validate_email($email) {
    // Safe emails return false
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        return true;
    }
    return false;
}

function validate_password_length($password) {
    return strlen($password) < 8;
}

function validate_password_digit($password) {
    // Returns true if no digit is found
    return !(preg_match("/\d/", $password));
}

// TODO: Add confirm password in html
function validate_confirm_password($password, $confirm_password) {
    return $password !== $confirm_password;
}

function validate_age($age) {
    return $age <= 0;
}

function validate_terms($terms) {
    return $terms != "on";
}

// Upon submitting the form, gather all the variables
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Gather all the form information
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    // $confirm_password = $_POST["confirm-password"];
    $age = $_POST["age"];
    $terms = $_POST["terms"];

    $errors_caught = [
        "name" => validate_name($username),
        "email" => validate_email($email),
        "password_length" => validate_password_length($password),
        "password_digit" => validate_password_digit($password),
        // "confirm_password" => validate_confirm_password($password, $confirm_password),
        "age" => validate_age($age),
        "terms" => validate_terms($terms)
    ];
}

foreach ($errors_caught as $error_type => $error_happened) {
    if ($error_happened) {
        include_once "./includes/php/display-error.php";
    }
}

?>