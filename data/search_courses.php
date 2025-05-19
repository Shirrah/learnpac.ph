<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Log request details
error_log("Search request received: " . print_r($_GET, true));

require_once 'dbconn.php';

try {
    $conn = getDBConnection();
    
    $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';

    // Log search parameters
    error_log("Search parameters - Term: '$searchTerm', Category: '$category'");

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

    // Log the final SQL query
    error_log("Executing SQL query: $sql");
    error_log("Query parameters: " . print_r($params, true));

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    
    if (!empty($params)) {
        if (!$stmt->bind_param($types, ...$params)) {
            throw new Exception("Failed to bind parameters: " . $stmt->error);
        }
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Failed to get result: " . $stmt->error);
    }
    
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

    // Log success
    error_log("Search successful. Found " . count($courses) . " courses.");

    echo json_encode([
        'success' => true, 
        'courses' => $courses,
        'count' => count($courses)
    ]);

} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'An error occurred while searching courses: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        closeDBConnection($conn);
    }
}
?> 