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

require_once 'dbconn.php';

try {
    $conn = getDBConnection();
    $response = array('success' => false);

    // Handle GET request for cart contents
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_cart') {
        $cart_items = array();
        
        // Get cart items with course details
        $stmt = $conn->prepare("
            SELECT c.id, c.title, c.price, c.image, ci.quantity 
            FROM cart_items ci 
            JOIN courses c ON ci.course_id = c.id 
            WHERE ci.session_id = ?
        ");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        
        $session_id = session_id();
        if (!$stmt->bind_param("s", $session_id)) {
            throw new Exception("Failed to bind parameters: " . $stmt->error);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute query: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Failed to get result: " . $stmt->error);
        }
        
        $total = 0;
        while ($row = $result->fetch_assoc()) {
            $item_total = floatval($row['price']) * intval($row['quantity']);
            $total += $item_total;
            
            $cart_items[] = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'price' => floatval($row['price']),
                'image' => $row['image'],
                'quantity' => intval($row['quantity']),
                'item_total' => $item_total
            );
        }
        
        echo json_encode([
            'success' => true,
            'cart' => $cart_items,
            'total' => $total,
            'item_count' => count($cart_items)
        ]);
        exit();
    }

    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['action'])) {
            throw new Exception('Missing action parameter');
        }

        $session_id = session_id();

        switch ($data['action']) {
            case 'add':
                if (!isset($data['course_id'])) {
                    throw new Exception('Missing course_id parameter');
                }

                // Check if course exists
                $stmt = $conn->prepare("SELECT id, price FROM courses WHERE id = ?");
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $conn->error);
                }
                
                if (!$stmt->bind_param("i", $data['course_id'])) {
                    throw new Exception("Failed to bind parameters: " . $stmt->error);
                }
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to execute query: " . $stmt->error);
                }
                
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    throw new Exception('Course not found');
                }

                $quantity = isset($data['quantity']) ? max(1, intval($data['quantity'])) : 1;

                // Add or update cart item
                $stmt = $conn->prepare("
                    INSERT INTO cart_items (session_id, course_id, quantity) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE quantity = quantity + ?
                ");
                
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $conn->error);
                }
                
                if (!$stmt->bind_param("siii", $session_id, $data['course_id'], $quantity, $quantity)) {
                    throw new Exception("Failed to bind parameters: " . $stmt->error);
                }
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to execute query: " . $stmt->error);
                }

                $response['message'] = 'Course added to cart';
                $response['success'] = true;
                break;

            case 'update':
                if (!isset($data['course_id'])) {
                    throw new Exception('Missing course_id parameter');
                }

                $quantity = isset($data['quantity']) ? max(1, intval($data['quantity'])) : 1;

                $stmt = $conn->prepare("
                    UPDATE cart_items 
                    SET quantity = ? 
                    WHERE session_id = ? AND course_id = ?
                ");
                
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $conn->error);
                }
                
                if (!$stmt->bind_param("isi", $quantity, $session_id, $data['course_id'])) {
                    throw new Exception("Failed to bind parameters: " . $stmt->error);
                }
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to execute query: " . $stmt->error);
                }

                if ($stmt->affected_rows === 0) {
                    throw new Exception('Course not found in cart');
                }

                $response['message'] = 'Cart updated';
                $response['success'] = true;
                break;

            case 'remove':
                if (!isset($data['course_id'])) {
                    throw new Exception('Missing course_id parameter');
                }

                $stmt = $conn->prepare("
                    DELETE FROM cart_items 
                    WHERE session_id = ? AND course_id = ?
                ");
                
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $conn->error);
                }
                
                if (!$stmt->bind_param("si", $session_id, $data['course_id'])) {
                    throw new Exception("Failed to bind parameters: " . $stmt->error);
                }
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to execute query: " . $stmt->error);
                }

                if ($stmt->affected_rows === 0) {
                    throw new Exception('Course not found in cart');
                }

                $response['message'] = 'Course removed from cart';
                $response['success'] = true;
                break;

            case 'clear':
                $stmt = $conn->prepare("DELETE FROM cart_items WHERE session_id = ?");
                
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $conn->error);
                }
                
                if (!$stmt->bind_param("s", $session_id)) {
                    throw new Exception("Failed to bind parameters: " . $stmt->error);
                }
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to execute query: " . $stmt->error);
                }

                $response['message'] = 'Cart cleared';
                $response['success'] = true;
                break;

            default:
                throw new Exception('Invalid action');
        }
    }

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Cart operation error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
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