<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$product_id = $_GET['id'] ?? 0;
$db = Database::getInstance();

$product = $db->query("
    SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ? AND p.status = 'active'
", [$product_id])->fetch();

if(!$product) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

$page_title = $product['name'];

// Update view count
$db->query("UPDATE products SET views = views + 1 WHERE id = ?", [$product_id]);

// Get related products
$related = $db->query("
    SELECT * FROM products 
    WHERE category_id = ? AND id != ? AND status = 'active' 
    LIMIT 4
", [$product['category_id'], $product_id])->fetchAll();

// Check if in wishlist
$in_wishlist = false;
if(isset($_SESSION['user_id'])) {
    $check = $db->query("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?", 
                        [$_SESSION['user_id'], $product_id])->fetch();
    $in_wishlist = !empty($check);
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
            <li class="breadcrumb-item"><a href="shop.php?category=<?php echo $product['category_slug']; ?>">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6 mb-4">
            <div class="position-relative">
                <img src="assets/images/products/<?php echo $product['image']; ?>" 
                     id="main-image"
                     class="img-fluid rounded-4 shadow-sm" 
                     alt="<?php echo $product['name']; ?>"
                     style="width: 100%; height: auto;">
                <?php if($product['discount_price']): ?>
                <span class="badge bg-danger position-absolute top-0 end-0 m-3 fs-6">
                    -<?php echo round((1 - $product['discount_price']/$product['price']) * 100); ?>%
                </span>
                <?php endif; ?>
            </div>
            
            <?php 
            $images = $product['images'] ? json_decode($product['images'], true) : [];
            if(!empty($images)): ?>
            <div class="row mt-3 g-2">
                <div class="col-3">
                    <img src="assets/images/products/<?php echo $product['image']; ?>" 
                         class="img-fluid rounded cursor-pointer thumb-image"
                         onclick="changeImage(this.src)"
                         style="height: 80px; object-fit: cover;">
                </div>
                <?php foreach($images as $img): ?>
                <div class="col-3">
                    <img src="assets/images/products/<?php echo $img; ?>" 
                         class="img-fluid rounded cursor-pointer thumb-image"
                         onclick="changeImage(this.src)"
                         style="height: 80px; object-fit: cover;">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Product Info -->
        <div class="col-md-6">
            <h1 class="h2 mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="mb-3">
                <span class="text-warning">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </span>
                <span class="text-muted">(25 reviews)</span>
            </div>
            
            <div class="mb-4">
                <?php if($product['discount_price']): ?>
                <span class="display-6 text-danger fw-bold">UGX <?php echo number_format($product['discount_price']); ?></span>
                <span class="text-muted text-decoration-line-through ms-2">UGX <?php echo number_format($product['price']); ?></span>
                <span class="badge bg-success ms-2">Save UGX <?php echo number_format($product['price'] - $product['discount_price']); ?></span>
                <?php else: ?>
                <span class="display-6 text-primary fw-bold">UGX <?php echo number_format($product['price']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <h6>Availability:</h6>
                <?php if($product['stock'] > 0): ?>
                <span class="text-success">
                    <i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock']; ?> items)
                </span>
                <?php else: ?>
                <span class="text-danger">
                    <i class="fas fa-times-circle"></i> Out of Stock
                </span>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <h6>Description:</h6>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            
            <?php if($product['stock'] > 0): ?>
            <div class="row g-3 mb-4">
                <div class="col-4">
                    <label class="form-label">Quantity</label>
                    <div class="input-group">
                        <button class="btn btn-outline-secondary" type="button" id="decrement-qty">-</button>
                        <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                        <button class="btn btn-outline-secondary" type="button" id="increment-qty">+</button>
                    </div>
                </div>
                <div class="col-8">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-lg add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="d-flex gap-2 mb-4">
                <button class="btn btn-outline-danger" id="wishlist-btn" data-product-id="<?php echo $product['id']; ?>">
                    <i class="fas fa-heart <?php echo $in_wishlist ? 'text-danger' : ''; ?>"></i>
                    <?php echo $in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                </button>
                <button class="btn btn-outline-secondary" onclick="shareProduct()">
                    <i class="fas fa-share-alt"></i> Share
                </button>
            </div>
            
            <hr>
            
            <div class="row text-center">
                <div class="col-4">
                    <i class="fas fa-truck fa-2x text-primary mb-2"></i>
                    <p class="small">Free Delivery<br>on orders over UGX 200k</p>
                </div>
                <div class="col-4">
                    <i class="fas fa-undo-alt fa-2x text-primary mb-2"></i>
                    <p class="small">7-Day Returns<br>Money back guarantee</p>
                </div>
                <div class="col-4">
                    <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                    <p class="small">Secure Payment<br>MTN & Airtel Money</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if(!empty($related)): ?>
    <div class="mt-5">
        <h3 class="mb-4">Related Products</h3>
        <div class="row g-4">
            <?php foreach($related as $rel): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <img src="assets/images/products/<?php echo $rel['image']; ?>" 
                         class="card-img-top" alt="<?php echo $rel['name']; ?>"
                         style="height: 150px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($rel['name']); ?></h6>
                        <div class="text-primary fw-bold">UGX <?php echo number_format($rel['discount_price'] ?: $rel['price']); ?></div>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <a href="product.php?id=<?php echo $rel['id']; ?>" class="btn btn-sm btn-outline-primary w-100">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function changeImage(src) {
    document.getElementById('main-image').src = src;
}

// Quantity controls
document.getElementById('decrement-qty').addEventListener('click', () => {
    let qty = document.getElementById('quantity');
    let value = parseInt(qty.value);
    if(value > 1) {
        qty.value = value - 1;
    }
});

document.getElementById('increment-qty').addEventListener('click', () => {
    let qty = document.getElementById('quantity');
    let value = parseInt(qty.value);
    let max = parseInt(qty.getAttribute('max'));
    if(value < max) {
        qty.value = value + 1;
    }
});

// Wishlist toggle
document.getElementById('wishlist-btn').addEventListener('click', function() {
    const productId = this.dataset.productId;
    
    $.ajax({
        url: '/api/wishlist.php',
        method: 'POST',
        data: {
            action: 'toggle',
            product_id: productId
        },
        success: function(response) {
            if(response.success) {
                const icon = document.querySelector('#wishlist-btn i');
                if(response.added) {
                    icon.classList.add('text-danger');
                    document.querySelector('#wishlist-btn').innerHTML = '<i class="fas fa-heart text-danger"></i> Remove from Wishlist';
                    showNotification('Added to wishlist', 'success');
                } else {
                    icon.classList.remove('text-danger');
                    document.querySelector('#wishlist-btn').innerHTML = '<i class="fas fa-heart"></i> Add to Wishlist';
                    showNotification('Removed from wishlist', 'success');
                }
            } else if(response.require_login) {
                showNotification('Please login first', 'error');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1500);
            }
        }
    });
});

function shareProduct() {
    if(navigator.share) {
        navigator.share({
            title: '<?php echo addslashes($product['name']); ?>',
            text: 'Check out this product on EasyBuy Uganda',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        showNotification('Link copied to clipboard!', 'success');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>