<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

header('Content-Type: application/xml');

$db = Database::getInstance();
$base_url = APP_URL;

// Get all products
$products = $db->query("SELECT slug, updated_at FROM products WHERE status = 'active'")->fetchAll();

// Get all categories
$categories = $db->query("SELECT slug FROM categories")->fetchAll();

// Get all blog posts
$blog_posts = $db->query("SELECT slug, updated_at FROM blog_posts WHERE status = 'published'")->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Static Pages -->
    <url>
        <loc><?php echo $base_url; ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo $base_url; ?>/shop.php</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo $base_url; ?>/about.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?php echo $base_url; ?>/contact.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?php echo $base_url; ?>/blog.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <!-- Product Pages -->
    <?php foreach($products as $product): ?>
    <url>
        <loc><?php echo $base_url; ?>/product.php?slug=<?php echo $product['slug']; ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($product['updated_at'])); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>
    
    <!-- Category Pages -->
    <?php foreach($categories as $category): ?>
    <url>
        <loc><?php echo $base_url; ?>/shop.php?category=<?php echo $category['slug']; ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>
    
    <!-- Blog Pages -->
    <?php foreach($blog_posts as $post): ?>
    <url>
        <loc><?php echo $base_url; ?>/blog-post.php?slug=<?php echo $post['slug']; ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($post['updated_at'])); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>
</urlset>