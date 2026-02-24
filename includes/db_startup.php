<?php
    require_once "./includes/input_sanitization.php";

    $SERVERNAME = "localhost";
    $USERNAME = "root";
    $PASSWORD = "";
    $DB_NAME = "artispo";

    function make_db_connection($servername, $db_name, $username, $password) {
        try {
            $connection = new PDO("mysql:host=$servername; dbname=$db_name", $username, $password);

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $connection;

            // Debug
            // echo "Connected successfully!";
        } catch (\PDOException $th) {
            die("Connection failed: " . $th->getMessage());
        }
    }

    // SQL Requests

    // Make a custom SQL request for unspecific situations.
    function sqlRequest($connection, $sql_request, $values_array, $custom_echo = "") {;
        try {
            $request = $connection->prepare($sql_request);
            $request_success = $request->execute($values_array);

            // Check if a custom echo statement exists
            if ($custom_echo) {
                echo $custom_echo;
            }
            
            // Check if the query begins with "SELECT"
            if (stripos(trim($sql_request), "SELECT") === 0) {
                return $request->fetchAll();
            }

            return $request_success;

        } catch (\PDOException $th) {
            die("Connection failed: " . $th->getMessage());
        }
        
    }

    // Premade SQL Requests

    // Register / Sign Up function
    function register($connection, $username, $email, $user_password) {
        try {
            // Create a new account in the database
            sqlRequest(
                $connection,
                "INSERT INTO users(username, email, user_password)
                VALUES (?, ?, ?)",
                [$username, $email, $user_password],
                "<p class='account-made'> Your account has been successfully made! </p>"
            );
            
        } catch (\PDOException $th) {
            die("Connection failed: " . $th->getmessage());
        }

        return;
    }

?>