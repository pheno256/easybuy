<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Checkout';
$db = Database::getInstance();

// Get cart items
$cart_items = $db->query("
    SELECT c.*, p.name, p.price, p.discount_price, p.image 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
", [$_SESSION['user_id']])->fetchAll();

$total = 0;
foreach($cart_items as $item) {
    $price = $item['discount_price'] ?: $item['price'];
    $total += $price * $item['quantity'];
}

// Get user address
$user = $db->query("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']])->fetch();

// Get Uganda districts
$districts = $db->query("SELECT * FROM uganda_districts ORDER BY district_name")->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Checkout</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Shipping Information</h5>
                    <form id="checkout-form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">District</label>
                                <select class="form-select" name="district" required>
                                    <option value="">Select District</option>
                                    <?php foreach($districts as $district): ?>
                                    <option value="<?php echo $district['district_name']; ?>"
                                        <?php echo $user['district'] == $district['district_name'] ? 'selected' : ''; ?>>
                                        <?php echo $district['district_name']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City/Town</label>
                                <input type="text" class="form-control" name="city" 
                                       value="<?php echo htmlspecialchars($user['city']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Street Address</label>
                                <input type="text" class="form-control" name="street_address" 
                                       value="<?php echo htmlspecialchars($user['street_address']); ?>" required>
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">Payment Method</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="payment-method card border-2" data-method="mtn">
                                    <div class="card-body text-center">
                                        <input type="radio" name="payment_method" value="mtn" id="mtn" class="d-none" required>
                                        <label for="mtn" class="cursor-pointer d-block">
                                            <img src="assets/images/mtn-momo.png" alt="MTN Mobile Money" height="50">
                                            <h6 class="mt-2">MTN Mobile Money</h6>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="payment-method card border-2" data-method="airtel">
                                    <div class="card-body text-center">
                                        <input type="radio" name="payment_method" value="airtel" id="airtel" class="d-none" required>
                                        <label for="airtel" class="cursor-pointer d-block">
                                            <img src="assets/images/airtel-money.png" alt="Airtel Money" height="50">
                                            <h6 class="mt-2">Airtel Money</h6>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="mobile-money-field" class="mt-3" style="display: none;">
                            <label class="form-label">Mobile Money Number</label>
                            <input type="tel" class="form-control" name="phone_number" 
                                   placeholder="Enter your mobile money number">
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">
                            Place Order & Pay
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Order Summary</h5>
                    <?php foreach($cart_items as $item): 
                        $price = $item['discount_price'] ?: $item['price'];
                        $item_total = $price * $item['quantity'];
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?php echo htmlspecialchars($item['name']); ?> x<?php echo $item['quantity']; ?></span>
                        <span>UGX <?php echo number_format($item_total); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal</strong>
                        <strong>UGX <?php echo number_format($total); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee</span>
                        <span>UGX <?php echo number_format($total > 200000 ? 0 : 15000); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5>Total</h5>
                        <h5 class="text-primary">UGX <?php echo number_format($total + ($total > 200000 ? 0 : 15000)); ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-method {
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method:hover {
    border-color: #2563eb !important;
}

.payment-method.selected {
    border-color: #2563eb !important;
    background: #f0f9ff;
}

.cursor-pointer {
    cursor: pointer;
}
</style>

<script>
$(document).ready(function() {
    // Payment method selection
    $('.payment-method').click(function() {
        $('.payment-method').removeClass('selected');
        $(this).addClass('selected');
        var method = $(this).data('method');
        $(`input[name="payment_method"][value="${method}"]`).prop('checked', true);
        
        if(method === 'mtn' || method === 'airtel') {
            $('#mobile-money-field').slideDown();
        }
    });
    
    // Form submission
    $('#checkout-form').submit(function(e) {
        e.preventDefault();
        
        if(!$('input[name="payment_method"]:checked').val()) {
            showNotification('Please select a payment method', 'error');
            return;
        }
        
        showLoading();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '/api/checkout.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                if(response.success) {
                    if(response.payment_url) {
                        // Redirect to payment page or show payment modal
                        window.location.href = response.payment_url;
                    } else {
                        showNotification('Order placed successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = 'order-confirmation.php?order=' + response.order_number;
                        }, 2000);
                    }
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                showNotification('Error processing order', 'error');
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>