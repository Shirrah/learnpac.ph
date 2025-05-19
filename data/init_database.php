<?php
require_once 'dbconn.php';

try {
    $conn = getDBConnection();
    
    // Create courses table
    $sql = "CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        category VARCHAR(50),
        duration VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($sql)) {
        throw new Exception("Error creating courses table: " . $conn->error);
    }
    
    // Create cart_items table
    $sql = "CREATE TABLE IF NOT EXISTS cart_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(255) NOT NULL,
        course_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
        UNIQUE KEY unique_cart_item (session_id, course_id)
    )";
    
    if (!$conn->query($sql)) {
        throw new Exception("Error creating cart_items table: " . $conn->error);
    }
    
    // Check if we need to insert sample data
    $result = $conn->query("SELECT COUNT(*) as count FROM courses");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        // Insert sample courses
        $sql = "INSERT INTO courses (title, description, price, image, category, duration) VALUES
            ('Basic Healthcare Training', 'Learn the fundamentals of healthcare practice.', 299.00, 'https://placehold.co/400x250?text=Healthcare+Course+1', 'Healthcare', '8 weeks'),
            ('Mental Health Awareness', 'Understand the basics of mental health and wellbeing.', 349.00, 'https://placehold.co/400x250?text=Mental+Health+Course', 'Mental Health', '6 weeks'),
            ('Social Care Foundations', 'An introduction to working in the social care sector.', 399.00, 'https://placehold.co/400x250?text=Social+Care+Course', 'Social Care', '10 weeks'),
            ('First Aid Training', 'Essential first aid skills for healthcare professionals.', 249.00, 'https://placehold.co/400x250?text=First+Aid+Course', 'Healthcare', '4 weeks'),
            ('Dementia Care', 'Specialized training for dementia care professionals.', 449.00, 'https://placehold.co/400x250?text=Dementia+Care+Course', 'Healthcare', '12 weeks')";
        
        if (!$conn->query($sql)) {
            throw new Exception("Error inserting sample data: " . $conn->error);
        }
    }
    
    echo "Database initialized successfully!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        closeDBConnection($conn);
    }
}
?> 