-- Vendors table
CREATE TABLE IF NOT EXISTS vendors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    business_name VARCHAR(100) NOT NULL,
    owner_name VARCHAR(50) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(200) NOT NULL,
    address TEXT,
    business_type VARCHAR(50),
    tin_number VARCHAR(50),
    commission_rate DECIMAL(5,2) DEFAULT 10.00,
    balance DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'active', 'suspended') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Vendor applications table
CREATE TABLE IF NOT EXISTS vendor_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    business_name VARCHAR(100) NOT NULL,
    owner_name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT,
    business_type VARCHAR(50),
    tin_number VARCHAR(50),
    description TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL
);

-- Gift cards table
CREATE TABLE IF NOT EXISTS gift_cards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    balance DECIMAL(10,2) NOT NULL,
    recipient_name VARCHAR(50),
    recipient_email VARCHAR(50),
    message TEXT,
    buyer_id INT NULL,
    status ENUM('active', 'used', 'expired') DEFAULT 'active',
    expiry_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Add vendor_id to products
ALTER TABLE products ADD COLUMN vendor_id INT NULL;
ALTER TABLE products ADD FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE SET NULL;

-- Add gift card usage tracking
CREATE TABLE IF NOT EXISTS gift_card_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    gift_card_id INT NOT NULL,
    order_id INT NOT NULL,
    amount_used DECIMAL(10,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gift_card_id) REFERENCES gift_cards(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);