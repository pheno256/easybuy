<?php
session_start();
if(!isset($_SESSION['vendor_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/config.php';
require_once '../app/lib/Database.php';

$page_title = 'My Orders';
$db = Database::getInstance();
$vendor_id = $_SESSION['vendor_id'];

// Get vendor orders
$orders = $db->query("
    SELECT DISTINCT o.*, oi.product_id, oi.quantity, oi.price 
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE p.vendor_id = ?
    ORDER BY o.created_at DESC
", [$vendor_id])->fetchAll();

require_once 'includes/vendor-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">My Orders</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-shopping-cart fa-3x mb-3 d-block"></i>
                            No orders yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['order_number']; ?></td>
                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['product_name'] ?? 'Product'); ?></td>
                        <td><?php echo $order['quantity']; ?></td>
                        <td>UGX <?php echo number_format($order['price'] * $order['quantity']); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $order['order_status'] == 'delivered' ? 'success' : 
                                    ($order['order_status'] == 'cancelled' ? 'danger' : 'warning'); 
                            ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewOrder(<?php echo $order['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function viewOrder(id) {
    window.location.href = 'order-details.php?id=' + id;
}
</script>

<?php require_once 'includes/vendor-footer.php'; ?>