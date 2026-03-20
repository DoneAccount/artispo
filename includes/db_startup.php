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

    // UUID Generator for unique IDs
    function uuidv4() {
        // Source: https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
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
                "INSERT INTO users(user_id, username, email, password)
                VALUES (?, ?, ?, ?)",
                [uuidv4(), $username, $email, $user_password],
                "<p class='account-made'> Your account has been successfully made! </p>"
            );
            
        } catch (\PDOException $th) {
            die("Connection failed: " . $th->getmessage());
        }

        return;
    }

    function login_page($connection, $username, $user_password) {
        try {
            // Initialize empty array
            $returned_array = [];

            // Find matching hash and username in database
            $returned_array = sqlRequest(
                $connection,
                "SELECT * FROM users WHERE username = ?",
                [$username]
            );

            // Check the user
            if (!empty($returned_array)) {
                $user = $returned_array[0];

                if (password_verify($user_password, $user["password"])) {
                    login();
                    header("Location: home.php");
                    exit();
                }
            }
            
            return "<p class='invalid-user-or-pass'>Invalid username or password entered!</p>";

        } catch (\PDOException $th) {
            die("Connection failed: " . $th->getmessage());
        }
    }

?>