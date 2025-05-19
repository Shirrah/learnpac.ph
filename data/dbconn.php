<?php
// Database configuration
$host = 'localhost';
$dbname = 'learnpac';
$username = 'root';
$password = '';
$port = 3306;

function getDBConnection() {
    global $host, $dbname, $username, $password, $port;
    
    try {
        // Create connection
        $conn = new mysqli($host, $username, $password, $dbname, $port);

        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    } catch (Exception $e) {
        // Log error
        error_log("Database connection error: " . $e->getMessage());
        throw $e;
    }
}

// Function to safely close database connection
function closeDBConnection($conn) {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 