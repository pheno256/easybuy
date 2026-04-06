<?php
require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_number = $_GET['order'] ?? '';
$db = Database::getInstance();

$order = $db->query("
    SELECT * FROM orders 
    WHERE order_number = ? AND user_id = ?
", [$order_number, $_SESSION['user_id']])->fetch();

if(!$order) {
    header('Location: account.php');
    exit;
}

$order_items = $db->query("
    SELECT * FROM order_items 
    WHERE order_id = ?
", [$order['id']])->fetchAll();

$page_title = 'Order Confirmation';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
        <h1>Thank You for Your Order!</h1>
        <p class="lead">Your order has been placed successfully.</p>
        <p class="text-muted">Order #: <strong><?php echo $order['order_number']; ?></strong></p>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Shipping Information</h6>
                            <p class="mb-1"><?php echo htmlspecialchars($order['full_name']); ?></p>
                            <p class="mb-1"><?php echo htmlspecialchars($order['email']); ?></p>
                            <p class="mb-1"><?php echo htmlspecialchars($order['phone']); ?></p>
                            <p class="mb-0">
                                <?php echo htmlspecialchars($order['street_address']); ?><br>
                                <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['district']); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p class="mb-1">Order Date: <?php echo date('F d, Y', strtotime($order['created_at'])); ?></p>
                            <p class="mb-1">Payment Method: <?php echo strtoupper($order['payment_method']); ?> Money</p>
                            <p class="mb-1">
                                Payment Status: 
                                <span class="badge bg-<?php echo $order['payment_status'] == 'completed' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </p>
                            <p class="mb-0">
                                Order Status: 
                                <span class="badge bg-<?php 
                                    echo $order['order_status'] == 'delivered' ? 'success' : 
                                        ($order['order_status'] == 'cancelled' ? 'danger' : 'info'); 
                                ?>">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <h6>Items Ordered</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($order_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>UGX <?php echo number_format($item['price']); ?></td>
                                    <td>UGX <?php echo number_format($item['price'] * $item['quantity']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td>UGX <?php echo number_format($order['total_amount'] - 15000); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Delivery Fee:</strong></td>
                                    <td>UGX <?php echo number_format($order['total_amount'] > 200000 ? 0 : 15000); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong class="text-primary">UGX <?php echo number_format($order['total_amount']); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-truck"></i> 
                        <strong>Estimated Delivery:</strong> 3-5 business days within Uganda
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                        <a href="account.php" class="btn btn-outline-primary">
                            <i class="fas fa-user"></i> My Account
                        </a>
                        <a href="track-order.php?order=<?php echo $order['order_number']; ?>" class="btn btn-info">
                            <i class="fas fa-truck"></i> Track Order
                        </a>
                        <a href="shop.php" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>