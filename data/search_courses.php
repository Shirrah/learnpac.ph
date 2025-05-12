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
    $conn = new mysqli($host, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $searchTerm = isset($_GET['q']) ? $_GET['q'] : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';

    // Prepare the SQL query
    $sql = "SELECT * FROM courses WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($searchTerm)) {
        $sql .= " AND (title LIKE ? OR description LIKE ?)";
        $searchTerm = "%$searchTerm%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ss";
    }

    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
        $types .= "s";
    }

    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'price' => floatval($row['price']),
            'image' => $row['image'],
            'category' => $row['category'],
            'duration' => $row['duration']
        ];
    }

    echo json_encode(['success' => true, 'courses' => $courses]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 