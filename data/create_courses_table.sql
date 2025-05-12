CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    duration VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample courses
INSERT INTO courses (title, description, price, image, category, duration) VALUES
('Basic Healthcare Training', 'Learn the fundamentals of healthcare practice.', 299.00, 'https://placehold.co/400x250?text=Healthcare+Course+1', 'Healthcare', '8 weeks'),
('Mental Health Awareness', 'Understand the basics of mental health and wellbeing.', 349.00, 'https://placehold.co/400x250?text=Mental+Health+Course', 'Mental Health', '6 weeks'),
('Social Care Foundations', 'An introduction to working in the social care sector.', 399.00, 'https://placehold.co/400x250?text=Social+Care+Course', 'Social Care', '10 weeks'); 