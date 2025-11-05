-- Create DB (if not already)
CREATE DATABASE IF NOT EXISTS expiry_tracker;
USE expiry_tracker;

-- users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','salesman') NOT NULL DEFAULT 'salesman',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- products table
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_name VARCHAR(255) NOT NULL,
  retailer_name VARCHAR(255) NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  expiry_date DATE NOT NULL,
  category VARCHAR(100),
  remarks TEXT,
  added_by INT NOT NULL,
  added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Seed an admin user
-- Password below is 'Admin@123' (hashed). You can change after login.
INSERT INTO users (name, email, password, role)
VALUES ('Super Admin', 'admin@company.com', 
        '$2y$10$e0NRZ0u0nB2t1Qp4S2r2Re3KXK0c3rYb7aZq5YyZp7QmD0y1u2e4a', 'admin');
-- The hash corresponds to password: Admin@123
