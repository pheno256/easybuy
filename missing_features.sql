-- Brands table
CREATE TABLE IF NOT EXISTS brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    logo VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add brand_id to products
ALTER TABLE products ADD COLUMN brand_id INT NULL;
ALTER TABLE products ADD FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL;

-- Flash sales table
CREATE TABLE IF NOT EXISTS flash_sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    discount_percentage DECIMAL(5,2) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Flash sale products table
CREATE TABLE IF NOT EXISTS flash_sale_products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    flash_sale_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flash_sale_id) REFERENCES flash_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_flash_sale_product (flash_sale_id, product_id)
);

-- Shared wishlists table
CREATE TABLE IF NOT EXISTS shared_wishlists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    share_token VARCHAR(100) UNIQUE NOT NULL,
    message TEXT,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Shared wishlist items table
CREATE TABLE IF NOT EXISTS shared_wishlist_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shared_wishlist_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shared_wishlist_id) REFERENCES shared_wishlists(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample brands
INSERT INTO brands (name, slug, description) VALUES
('Apple', 'apple', 'Premium Apple products'),
('Samsung', 'samsung', 'Samsung electronics and appliances'),
('Sony', 'sony', 'Sony entertainment products'),
('Nike', 'nike', 'Nike sports and athletic wear'),
('Adidas', 'adidas', 'Adidas sportswear'),
('LG', 'lg', 'LG home appliances'),
('HP', 'hp', 'HP computers and printers'),
('Dell', 'dell', 'Dell computing solutions');

-- Insert sample flash sale
INSERT INTO flash_sales (title, description, discount_percentage, start_date, end_date) VALUES
('Weekend Mega Sale', 'Up to 50% off on selected items', 30, NOW(), DATE_ADD(NOW(), INTERVAL 2 DAY));