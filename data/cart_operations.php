<?php
// Start session at the beginning
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

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

    // Get the request method and action
    $method = $_SERVER['REQUEST_METHOD'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    switch ($method) {
        case 'GET':
            if ($action === 'get_cart') {
                // Get cart items from session
                $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
                echo json_encode(['success' => true, 'cart' => $cart]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if ($action === 'add_to_cart') {
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                
                // Get course details from database
                $courseId = $data['courseId'];
                $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
                $stmt->bind_param("i", $courseId);
                $stmt->execute();
                $result = $stmt->get_result();
                $course = $result->fetch_assoc();
                
                if ($course) {
                    // Check if course is already in cart
                    $courseExists = false;
                    foreach ($_SESSION['cart'] as $item) {
                        if ($item['id'] == $courseId) {
                            $courseExists = true;
                            break;
                        }
                    }
                    
                    if (!$courseExists) {
                        $_SESSION['cart'][] = [
                            'id' => $course['id'],
                            'title' => $course['title'],
                            'price' => floatval($course['price']),
                            'image' => $course['image']
                        ];
                        echo json_encode(['success' => true, 'message' => 'Course added to cart']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Course already in cart']);
                    }
                } else {
                    throw new Exception("Course not found");
                }
            }
            break;

        case 'DELETE':
            if ($action === 'remove_from_cart') {
                $courseId = $_GET['courseId'];
                
                if (isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($courseId) {
                        return $item['id'] != $courseId;
                    });
                    echo json_encode(['success' => true, 'message' => 'Course removed from cart']);
                }
            }
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 