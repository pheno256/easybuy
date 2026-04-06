<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'Track Order';
$db = Database::getInstance();
$order = null;
$error = '';

$order_number = $_GET['order'] ?? '';

if(!empty($order_number)) {
    $order = $db->query("
        SELECT * FROM orders 
        WHERE order_number = ?
    ", [$order_number])->fetch();
    
    if(!$order) {
        $error = "Order not found";
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_number = $_POST['order_number'];
    $email = $_POST['email'];
    
    $order = $db->query("
        SELECT * FROM orders 
        WHERE order_number = ? AND email = ?
    ", [$order_number, $email])->fetch();
    
    if($order) {
        header("Location: track-order.php?order=" . urlencode($order_number));
        exit;
    } else {
        $error = "Order not found. Please check your order number and email.";
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="text-center mb-5">Track Your Order</h1>
    
    <?php if(!$order): ?>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">
                        Enter your order number and email address to track your order.
                    </p>
                    
                    <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Order Number</label>
                            <input type="text" class="form-control" name="order_number" required 
                                   placeholder="e.g., ORD-ABC123" value="<?php echo htmlspecialchars($order_number); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Track Order
                        </button>
                    </form>
                    
                    <hr>
                    <div class="text-center">
                        <p class="mb-0">Don't have an order number? <a href="account.php">Check your orders</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Order #<?php echo $order['order_number']; ?></h5>
                    
                    <!-- Order Status Timeline -->
                    <div class="tracking-timeline mb-4">
                        <div class="step <?php echo in_array($order['order_status'], ['pending', 'processing', 'shipped', 'delivered']) ? 'completed' : ''; ?>">
                            <div class="circle">1</div>
                            <div class="label">Order Placed</div>
                            <div class="date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                        </div>
                        <div class="step <?php echo in_array($order['order_status'], ['processing', 'shipped', 'delivered']) ? 'completed' : ($order['order_status'] == 'pending' ? 'active' : ''); ?>">
                            <div class="circle">2</div>
                            <div class="label">Processing</div>
                            <div class="date"><?php echo $order['order_status'] != 'pending' ? date('M d, Y', strtotime($order['updated_at'])) : 'Pending'; ?></div>
                        </div>
                        <div class="step <?php echo in_array($order['order_status'], ['shipped', 'delivered']) ? 'completed' : ($order['order_status'] == 'processing' ? 'active' : ''); ?>">
                            <div class="circle">3</div>
                            <div class="label">Shipped</div>
                            <div class="date"><?php echo $order['order_status'] == 'shipped' || $order['order_status'] == 'delivered' ? date('M d, Y', strtotime($order['updated_at'])) : 'Not yet'; ?></div>
                        </div>
                        <div class="step <?php echo $order['order_status'] == 'delivered' ? 'completed' : ($order['order_status'] == 'shipped' ? 'active' : ''); ?>">
                            <div class="circle">4</div>
                            <div class="label">Delivered</div>
                            <div class="date"><?php echo $order['order_status'] == 'delivered' ? date('M d, Y', strtotime($order['updated_at'])) : 'Not yet'; ?></div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Order Status:</strong> <?php echo ucfirst($order['order_status']); ?>
                        <?php if($order['order_status'] == 'shipped'): ?>
                        <br>Your order is on the way! Estimated delivery in 2-3 days.
                        <?php elseif($order['order_status'] == 'delivered'): ?>
                        <br>Your order has been delivered. Thank you for shopping with us!
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Shipping Address</h6>
                            <p>
                                <?php echo htmlspecialchars($order['full_name']); ?><br>
                                <?php echo htmlspecialchars($order['phone']); ?><br>
                                <?php echo htmlspecialchars($order['street_address']); ?><br>
                                <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['district']); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Order Summary</h6>
                            <p>
                                Total Amount: <strong>UGX <?php echo number_format($order['total_amount']); ?></strong><br>
                                Payment Method: <?php echo strtoupper($order['payment_method']); ?> Money<br>
                                Payment Status: 
                                <span class="badge bg-<?php echo $order['payment_status'] == 'completed' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="account.php" class="btn btn-outline-primary">Go to My Account</a>
                        <a href="contact.php" class="btn btn-outline-secondary">Need Help?</a>
                        <?php if($order['order_status'] == 'delivered'): ?>
                        <button class="btn btn-success" onclick="showNotification('Thank you for your feedback!', 'success')">
                            <i class="fas fa-star"></i> Rate Your Experience
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.tracking-timeline {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin: 30px 0;
}

.tracking-timeline:before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e0e0e0;
    z-index: 1;
}

.tracking-timeline .step {
    position: relative;
    text-align: center;
    flex: 1;
    z-index: 2;
}

.tracking-timeline .step .circle {
    width: 40px;
    height: 40px;
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-weight: bold;
    position: relative;
    z-index: 2;
}

.tracking-timeline .step.completed .circle {
    background: #10b981;
    border-color: #10b981;
    color: white;
}

.tracking-timeline .step.active .circle {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

.tracking-timeline .step .label {
    font-size: 14px;
    font-weight: 500;
}

.tracking-timeline .step .date {
    font-size: 12px;
    color: #6b7280;
}

@media (max-width: 768px) {
    .tracking-timeline .step .label {
        font-size: 10px;
    }
    .tracking-timeline .step .date {
        font-size: 9px;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>