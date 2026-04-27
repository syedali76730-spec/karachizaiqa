-- Karachi Zaiqa Database Schema
-- MySQL Database Structure (For Shared Hosting)
-- Database name: u455754869_karachi_zaiqa

USE u455754869_karachi_zaiqa;

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) DEFAULT 'main',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add-ons Table
CREATE TABLE IF NOT EXISTS addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    customer_email VARCHAR(100),
    pickup_date DATE NOT NULL,
    pickup_time VARCHAR(20) NOT NULL,
    special_instructions TEXT,
    subtotal DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(20) DEFAULT 'cash',
    status ENUM('received', 'preparing', 'ready', 'picked_up') DEFAULT 'received',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Order Add-ons Table
CREATE TABLE IF NOT EXISTS order_addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    addon_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (addon_id) REFERENCES addons(id)
);

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Initial Products
INSERT INTO products (name, description, price, category) VALUES
('Single Plate Haleem', 'Authentic Karachi-style Haleem - Single serving', 14.00, 'main'),
('Double Plate Haleem', 'Authentic Karachi-style Haleem - Double serving', 22.00, 'main');

-- Insert Initial Add-ons
INSERT INTO addons (name, price) VALUES
('Roti', 2.00),
('Adrak (Ginger)', 2.00),
('Extra Spice', 2.00),
('Fried Onions', 2.00),
('Beverage (Pepsi/7UP)', 2.00);

-- Insert Admin User (email: syed.ali.76730@gmail.com, password: admin123)
-- Password hash for 'admin123' using PHP password_hash()
INSERT INTO admin_users (email, password_hash, name) VALUES
('syed.ali.76730@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin');

-- Create indexes for better performance
CREATE INDEX idx_order_number ON orders(order_number);
CREATE INDEX idx_order_status ON orders(status);
CREATE INDEX idx_pickup_date ON orders(pickup_date);
CREATE INDEX idx_created_at ON orders(created_at);
