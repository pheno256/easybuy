<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Flash Sales';
$db = Database::getInstance();

// Get active flash sales
$flash_sales = $db->query("
    SELECT * FROM flash_sales 
    WHERE status = 'active' 
    AND start_date <= NOW() 
    AND end_date >= NOW()
    ORDER BY end_date ASC
")->fetchAll();

// Get products on flash sale
$products = $db->query("
    SELECT p.*, fs.discount_percentage, fs.end_date 
    FROM products p
    JOIN flash_sale_products fsp ON p.id = fsp.product_id
    JOIN flash_sales fs ON fsp.flash_sale_id = fs.id
    WHERE fs.status = 'active' 
    AND fs.start_date <= NOW() 
    AND fs.end_date >= NOW()
    ORDER BY fs.end_date ASC
")->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4">🔥 Flash Sales</h1>
        <p class="lead">Limited time offers - Grab them before they're gone!</p>
    </div>
    
    <?php foreach($flash_sales as $sale): ?>
    <div class="card bg-danger text-white mb-5">
        <div class="card-body text-center py-4">
            <h2><?php echo htmlspecialchars($sale['title']); ?></h2>
            <p><?php echo htmlspecialchars($sale['description']); ?></p>
            <div class="countdown-timer" data-end="<?php echo $sale['end_date']; ?>">
                <div class="d-flex justify-content-center gap-4">
                    <div><span class="h2 hours">00</span><br>Hours</div>
                    <div><span class="h2 minutes">00</span><br>Minutes</div>
                    <div><span class="h2 seconds">00</span><br>Seconds</div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if(empty($products)): ?>
    <div class="text-center py-5">
        <i class="fas fa-clock fa-5x text-muted mb-4"></i>
        <h3>No Active Flash Sales</h3>
        <p>Check back soon for exciting deals!</p>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach($products as $product): 
            $sale_price = $product['price'] * (1 - $product['discount_percentage'] / 100);
        ?>
        <div class="col-md-6 col-lg-3">
            <div class="card product-card h-100 border-0 shadow-sm">
                <div class="position-relative">
                    <img src="assets/images/products/<?php echo $product['image']; ?>" 
                         class="card-img-top" alt="<?php echo $product['name']; ?>"
                         style="height: 200px; object-fit: cover;">
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                        -<?php echo $product['discount_percentage']; ?>%
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <div class="mb-2">
                        <span class="text-muted text-decoration-line-through">UGX <?php echo number_format($product['price']); ?></span>
                        <span class="text-danger fw-bold ms-2">UGX <?php echo number_format($sale_price); ?></span>
                    </div>
                    <button class="btn btn-primary w-100 add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function updateCountdowns() {
    document.querySelectorAll('.countdown-timer').forEach(timer => {
        const endDate = new Date(timer.dataset.end).getTime();
        const now = new Date().getTime();
        const distance = endDate - now;
        
        if(distance < 0) {
            location.reload();
            return;
        }
        
        const hours = Math.floor(distance / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        timer.querySelector('.hours').textContent = String(hours).padStart(2, '0');
        timer.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
        timer.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
    });
}

setInterval(updateCountdowns, 1000);
updateCountdowns();
</script>

<?php require_once 'includes/footer.php'; ?>