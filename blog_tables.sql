-- Blog categories table
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blog posts table
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category_id INT,
    author_id INT,
    views INT DEFAULT 0,
    status ENUM('draft', 'published') DEFAULT 'draft',
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert sample blog categories
INSERT INTO blog_categories (name, slug, description) VALUES
('Shopping Tips', 'shopping-tips', 'Tips and tricks for better shopping'),
('Product Reviews', 'product-reviews', 'Honest reviews of our products'),
('Company News', 'company-news', 'Updates from EasyBuy'),
('Guides', 'guides', 'How-to guides and tutorials');

-- Insert sample blog post
INSERT INTO blog_posts (title, slug, content, excerpt, category_id, status, published_at) VALUES
('How to Shop Safely Online in Uganda', 'how-to-shop-safely-online', 
'<p>Online shopping is convenient, but safety is important. Here are tips to shop safely...</p>',
'Learn essential tips for safe online shopping in Uganda', 1, 'published', NOW());