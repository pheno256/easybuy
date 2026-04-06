-- ============================================
-- EasyBuy Uganda - Sample Data
-- Version: 2.0.0
-- ============================================

USE easybuy_db;

-- ============================================
-- SAMPLE CATEGORIES
-- ============================================

INSERT INTO categories (name, slug, description, status) VALUES
('Electronics', 'electronics', 'Latest electronic gadgets and devices', 'active'),
('Fashion', 'fashion', 'Trendy clothing and accessories for men and women', 'active'),
('Home & Living', 'home-living', 'Beautiful home decor and furniture', 'active'),
('Phones & Tablets', 'phones-tablets', 'Smartphones and tablets from top brands', 'active'),
('Computing', 'computing', 'Laptops, desktops and accessories', 'active'),
('Sports & Outdoors', 'sports-outdoors', 'Sports equipment and outdoor gear', 'active'),
('Beauty & Health', 'beauty-health', 'Beauty products and health supplements', 'active'),
('Baby & Kids', 'baby-kids', 'Products for babies and children', 'active'),
('Automotive', 'automotive', 'Car accessories and parts', 'active'),
('Books & Media', 'books-media', 'Books, movies, and music', 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- ============================================
-- SAMPLE BRANDS
-- ============================================

INSERT INTO brands (name, slug, description, status) VALUES
('Apple', 'apple', 'Premium Apple products including iPhone, Mac, iPad', 'active'),
('Samsung', 'samsung', 'Samsung electronics and appliances', 'active'),
('Sony', 'sony', 'Sony entertainment products and electronics', 'active'),
('Nike', 'nike', 'Nike sports and athletic wear', 'active'),
('Adidas', 'adidas', 'Adidas sportswear and accessories', 'active'),
('LG', 'lg', 'LG home appliances and electronics', 'active'),
('HP', 'hp', 'HP computers and printers', 'active'),
('Dell', 'dell', 'Dell computing solutions', 'active'),
('Huawei', 'huawei', 'Huawei smartphones and devices', 'active'),
('Tecno', 'tecno', 'Tecno mobile phones', 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- ============================================
-- SAMPLE PRODUCTS
-- ============================================

INSERT INTO products (name, slug, description, price, discount_price, stock, category_id, brand_id, featured, trending, status) VALUES
('iPhone 14 Pro', 'iphone-14-pro', 'Latest iPhone with dynamic island, A16 Bionic chip, and pro camera system', 3500000, 3200000, 10, 4, 1, 1, 1, 'active'),
('Samsung Galaxy S23 Ultra', 'samsung-galaxy-s23-ultra', 'Premium Android smartphone with 200MP camera and S Pen', 3200000, 2900000, 15, 4, 2, 1, 1, 'active'),
('MacBook Air M2', 'macbook-air-m2', 'Apple laptop with M2 chip, 13.6-inch Liquid Retina display', 4500000, 4300000, 5, 5, 1, 1, 1, 'active'),
('Sony WH-1000XM5', 'sony-wh-1000xm5', 'Industry-leading noise canceling headphones', 550000, 500000, 20, 1, 3, 1, 1, 'active'),
('Nike Air Max', 'nike-air-max', 'Comfortable running shoes with Air Max technology', 350000, 300000, 30, 2, 4, 1, 1, 'active'),
('Adidas Ultraboost', 'adidas-ultraboost', 'Energy returning running shoes', 380000, 330000, 25, 2, 5, 0, 1, 'active'),
('LG OLED TV', 'lg-oled-tv', '65-inch 4K Smart OLED TV with AI ThinQ', 4500000, 4200000, 8, 1, 6, 1, 0, 'active'),
('HP Spectre x360', 'hp-spectre-x360', 'Convertible laptop with Intel Core i7', 3800000, 3600000, 12, 5, 7, 0, 1, 'active'),
('Dell XPS 15', 'dell-xps-15', 'Premium laptop with InfinityEdge display', 4200000, 4000000, 7, 5, 8, 0, 0, 'active'),
('Huawei P60 Pro', 'huawei-p60-pro', 'Professional camera phone with XMAGE', 2800000, 2600000, 18, 4, 9, 0, 1, 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- ============================================
-- SAMPLE COUPONS
-- ============================================

INSERT INTO coupons (code, name, description, discount_type, discount_value, min_order_amount, usage_limit, start_date, expiry_date, status) VALUES
('WELCOME10', 'Welcome Discount', '10% off your first order', 'percentage', 10, 50000, 1000, NOW(), DATE_ADD(NOW(), INTERVAL 90 DAY), 'active'),
('SAVE20', 'Save 20%', '20% off on orders above UGX 100,000', 'percentage', 20, 100000, 500, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'active'),
('FREESHIP', 'Free Shipping', 'Free delivery on any order', 'fixed', 15000, 0, 200, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 'active'),
('FLASH15', 'Flash Sale', '15% off flash sale', 'percentage', 15, 50000, 300, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'active')
ON DUPLICATE KEY UPDATE code=VALUES(code);

-- ============================================
-- SAMPLE BLOG CATEGORIES
-- ============================================

INSERT INTO blog_categories (name, slug, description, status) VALUES
('Shopping Tips', 'shopping-tips', 'Tips and tricks for better online shopping', 'active'),
('Product Reviews', 'product-reviews', 'Honest reviews of our products', 'active'),
('Company News', 'company-news', 'Updates and announcements from EasyBuy', 'active'),
('Guides & Tutorials', 'guides-tutorials', 'How-to guides and tutorials', 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- ============================================
-- SAMPLE BLOG POSTS
-- ============================================

INSERT INTO blog_posts (title, slug, content, excerpt, category_id, author_id, status, published_at) VALUES
('How to Shop Safely Online in Uganda', 'how-to-shop-safely-online', 
'<p>Online shopping is convenient, but safety is important. Here are essential tips for safe online shopping in Uganda...</p>
<h3>1. Use Secure Websites</h3>
<p>Always look for HTTPS in the URL and a padlock icon in the address bar.</p>
<h3>2. Keep Your Credentials Safe</h3>
<p>Never share your passwords or PIN with anyone.</p>
<h3>3. Verify Seller Authenticity</h3>
<p>Check reviews and ratings before making a purchase.</p>',
'Learn essential tips for safe online shopping in Uganda. Protect yourself from fraud and shop with confidence.', 
1, 1, 'published', NOW()),

('Top 10 Products of 2024', 'top-10-products-2024', 
'<p>Discover the most popular products on EasyBuy Uganda this year...</p>
<h3>1. iPhone 14 Pro</h3>
<p>The most sought-after smartphone with amazing camera features.</p>
<h3>2. Samsung Galaxy S23 Ultra</h3>
<p>Premium Android experience with S Pen functionality.</p>',
'Check out the most popular and best-selling products on EasyBuy Uganda in 2024.', 
2, 1, 'published', NOW())
ON DUPLICATE KEY UPDATE title=VALUES(title);

-- ============================================
-- SAMPLE FLASH SALE
-- ============================================

INSERT INTO flash_sales (title, description, discount_percentage, start_date, end_date, status) VALUES
('Weekend Mega Sale', 'Up to 30% off on selected items', 30, NOW(), DATE_ADD(NOW(), INTERVAL 2 DAY), 'active')
ON DUPLICATE KEY UPDATE title=VALUES(title);

-- Add products to flash sale
INSERT INTO flash_sale_products (flash_sale_id, product_id)
SELECT 1, id FROM products WHERE featured = 1 LIMIT 5
ON DUPLICATE KEY UPDATE flash_sale_id=flash_sale_id;

-- ============================================
-- SAMPLE REVIEWS
-- ============================================

INSERT INTO reviews (user_id, product_id, rating, title, comment, status, verified_purchase) VALUES
(1, 1, 5, 'Amazing phone!', 'The iPhone 14 Pro exceeds expectations. Battery life is great and camera is incredible.', 'approved', 1),
(1, 2, 4, 'Great Android option', 'Samsung S23 Ultra is powerful. The S Pen is very useful for productivity.', 'approved', 1)
ON DUPLICATE KEY UPDATE user_id=VALUES(user_id);

-- ============================================
-- END OF SEED DATA
-- ============================================