<?php
// Start session at the beginning
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    $response = array('success' => false);

    // Handle GET request for cart contents
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_cart') {
        $cart_items = array();
        
        if (!empty($_SESSION['cart'])) {
            // Get course details for each item in cart
            $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
            $stmt = $conn->prepare("SELECT id, title, price, image FROM courses WHERE id IN ($placeholders)");
            $stmt->bind_param(str_repeat('i', count($_SESSION['cart'])), ...$_SESSION['cart']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $cart_items[] = array(
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'price' => floatval($row['price']),
                    'image' => $row['image']
                );
            }
        }
        
        echo json_encode([
            'success' => true,
            'cart' => $cart_items
        ]);
        exit();
    }

    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['action']) || !isset($data['course_id'])) {
            throw new Exception('Missing required parameters');
        }

        switch ($data['action']) {
            case 'add':
                // Check if course exists
                $stmt = $conn->prepare("SELECT id FROM courses WHERE id = ?");
                $stmt->bind_param("i", $data['course_id']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception('Course not found');
                }

                // Add course to cart if not already present
                if (!in_array($data['course_id'], $_SESSION['cart'])) {
                    $_SESSION['cart'][] = $data['course_id'];
                    $response['message'] = 'Course added to cart';
                } else {
                    $response['message'] = 'Course already in cart';
                }

                $response['success'] = true;
                $response['cart_count'] = count($_SESSION['cart']);
                break;

            case 'remove':
                // Remove course from cart
                $key = array_search($data['course_id'], $_SESSION['cart']);
                if ($key !== false) {
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
                    $response['message'] = 'Course removed from cart';
                } else {
                    $response['message'] = 'Course not found in cart';
                }
                $response['success'] = true;
                $response['cart_count'] = count($_SESSION['cart']);
                break;

            default:
                throw new Exception('Invalid action');
        }
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 