<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$share_token = $_GET['token'] ?? '';
$db = Database::getInstance();

$shared_wishlist = $db->query("
    SELECT sw.*, u.full_name 
    FROM shared_wishlists sw
    JOIN users u ON sw.user_id = u.id
    WHERE sw.share_token = ? AND sw.expires_at > NOW()
", [$share_token])->fetch();

if(!$shared_wishlist) {
    header('Location: 404.php');
    exit;
}

$products = $db->query("
    SELECT p.* 
    FROM shared_wishlist_items swi
    JOIN products p ON swi.product_id = p.id
    WHERE swi.shared_wishlist_id = ?
", [$shared_wishlist['id']])->fetchAll();

$page_title = $shared_wishlist['full_name'] . "'s Wishlist";

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <i class="fas fa-gift fa-4x text-primary mb-3"></i>
        <h1><?php echo htmlspecialchars($shared_wishlist['full_name']); ?>'s Wishlist</h1>
        <?php if($shared_wishlist['message']): ?>
        <p class="lead">"<?php echo htmlspecialchars($shared_wishlist['message']); ?>"</p>
        <?php endif; ?>
    </div>
    
    <?php if(empty($products)): ?>
    <div class="text-center py-5">
        <i class="fas fa-heart-broken fa-5x text-muted mb-4"></i>
        <h3>No items in this wishlist</h3>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach($products as $product): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card product-card h-100 border-0 shadow-sm">
                <img src="assets/images/products/<?php echo $product['image']; ?>" 
                     class="card-img-top" alt="<?php echo $product['name']; ?>"
                     style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <div class="text-primary fw-bold">UGX <?php echo number_format($product['discount_price'] ?: $product['price']); ?></div>
                    <button class="btn btn-primary w-100 mt-3 add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>