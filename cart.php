<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Shopping Cart';
$db = Database::getInstance();

// Get cart items
if(isset($_SESSION['user_id'])) {
    $cart_items = $db->query("
        SELECT c.*, p.name, p.price, p.discount_price, p.image, p.stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?
    ", [$_SESSION['user_id']])->fetchAll();
} else {
    // Handle guest cart using session
    $cart_items = [];
    if(isset($_SESSION['guest_cart'])) {
        foreach($_SESSION['guest_cart'] as $item) {
            $product = $db->query("SELECT * FROM products WHERE id = ?", [$item['product_id']])->fetch();
            if($product) {
                $cart_items[] = [
                    'id' => $item['id'],
                    'product_id' => $product['id'],
                    'quantity' => $item['quantity'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'discount_price' => $product['discount_price'],
                    'image' => $product['image'],
                    'stock' => $product['stock']
                ];
            }
        }
    }
}

$total = 0;
foreach($cart_items as $item) {
    $price = $item['discount_price'] ?: $item['price'];
    $total += $price * $item['quantity'];
}

$delivery_fee = $total > 200000 ? 0 : 15000;
$grand_total = $total + $delivery_fee;

require_once 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Shopping Cart</h1>
    
    <?php if(empty($cart_items)): ?>
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
        <h3>Your cart is empty</h3>
        <p class="text-muted">Looks like you haven't added any items to your cart yet.</p>
        <a href="shop.php" class="btn btn-primary btn-lg">Start Shopping</a>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($cart_items as $item): 
                                    $price = $item['discount_price'] ?: $item['price'];
                                    $subtotal = $price * $item['quantity'];
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/products/<?php echo $item['image']; ?>" 
                                                 alt="<?php echo $item['name']; ?>"
                                                 style="width: 60px; height: 60px; object-fit: cover;"
                                                 class="rounded me-3">
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                <small class="text-muted">In Stock</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>UGX <?php echo number_format($price); ?></td>
                                    <td>
                                        <div class="input-group" style="width: 120px;">
                                            <button class="btn btn-sm btn-outline-secondary update-qty" 
                                                    data-cart-id="<?php echo $item['id']; ?>" data-change="-1">-</button>
                                            <input type="text" class="form-control form-control-sm text-center cart-qty" 
                                                   value="<?php echo $item['quantity']; ?>" readonly>
                                            <button class="btn btn-sm btn-outline-secondary update-qty" 
                                                    data-cart-id="<?php echo $item['id']; ?>" data-change="1">+</button>
                                        </div>
                                    </td>
                                    <td>UGX <?php echo number_format($subtotal); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger remove-item" data-cart-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-3">
                        <a href="shop.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                        <button class="btn btn-outline-danger" id="clear-cart">
                            <i class="fas fa-trash"></i> Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>UGX <?php echo number_format($total); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee</span>
                        <span>UGX <?php echo number_format($delivery_fee); ?></span>
                    </div>
                    
                    <?php if($total > 200000): ?>
                    <div class="alert alert-success py-1">
                        <small><i class="fas fa-gift"></i> Free delivery applied!</small>
                    </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total</strong>
                        <strong class="text-primary h5">UGX <?php echo number_format($grand_total); ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Promo Code</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="coupon-code" placeholder="Enter code">
                            <button class="btn btn-outline-primary" id="apply-coupon">Apply</button>
                        </div>
                    </div>
                    
                    <a href="checkout.php" class="btn btn-primary btn-lg w-100">
                        Proceed to Checkout <i class="fas fa-arrow-right"></i>
                    </a>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-lock"></i> Secure payment via MTN & Airtel Money
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Update quantity
document.querySelectorAll('.update-qty').forEach(button => {
    button.addEventListener('click', function() {
        const cartId = this.dataset.cartId;
        const change = parseInt(this.dataset.change);
        const qtyInput = this.parentElement.querySelector('.cart-qty');
        let newQty = parseInt(qtyInput.value) + change;
        
        if(newQty < 1) newQty = 1;
        
        updateCartQuantity(cartId, newQty);
    });
});

function updateCartQuantity(cartId, quantity) {
    showLoading();
    
    $.ajax({
        url: '/api/cart.php',
        method: 'POST',
        data: {
            action: 'update',
            cart_id: cartId,
            quantity: quantity
        },
        success: function(response) {
            hideLoading();
            if(response.success) {
                location.reload();
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            showNotification('Error updating cart', 'error');
        }
    });
}

// Remove item
document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', function() {
        const cartId = this.dataset.cartId;
        
        confirmDelete('Remove this item from cart?', function() {
            showLoading();
            
            $.ajax({
                url: '/api/cart.php',
                method: 'POST',
                data: {
                    action: 'remove',
                    cart_id: cartId
                },
                success: function(response) {
                    hideLoading();
                    if(response.success) {
                        showNotification('Item removed', 'success');
                        location.reload();
                    }
                }
            });
        });
    });
});

// Clear cart
document.getElementById('clear-cart')?.addEventListener('click', function() {
    confirmDelete('Clear your entire cart?', function() {
        showLoading();
        
        $.ajax({
            url: '/api/cart.php',
            method: 'POST',
            data: {
                action: 'clear'
            },
            success: function(response) {
                hideLoading();
                if(response.success) {
                    showNotification('Cart cleared', 'success');
                    location.reload();
                }
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>