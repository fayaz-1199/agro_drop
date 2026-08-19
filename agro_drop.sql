CREATE DATABASE IF NOT EXISTS agro_drop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agro_drop;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(120) NOT NULL UNIQUE,
 phone VARCHAR(25) NOT NULL,
 password VARCHAR(255) NOT NULL,
 role ENUM('customer','farmer','admin') NOT NULL DEFAULT 'customer',
 address VARCHAR(255) NULL,
 created_by_farmer_id INT UNSIGNED NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT user_creator FOREIGN KEY (created_by_farmer_id) REFERENCES users(id) ON DELETE SET NULL
);
CREATE TABLE products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 farmer_id INT UNSIGNED NOT NULL,
 name VARCHAR(120) NOT NULL,
 category VARCHAR(60) NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 stock INT UNSIGNED NOT NULL DEFAULT 0,
 unit VARCHAR(20) NOT NULL DEFAULT 'kg',
 emoji VARCHAR(16) NOT NULL DEFAULT '🥬',
 description TEXT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT product_farmer FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE orders (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 customer_id INT UNSIGNED NOT NULL,
 order_number VARCHAR(25) NOT NULL UNIQUE,
 total DECIMAL(10,2) NOT NULL,
 delivery_address VARCHAR(255) NOT NULL,
 phone VARCHAR(25) NOT NULL,
 payment_method ENUM('Cash on Delivery','bKash') NOT NULL DEFAULT 'Cash on Delivery',
 status ENUM('Pending','Confirmed','Packed','On the way','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT order_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE RESTRICT
);
CREATE TABLE order_items (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 order_id INT UNSIGNED NOT NULL,
 product_id INT UNSIGNED NOT NULL,
 farmer_id INT UNSIGNED NOT NULL,
 product_name VARCHAR(120) NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 quantity INT UNSIGNED NOT NULL,
 subtotal DECIMAL(10,2) NOT NULL,
 CONSTRAINT item_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
 CONSTRAINT item_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
 CONSTRAINT item_farmer FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE RESTRICT
);
CREATE TABLE reviews (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 customer_id INT UNSIGNED NOT NULL,
 product_id INT UNSIGNED NOT NULL,
 rating TINYINT UNSIGNED NOT NULL,
 comment TEXT NOT NULL,
 status TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT review_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT review_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
 CONSTRAINT unique_customer_product_review UNIQUE (customer_id, product_id),
 CONSTRAINT valid_rating CHECK (rating BETWEEN 1 AND 5)
);

-- Password for the sample accounts: password123
INSERT INTO users (name,email,phone,password,role,address) VALUES
('Green Field Farm','farmer@agrodrop.test','01711000000','$2y$10$Evlk4.4lsKUZtCQEo5koKekUF423ic3NvPivUjjSS2sgVXsuvaQiG','farmer','Rajshahi, Bangladesh'),
('Amina Rahman','customer@agrodrop.test','01712000000','$2y$10$Evlk4.4lsKUZtCQEo5koKekUF423ic3NvPivUjjSS2sgVXsuvaQiG','customer','Dhanmondi, Dhaka');
INSERT INTO products (farmer_id,name,category,price,stock,unit,emoji,description) VALUES
(1,'Farm Fresh Tomato','Vegetables',65,80,'kg','🍅','Hand-picked ripe tomatoes, delivered fresh from the farm.'),
(1,'Aromatic Miniket Rice','Grains',95,150,'kg','🌾','Clean, fragrant local rice for everyday meals.'),
(1,'Natural Mustard Honey','Organic',650,25,'jar','🍯','Pure seasonal mustard-flower honey, no added sugar.'),
(1,'Fresh Green Chili','Vegetables',120,45,'kg','🌶️','Bright, spicy green chilies harvested this morning.'),
(1,'Red Lentil (Masoor Dal)','Grains',145,90,'kg','🫘','Nutritious premium masoor dal.'),
(1,'Farm Eggs','Dairy & Eggs',150,60,'dozen','🥚','Fresh farm eggs in a dozen pack.');
