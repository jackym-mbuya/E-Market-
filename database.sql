-- eCommerce Database Schema
-- Created: 2026-02-23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Database
CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    country VARCHAR(100) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT 'default-user.png',
    is_verified TINYINT(1) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT 'default-admin.png',
    role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    icon VARCHAR(50) DEFAULT NULL,
    parent_id INT(11) UNSIGNED DEFAULT NULL,
    featured TINYINT(1) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Subcategories Table (for backward compatibility)
CREATE TABLE IF NOT EXISTS subcategories (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT(11) UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT(11) UNSIGNED NOT NULL,
    subcategory_id INT(11) UNSIGNED DEFAULT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    short_description VARCHAR(500) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) DEFAULT NULL,
    stock_quantity INT(11) DEFAULT 0,
    sku VARCHAR(100) DEFAULT NULL,
    brand VARCHAR(100) DEFAULT NULL,
    model VARCHAR(100) DEFAULT NULL,
    weight DECIMAL(10,2) DEFAULT NULL,
    dimensions VARCHAR(100) DEFAULT NULL,
    tags VARCHAR(255) DEFAULT NULL,
    featured TINYINT(1) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_featured (featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product Images Table
CREATE TABLE IF NOT EXISTS product_images (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT(11) UNSIGNED NOT NULL,
    image VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Carts Table
CREATE TABLE IF NOT EXISTS carts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    user_id INT(11) UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart Items Table
CREATE TABLE IF NOT EXISTS cart_items (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id INT(11) UNSIGNED NOT NULL,
    product_id INT(11) UNSIGNED NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_cart (cart_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Wishlists Table
CREATE TABLE IF NOT EXISTS wishlists (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED NOT NULL,
    product_id INT(11) UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id),
    INDEX idx_user (user_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED NOT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) DEFAULT 0,
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    coupon_code VARCHAR(50) DEFAULT NULL,
    total DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_name VARCHAR(100) DEFAULT NULL,
    shipping_email VARCHAR(150) DEFAULT NULL,
    shipping_phone VARCHAR(20) DEFAULT NULL,
    shipping_address TEXT DEFAULT NULL,
    shipping_city VARCHAR(100) DEFAULT NULL,
    shipping_country VARCHAR(100) DEFAULT NULL,
    shipping_zip VARCHAR(20) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    tracking_number VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_order_number (order_number),
    INDEX idx_user (user_id),
    INDEX idx_status (order_status),
    INDEX idx_payment (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) UNSIGNED NOT NULL,
    product_id INT(11) UNSIGNED NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT(11) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) UNSIGNED NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(150) DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_data TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_transaction (transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Coupons Table
CREATE TABLE IF NOT EXISTS coupons (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_order_amount DECIMAL(10,2) DEFAULT 0,
    max_uses INT(11) DEFAULT NULL,
    used_count INT(11) DEFAULT 0,
    valid_from DATE DEFAULT NULL,
    valid_until DATE DEFAULT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Banners Table
CREATE TABLE IF NOT EXISTS banners (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT NULL,
    subtitle VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    link_text VARCHAR(50) DEFAULT NULL,
    position ENUM('main', 'sidebar', 'bottom', 'popup') DEFAULT 'main',
    status TINYINT(1) DEFAULT 1,
    sort_order INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_position (position),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT(11) UNSIGNED NOT NULL,
    user_id INT(11) UNSIGNED NOT NULL,
    rating INT(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT DEFAULT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings Table
CREATE TABLE IF NOT EXISTS settings (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES 
('site_name', 'eMarket'),
('site_email', 'support@emarket.com'),
('site_phone', '+1 234 567 890'),
('site_address', '123 Commerce St, Business City, USA'),
('site_logo', 'logo.png'),
('site_favicon', 'favicon.ico'),
('currency', 'USD'),
('currency_symbol', '$'),
('tax_rate', '0'),
('shipping_cost', '0'),
('free_shipping_amount', '100'),
('min_order_amount', '0'),
('order_prefix', 'ORD-'),
('date_format', 'Y-m-d'),
('time_format', 'H:i:s'),
('pagination_limit', '12'),
('maintenance_mode', '0'),
('facebook', 'https://facebook.com'),
('twitter', 'https://twitter.com'),
('instagram', 'https://instagram.com'),
('linkedin', 'https://linkedin.com'),
('youtube', 'https://youtube.com');

-- Insert default admin
INSERT INTO admins (name, email, password, role) VALUES 
('Administrator', 'admin@emarket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- Insert default categories
INSERT INTO categories (name, slug, description, icon, featured, status) VALUES 
('Electronics', 'electronics', 'Latest electronics and gadgets', 'fa-laptop', 1, 1),
('Fashion', 'fashion', 'Trendy fashion items', 'fa-tshirt', 1, 1),
('Home & Garden', 'home-garden', 'Home and garden essentials', 'fa-home', 1, 1),
('Health & Beauty', 'health-beauty', 'Health and beauty products', 'fa-heart', 1, 1),
('Sports & Toys', 'sports-toys', 'Sports equipment and toys', 'fa-basketball-ball', 1, 1),
('Books & Media', 'books-media', 'Books, music and movies', 'fa-book', 0, 1),
('Automotive', 'automotive', 'Car parts and accessories', 'fa-car', 0, 1),
('Food & Grocery', 'food-grocery', 'Food and grocery items', 'fa-shopping-basket', 0, 1);

-- Insert subcategories for Electronics
INSERT INTO subcategories (category_id, name, slug, status) VALUES 
(1, 'Mobile Phones', 'mobile-phones', 1),
(1, 'Laptops & Computers', 'laptops-computers', 1),
(1, 'Tablets', 'tablets', 1),
(1, 'Audio & Headphones', 'audio-headphones', 1),
(1, 'Cameras', 'cameras', 1),
(1, 'Gaming', 'gaming', 1);

-- Insert subcategories for Fashion
INSERT INTO subcategories (category_id, name, slug, status) VALUES 
(2, 'Men Clothing', 'men-clothing', 1),
(2, 'Women Clothing', 'women-clothing', 1),
(2, 'Shoes & Footwear', 'shoes-footwear', 1),
(2, 'Watches & Jewelry', 'watches-jewelry', 1),
(2, 'Bags & Luggage', 'bags-luggage', 1);

-- Insert subcategories for Home & Garden
INSERT INTO subcategories (category_id, name, slug, status) VALUES 
(3, 'Furniture', 'furniture', 1),
(3, 'Home Decor', 'home-decor', 1),
(3, 'Kitchen & Dining', 'kitchen-dining', 1),
(3, 'Bedding', 'bedding', 1),
(3, 'Lighting', 'lighting', 1);

-- Insert banners
INSERT INTO banners (title, subtitle, description, image, link, link_text, position, status, sort_order) VALUES 
('Office Furniture', 'SALE UP TO 50% OFF', 'Get the best furniture for your office at unbeatable prices', 'banner1.jpg', '#', 'Shop Now', 'main', 1, 1),
('Colorful Pillows', 'Starting at $19.99', 'Brighten up your space', 'banner2.jpg', '#', 'Shop Now', 'sidebar', 1, 2),
('Summer Collection', 'Up to 30% Off', 'Fresh styles for summer', 'banner3.jpg', '#', 'Shop Now', 'sidebar', 1, 3),
('Gift Special', 'Get Coupon CODE: GIFT50', 'Special discount on gift items', 'gift-banner.jpg', '#', 'Get Coupon', 'bottom', 1, 4);

-- Insert sample products
INSERT INTO products (category_id, name, slug, short_description, description, price, old_price, stock_quantity, sku, brand, featured, status) VALUES 
(3, 'Modern Office Chair', 'modern-office-chair', 'Ergonomic office chair with lumbar support', 'Premium ergonomic office chair with adjustable lumbar support, breathable mesh back, and comfortable cushioning. Perfect for long working hours.', 299.99, 399.99, 50, 'OFC-001', 'ComfortPlus', 1, 1),
(1, 'Wireless Bluetooth Headphones', 'wireless-bluetooth-headphones', 'Premium sound quality with noise cancellation', 'High-quality wireless headphones with active noise cancellation, 30-hour battery life, and premium sound quality.', 149.99, 199.99, 100, 'WBH-001', 'SoundMax', 1, 1),
(2, 'Classic Leather Watch', 'classic-leather-watch', 'Elegant timepiece for men', 'Timeless leather watch with stainless steel case, quartz movement, and genuine leather strap.', 89.99, 129.99, 75, 'CLW-001', 'TimeStyle', 1, 1),
(3, 'Minimalist Table Lamp', 'minimalist-table-lamp', 'Modern LED desk lamp', 'Sleek minimalist table lamp with adjustable brightness, USB charging port, and modern design.', 49.99, 69.99, 80, 'MTL-001', 'LumiTech', 1, 1),
(1, 'Smartphone Pro Max', 'smartphone-pro-max', 'Latest flagship smartphone', 'Flagship smartphone with 6.7-inch display, 108MP camera, 5G connectivity, and all-day battery.', 999.99, 1199.99, 30, 'SPM-001', 'TechGiant', 1, 1),
(4, 'Organic Skincare Set', 'organic-skincare-set', 'Natural beauty products', 'Complete organic skincare set including cleanser, toner, serum, and moisturizer. Vegan and cruelty-free.', 79.99, 99.99, 60, 'OSS-001', 'NatureBeauty', 1, 1),
(3, 'King Size Bed Frame', 'king-size-bed-frame', 'Modern wooden bed frame', 'Sturdy king-size bed frame made from premium oak wood with modern design and easy assembly.', 549.99, 699.99, 20, 'KSB-001', 'DreamHome', 1, 1),
(5, 'Professional Yoga Mat', 'professional-yoga-mat', 'Premium non-slip yoga mat', 'Extra-thick yoga mat with non-slip surface, eco-friendly material, and carrying strap.', 39.99, 49.99, 120, 'PYM-001', 'ZenFit', 1, 1);

-- Insert product images
INSERT INTO product_images (product_id, image, is_primary, sort_order) VALUES 
(1, 'product-1.jpg', 1, 1),
(1, 'product-1-2.jpg', 0, 2),
(1, 'product-1-3.jpg', 0, 3),
(2, 'product-2.jpg', 1, 1),
(2, 'product-2-2.jpg', 0, 2),
(3, 'product-3.jpg', 1, 1),
(4, 'product-4.jpg', 1, 1),
(5, 'product-5.jpg', 1, 1),
(6, 'product-6.jpg', 1, 1),
(7, 'product-7.jpg', 1, 1),
(8, 'product-8.jpg', 1, 1);

-- Insert coupons
INSERT INTO coupons (code, discount_type, discount_value, min_order_amount, max_uses, valid_from, valid_until, status) VALUES 
('WELCOME10', 'percentage', 10.00, 50.00, 100, '2026-01-01', '2026-12-31', 1),
('GIFT50', 'fixed', 50.00, 100.00, 50, '2026-02-01', '2026-04-30', 1),
('SUMMER20', 'percentage', 20.00, 75.00, 200, '2026-06-01', '2026-08-31', 1);

-- Insert sample user
INSERT INTO users (name, email, password) VALUES 
('John Doe', 'user@emarket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample reviews
INSERT INTO reviews (product_id, user_id, rating, review_text, status) VALUES 
(1, 1, 5, 'Excellent chair! Very comfortable for long hours of work.', 1),
(2, 1, 4, 'Great sound quality, battery lasts long.', 1),
(3, 1, 5, 'Beautiful watch, great value for money.', 1);
