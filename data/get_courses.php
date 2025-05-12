<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Database configuration
$host = 'sql12.freesqldatabase.com';
$dbname = 'sql12778389';
$username = 'sql12778389';
$password = 'qEJaU42yQ4';
$port = 3306;

try {
    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname, $port);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Fetch courses from database
    $sql = "SELECT * FROM courses";
    $result = $conn->query($sql);

    if ($result) {
        $courses = array();
        while ($row = $result->fetch_assoc()) {
            $courses[] = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'price' => floatval($row['price']),
                'image' => $row['image'],
                'category' => $row['category'],
                'duration' => $row['duration']
            );
        }
        echo json_encode(['success' => true, 'courses' => $courses]);
    } else {
        throw new Exception("Error fetching courses: " . $conn->error);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 