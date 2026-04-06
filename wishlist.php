<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'My Wishlist';
$db = Database::getInstance();

// Handle remove from wishlist
if(isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $db->query("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?", 
               [$_SESSION['user_id'], $product_id]);
    header('Location: wishlist.php');
    exit;
}

// Handle clear wishlist
if(isset($_GET['clear'])) {
    $db->query("DELETE FROM wishlist WHERE user_id = ?", [$_SESSION['user_id']]);
    header('Location: wishlist.php');
    exit;
}

// Get wishlist items
$wishlist = $db->query("
    SELECT w.*, p.name, p.price, p.discount_price, p.image, p.stock, p.slug 
    FROM wishlist w 
    JOIN products p ON w.product_id = p.id 
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
", [$_SESSION['user_id']])->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">My Wishlist</h1>
        <?php if(!empty($wishlist)): ?>
        <a href="?clear=1" class="btn btn-outline-danger" onclick="return confirm('Clear your entire wishlist?')">
            <i class="fas fa-trash"></i> Clear All
        </a>
        <?php endif; ?>
    </div>
    
    <?php if(empty($wishlist)): ?>
    <div class="text-center py-5">
        <i class="fas fa-heart fa-5x text-muted mb-4"></i>
        <h3>Your wishlist is empty</h3>
        <p class="text-muted">Save your favorite items here!</p>
        <a href="shop.php" class="btn btn-primary btn-lg">Start Shopping</a>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach($wishlist as $item): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card product-card h-100 border-0 shadow-sm">
                <div class="position-relative">
                    <img src="assets/images/products/<?php echo $item['image']; ?>" 
                         class="card-img-top" alt="<?php echo $item['name']; ?>"
                         style="height: 250px; object-fit: cover;">
                    <a href="?remove=<?php echo $item['product_id']; ?>" 
                       class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                       onclick="return confirm('Remove from wishlist?')">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php if($item['discount_price']): ?>
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                        -<?php echo round((1 - $item['discount_price']/$item['price']) * 100); ?>%
                    </span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                    <div class="mb-2">
                        <?php if($item['discount_price']): ?>
                        <span class="text-muted text-decoration-line-through">UGX <?php echo number_format($item['price']); ?></span>
                        <span class="text-danger fw-bold ms-2">UGX <?php echo number_format($item['discount_price']); ?></span>
                        <?php else: ?>
                        <span class="text-primary fw-bold">UGX <?php echo number_format($item['price']); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if($item['stock'] > 0): ?>
                    <button class="btn btn-primary w-100 add-to-cart" data-product-id="<?php echo $item['product_id']; ?>">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                    <?php else: ?>
                    <button class="btn btn-secondary w-100" disabled>
                        <i class="fas fa-times-circle"></i> Out of Stock
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-0 text-center pb-3">
                    <a href="product.php?id=<?php echo $item['product_id']; ?>" class="text-decoration-none">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Share Wishlist -->
    <div class="text-center mt-5">
        <h5>Share your wishlist</h5>
        <div class="d-flex justify-content-center gap-3 mt-3">
            <button class="btn btn-outline-primary rounded-circle" style="width: 50px; height: 50px;" onclick="shareWishlist('facebook')">
                <i class="fab fa-facebook-f"></i>
            </button>
            <button class="btn btn-outline-primary rounded-circle" style="width: 50px; height: 50px;" onclick="shareWishlist('twitter')">
                <i class="fab fa-twitter"></i>
            </button>
            <button class="btn btn-outline-primary rounded-circle" style="width: 50px; height: 50px;" onclick="shareWishlist('whatsapp')">
                <i class="fab fa-whatsapp"></i>
            </button>
            <button class="btn btn-outline-primary rounded-circle" style="width: 50px; height: 50px;" onclick="shareWishlist('email')">
                <i class="fas fa-envelope"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function shareWishlist(platform) {
    const url = window.location.href;
    const text = 'Check out my wishlist on EasyBuy Uganda!';
    
    let shareUrl = '';
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`;
            break;
        case 'email':
            shareUrl = `mailto:?subject=My Wishlist&body=${encodeURIComponent(text + '\n\n' + url)}`;
            break;
    }
    
    if(shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>