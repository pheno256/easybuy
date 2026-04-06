<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$post_id = $_GET['id'] ?? 0;
$db = Database::getInstance();

$post = $db->query("
    SELECT p.*, c.name as category_name 
    FROM blog_posts p
    LEFT JOIN blog_categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.status = 'published'
", [$post_id])->fetch();

if(!$post) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

$page_title = $post['title'];

// Update view count
$db->query("UPDATE blog_posts SET views = views + 1 WHERE id = ?", [$post_id]);

// Get related posts
$related = $db->query("
    SELECT * FROM blog_posts 
    WHERE category_id = ? AND id != ? AND status = 'published'
    LIMIT 3
", [$post['category_id'], $post_id])->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <article>
                <h1 class="mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>
                
                <div class="text-muted mb-4">
                    <i class="far fa-calendar-alt"></i> <?php echo date('F d, Y', strtotime($post['created_at'])); ?>
                    <span class="mx-2">•</span>
                    <i class="far fa-eye"></i> <?php echo $post['views']; ?> views
                    <span class="mx-2">•</span>
                    <i class="far fa-folder"></i> <?php echo htmlspecialchars($post['category_name']); ?>
                </div>
                
                <?php if($post['featured_image']): ?>
                <img src="assets/images/blog/<?php echo $post['featured_image']; ?>" 
                     class="img-fluid rounded-4 mb-4" alt="<?php echo $post['title']; ?>">
                <?php endif; ?>
                
                <div class="blog-content">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-between">
                    <a href="blog.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Blog
                    </a>
                    <button class="btn btn-outline-secondary" onclick="sharePost()">
                        <i class="fas fa-share-alt"></i> Share
                    </button>
                </div>
            </article>
            
            <?php if(!empty($related)): ?>
            <div class="mt-5">
                <h4>Related Posts</h4>
                <div class="row g-4 mt-2">
                    <?php foreach($related as $rel): ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <?php if($rel['featured_image']): ?>
                            <img src="assets/images/blog/<?php echo $rel['featured_image']; ?>" 
                                 style="height: 150px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h6><?php echo htmlspecialchars($rel['title']); ?></h6>
                                <a href="blog-post.php?id=<?php echo $rel['id']; ?>" class="btn btn-sm btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function sharePost() {
    if(navigator.share) {
        navigator.share({
            title: '<?php echo addslashes($post['title']); ?>',
            text: 'Check out this article on EasyBuy Uganda',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        showNotification('Link copied to clipboard!', 'success');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>