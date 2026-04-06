<?php
session_start();
if(!isset($_SESSION['vendor_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$order_id = $_GET['id'] ?? 0;
$db = Database::getInstance();
$vendor_id = $_SESSION['vendor_id'];

$order = $db->query("
    SELECT o.* FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.id = ? AND p.vendor_id = ?
    GROUP BY o.id
", [$order_id, $vendor_id])->fetch();

if(!$order) {
    header('Location: orders.php');
    exit;
}

$order_items = $db->query("
    SELECT oi.*, p.name as product_name, p.image 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ? AND p.vendor_id = ?
", [$order_id, $vendor_id])->fetchAll();

$page_title = 'Order Details #' . $order['order_number'];

require_once 'includes/vendor-header.php';
?>

<div class="mb-4">
    <a href="orders.php" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Order #<?php echo $order['order_number']; ?></h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Customer Information</h6>
                <p>
                    <strong>Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?><br>
                    <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?><br>
                    <strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?>
                </p>
            </div>
            <div class="col-md-6">
                <h6>Shipping Address</h6>
                <p>
                    <?php echo htmlspecialchars($order['street_address']); ?><br>
                    <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['district']); ?>
                </p>
            </div>
        </div>
        
        <h6>Items in this Order</h6>
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
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if($item['image']): ?>
                                <img src="../assets/images/products/<?php echo $item['image']; ?>" 
                                     style="width: 40px; height: 40px; object-fit: cover;" class="rounded me-2">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </div>
                        </td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>UGX <?php echo number_format($item['price']); ?></td>
                        <td>UGX <?php echo number_format($item['price'] * $item['quantity']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <h6>Order Status</h6>
                <span class="badge bg-<?php 
                    echo $order['order_status'] == 'delivered' ? 'success' : 
                        ($order['order_status'] == 'cancelled' ? 'danger' : 'warning'); 
                ?> fs-6">
                    <?php echo ucfirst($order['order_status']); ?>
                </span>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/vendor-footer.php'; ?>