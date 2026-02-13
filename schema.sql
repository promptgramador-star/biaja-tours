CREATE DATABASE IF NOT EXISTS biaja_db;
USE biaja_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- In a real app, use bcrypt. For demo: simple string or hash
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    deadline DATE,
    image_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a default admin user (password: admin123)
-- In production, NEVER insert plain text passwords if hashing is implemented.
INSERT INTO users (username, password_hash) VALUES ('admin', 'admin123') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO users (username, password_hash) VALUES ('hvelez', 'abcd1234') ON DUPLICATE KEY UPDATE id=id;
