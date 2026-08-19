-- Run this once on an existing AgroDrop database.
-- New installations can import agro_drop.sql instead.

ALTER TABLE users
    ADD COLUMN created_by_farmer_id INT UNSIGNED NULL AFTER address,
    ADD CONSTRAINT user_creator FOREIGN KEY (created_by_farmer_id) REFERENCES users(id) ON DELETE SET NULL;

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
