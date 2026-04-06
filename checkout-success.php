<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'] ?? 0;
$db = Database::getInstance();

$order = $db->query("
    SELECT * FROM orders 
    WHERE id = ? AND user_id = ?
", [$order_id, $_SESSION['user_id']])->fetch();

if(!$order) {
    header('Location: account.php');
    exit;
}

$page_title = 'Order Successful';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <div class="mb-4">
                <i class="fas fa-check-circle text-success fa-5x"></i>
            </div>
            <h1 class="mb-3">Thank You for Your Order!</h1>
            <p class="lead">Your order has been placed successfully.</p>
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5>Order #<?php echo $order['order_number']; ?></h5>
                    <p>Total Amount: <strong>UGX <?php echo number_format($order['total_amount']); ?></strong></p>
                    <p>Payment Method: <strong><?php echo strtoupper($order['payment_method']); ?> Money</strong></p>
                    <p>Payment Status: <span class="badge bg-success"><?php echo ucfirst($order['payment_status']); ?></span></p>
                    <hr>
                    <p>A confirmation email has been sent to your email address.</p>
                    <div class="mt-4">
                        <a href="track-order.php?order=<?php echo $order['order_number']; ?>" class="btn btn-info">
                            <i class="fas fa-truck"></i> Track Order
                        </a>
                        <a href="account.php" class="btn btn-primary">
                            <i class="fas fa-user"></i> My Account
                        </a>
                        <a href="shop.php" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-bag"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>