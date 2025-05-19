<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$port = 3306;

try {
    // Create connection without database
    $conn = new mysqli($host, $username, $password, '', $port);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS learnpac";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating database: " . $conn->error);
    }

    echo "Database created successfully!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 