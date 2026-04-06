<?php
// Track recently viewed products
function trackRecentlyViewed($product_id) {
    if(!isset($_SESSION['recently_viewed'])) {
        $_SESSION['recently_viewed'] = [];
    }
    
    // Remove if already exists
    $_SESSION['recently_viewed'] = array_diff($_SESSION['recently_viewed'], [$product_id]);
    
    // Add to beginning
    array_unshift($_SESSION['recently_viewed'], $product_id);
    
    // Keep only last 5
    $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 5);
}

function getRecentlyViewed($db) {
    if(isset($_SESSION['recently_viewed']) && !empty($_SESSION['recently_viewed'])) {
        $placeholders = implode(',', array_fill(0, count($_SESSION['recently_viewed']), '?'));
        return $db->query("SELECT * FROM products WHERE id IN ($placeholders) AND status = 'active'", 
                         $_SESSION['recently_viewed'])->fetchAll();
    }
    return [];
}
?>

<!-- Recently Viewed Products Section -->
<?php 
$recent_products = getRecentlyViewed($db);
if(!empty($recent_products)): 
?>
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="text-center mb-4">Recently Viewed</h3>
        <div class="row g-4">
            <?php foreach($recent_products as $product): ?>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <img src="assets/images/products/<?php echo $product['image']; ?>" 
                         class="card-img-top" alt="<?php echo $product['name']; ?>"
                         style="height: 120px; object-fit: cover;">
                    <div class="card-body p-2">
                        <h6 class="card-title small"><?php echo htmlspecialchars(substr($product['name'], 0, 30)); ?></h6>
                        <div class="text-primary fw-bold small">UGX <?php echo number_format($product['discount_price'] ?: $product['price']); ?></div>
                    </div>
                    <div class="card-footer bg-white border-0 p-2">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary w-100">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>