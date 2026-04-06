<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Compare Products';
$db = Database::getInstance();

$product_ids = $_GET['ids'] ?? '';
$product_ids = explode(',', $product_ids);
$products = [];

if(!empty($product_ids) && $product_ids[0]) {
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $products = $db->query("SELECT * FROM products WHERE id IN ($placeholders) AND status = 'active'", $product_ids)->fetchAll();
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Compare Products</h1>
    
    <?php if(empty($products)): ?>
    <div class="text-center py-5">
        <i class="fas fa-chart-line fa-5x text-muted mb-4"></i>
        <h3>No products to compare</h3>
        <p class="text-muted">Add products to compare by clicking the compare button on product pages</p>
        <a href="shop.php" class="btn btn-primary">Start Shopping</a>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered compare-table">
            <thead>
                <tr>
                    <th style="width: 200px;">Features</th>
                    <?php foreach($products as $product): ?>
                    <th style="min-width: 250px;">
                        <div class="text-center">
                            <button class="btn btn-sm btn-danger float-end remove-compare" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-times"></i>
                            </button>
                            <img src="assets/images/products/<?php echo $product['image']; ?>" 
                                 style="height: 150px; object-fit: cover;" class="mb-3">
                            <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                        </div>
                    </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Price</strong></td>
                    <?php foreach($products as $product): ?>
                    <td class="text-center">
                        <?php if($product['discount_price']): ?>
                        <span class="text-muted text-decoration-line-through">UGX <?php echo number_format($product['price']); ?></span><br>
                        <span class="text-danger fw-bold fs-5">UGX <?php echo number_format($product['discount_price']); ?></span>
                        <?php else: ?>
                        <span class="text-primary fw-bold fs-5">UGX <?php echo number_format($product['price']); ?></span>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td><strong>Rating</strong></td>
                    <?php foreach($products as $product): ?>
                    <td class="text-center">
                        <div class="text-warning">
                            <?php 
                            $rating = $product['rating'] ?? 0;
                            for($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?php echo $i <= $rating ? '' : '-o'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <small class="text-muted">(<?php echo $product['rating'] ?? 0; ?>/5)</small>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td><strong>Stock Status</strong></td>
                    <?php foreach($products as $product): ?>
                    <td class="text-center">
                        <?php if($product['stock'] > 0): ?>
                        <span class="badge bg-success">In Stock (<?php echo $product['stock']; ?>)</span>
                        <?php else: ?>
                        <span class="badge bg-danger">Out of Stock</span>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td><strong>Description</strong></td>
                    <?php foreach($products as $product): ?>
                    <td><?php echo nl2br(htmlspecialchars(substr($product['description'], 0, 200))); ?>...</td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td><strong>Actions</strong></td>
                    <?php foreach($products as $product): ?>
                    <td class="text-center">
                        <?php if($product['stock'] > 0): ?>
                        <button class="btn btn-primary btn-sm w-100 mb-2 add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <?php endif; ?>
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm w-100">
                            View Details
                        </a>
                    </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="text-center mt-4">
        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
        <button class="btn btn-outline-danger" id="clear-compare">Clear All</button>
    </div>
    <?php endif; ?>
</div>

<style>
.compare-table th, .compare-table td {
    vertical-align: middle;
    padding: 15px;
}
.remove-compare {
    position: relative;
    float: right;
}
</style>

<script>
// Remove from compare
document.querySelectorAll('.remove-compare').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        let currentIds = getCompareIds();
        currentIds = currentIds.filter(id => id != productId);
        
        if(currentIds.length > 0) {
            window.location.href = 'compare.php?ids=' + currentIds.join(',');
        } else {
            window.location.href = 'compare.php';
        }
    });
});

// Clear all compare
document.getElementById('clear-compare')?.addEventListener('click', function() {
    localStorage.removeItem('compare_products');
    window.location.href = 'compare.php';
});

function getCompareIds() {
    const urlParams = new URLSearchParams(window.location.search);
    const ids = urlParams.get('ids');
    return ids ? ids.split(',') : [];
}
</script>

<?php require_once 'includes/footer.php'; ?>