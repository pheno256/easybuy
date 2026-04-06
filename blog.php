<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Blog';
$db = Database::getInstance();

// Get blog posts
$posts = $db->query("
    SELECT * FROM blog_posts 
    WHERE status = 'published' 
    ORDER BY created_at DESC 
    LIMIT 12
")->fetchAll();

// Get categories
$categories = $db->query("
    SELECT * FROM blog_categories 
    WHERE status = 'active'
    ORDER BY name
")->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">Blog</h1>
            
            <?php if(empty($posts)): ?>
            <div class="text-center py-5">
                <i class="fas fa-newspaper fa-5x text-muted mb-4"></i>
                <h3>No posts yet</h3>
                <p>Check back soon for updates!</p>
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach($posts as $post): ?>
                <div class="col-md-6 mb-4">
                    <div class="card blog-card h-100 shadow-sm">
                        <?php if($post['featured_image']): ?>
                        <img src="assets/images/blog/<?php echo $post['featured_image']; ?>" 
                             class="card-img-top" alt="<?php echo $post['title']; ?>"
                             style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="text-muted small mb-2">
                                <i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($post['excerpt'] ?: $post['content'], 0, 120)); ?>...</p>
                            <a href="blog-post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <?php foreach($categories as $cat): ?>
                        <li class="mb-2">
                            <a href="blog.php?category=<?php echo $cat['slug']; ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($cat['name']); ?>
                                <span class="badge bg-secondary float-end"><?php echo $cat['post_count']; ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Newsletter</h5>
                </div>
                <div class="card-body">
                    <p>Subscribe to get the latest updates!</p>
                    <form id="blog-newsletter">
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Your email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>