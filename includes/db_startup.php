<?php

    $servername = "localhost";
    $username = "novusego";
    $password = "r00taccessG";
    $db_name = "artispo";

    try {
        $connection = new PDO("mysql:host=$servername; dbname=$db_name", $username, $password);

        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Debug
        echo "Connected successfully!";
    } catch (\PDOException $th) {
        die("Connection failed: " . $th->getMessage());
    }

    // Make a custom SQL request for unspecific situations.
    function customSqlRequest($sql_request, $values_array, $custom_echo = "") {
        global $connection;
        
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
    function register() {
        return;
    }

?>